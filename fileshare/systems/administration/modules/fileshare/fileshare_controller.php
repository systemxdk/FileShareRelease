<?

class FileshareController extends shController {
	
	function __construct(array $arguments = array()){
		parent::__construct($arguments);
		$this->className = 'Fileshare';
		UserAccessFilter::Verify(USER_ADMIN, true);
		$GLOBALS['_CONFIG']['draw_submenu'] = FileshareSubmenu::menu();
	}
	
	function index(){
		try {
			$list = new shListbox();
			$list->SetTable('fileshare');
            $list->SetOrderBy('user_id DESC');
			$list->SetHeaderClass('td_border_top_bottom bold');
			$list->SetRowClass('td_border_bottom');
			$list->SetSelect(
				Array(
					'id' => Array('header' => Language::tag('TABLE_HEADER_ID'), 'attributes' => Array('hidden' => true, 'width' => 125)),
					'user_id' => Array('header' => Language::tag('TABLE_HEADER_USERNAME'), 'attributes' => Array(), 'render' => Array('class' => 'Userassistant', 'method' => 'render_username')),
					'status' => Array('header' => Language::tag('TABLE_HEADER_STATUS'), 'attributes' => Array('width' => 150), 'render' => Array('class' => 'FileshareController', 'method' => 'render_status')),
					'active_days' => Array('header' => Language::tag('TABLE_HEADER_ACTIVE_DAYS'), 'attributes' => Array('width' => 150)),
					'expiration' => Array('header' => Language::tag('TABLE_HEADER_EXPIRATION'), 'attributes' => Array('width' => 125), 'render' => Array('class' => 'Dateassistant', 'method' => 'render_date_dk')),
					'updated' => Array('header' => Language::tag('TABLE_HEADER_UPDATED'), 'attributes' => Array('width' => 125), 'render' => Array('class' => 'Dateassistant', 'method' => 'render_date_dk')),
					'created' => Array('header' => Language::tag('TABLE_HEADER_CREATED'), 'attributes' => Array('width' => 125), 'render' => Array('class' => 'Dateassistant', 'method' => 'render_date_dk'))
				)
			);
			$list->SetButton(
				Array(
					'image' 	=> '/images/lock.png',
					'width'		=> 15, 
					'height'	=> 15, 
					'href'		=> new shUrl('administration/fileshare/changepassword?id={id}'),
					'caption' 	=> Language::tag('TABLE_HEADER_CHANGE_PASSWORD'),
                    'render'    => array("class" => "FileshareController", "method" => "render_button_change_password")
				)
			);
			$list->SetButton(
				Array(
					'image' 	=> '/images/stop.png',
					'width'		=> 15, 
					'height'	=> 15, 
					'caption' 	=> Language::tag('TABLE_HEADER_FILESHARE_DEACTIVATE'),
					'href'		=> new shUrl('administration/fileshare/delete?id={id}'),
                    'render'    => array("class" => "FileshareController", "method" => "render_button_delete")
				)
			);
            
			$this->list = $list->Render();
		} catch (Exception $e) {
            $this->redirect(new shUrl('administration/network/index'), array('error' => $e->getMessage()));
		}
	}
	
    static function render_button_delete($row) {
        return $row->status == FileshareAssistant::$STATUS_ACTIVE; //Only show the delete button if fileshare is active
    }
    
    static function render_button_change_password($row) {
        return $row->status == FileshareAssistant::$STATUS_ACTIVE; //Only show the change password button if fileshare is active
    }
    
    static function render_status($row, $value) {
        
        $class = "";
        switch ($value) {
            case FileshareAssistant::$STATUS_ACTIVE:
                $class = "status_active";
                break;
            case FileshareAssistant::$STATUS_INACTIVE:
                $class = "status_inactive";
                break;
            case FileshareAssistant::$STATUS_PENDING:
                $class = "status_pending";
                break;
            case FileshareAssistant::$STATUS_ERROR:
                $class = "status_error";
                break;
        }
        return '<span class="' . $class. '">' . Language::tag("TEXT_STATUS_" . $value) . '</span>';
    }
    
    function delete() {
        try {
            if (!$this->arguments->id) throw new Exception(Language::tag("ERROR_MISSING_ID"));
            
            $user = new User();
            $user->find($_SESSION["authorized"]); //Admin user performing the action
            
            $fileshare = new Fileshare();
            $fileshare->find($this->arguments->id);
            
            $filesharequeue = new Filesharequeue();
            $filesharequeue->fileshare_id = $fileshare->id;
            $filesharequeue->user_id = $fileshare->user_id;
            $filesharequeue->action = "DELETE";
            $filesharequeue->status = "PENDING";
            $filesharequeue->created = "now()";
            $filesharequeue->create();
            
            $fileshare->status = Fileshareassistant::$STATUS_PENDING;
            $fileshare->updated = "now()";
            $fileshare->updated_by = $user->username;
            $fileshare->update();
            
            $this->redirect("index", array("success" => Language::tag("TEXT_FILESHARE_DELETE_SUCCESS")));
            
        } catch (Exception $e) {
            $this->redirect(new shUrl('index'), array('error' => $e->getMessage()));
        }
    }
    
    function changepassword() {
        try {
            if (!$this->arguments->id) throw new Exception(Language::tag("ERROR_MISSING_ID"));
            
            $user = new User();
            $user->find($_SESSION["authorized"]); //Admin user performing the action
            
            $fileshare = new Fileshare();
            $fileshare->find($this->arguments->id);
            
            $filesharequeue = new Filesharequeue();
            $filesharequeue->fileshare_id = $fileshare->id;
            $filesharequeue->user_id = $fileshare->user_id;
            $filesharequeue->action = "CHANGE_PASSWORD";
            $filesharequeue->status = "PENDING";
            $filesharequeue->created = "now()";
            $filesharequeue->create();
            
            $fileshare->status = Fileshareassistant::$STATUS_PENDING;
            $fileshare->updated = "now()";
            $fileshare->updated_by = $user->username;
            $fileshare->update();
            
            $this->redirect("index", array("success" => Language::tag("TEXT_FILESHARE_CHANGE_PASSWORD_SUCCESS")));
            
        } catch (Exception $e) {
            $this->redirect(new shUrl('index'), array('error' => $e->getMessage()));
        }
    }
    
    function add() {
        $model = new Fileshare();
        $fileshares = $model->find_all();
        
        $users_with_accounts = array();
        
        foreach ($fileshares as $fs) {
            if (!in_array($fs->user_id, $users_with_accounts)) $users_with_accounts[] = $fs->user_id;
        }
        
        $this->users_with_accounts = $users_with_accounts; //Register to view.
        $this->default_expiration_days = SettingAssistant::get("default_expiration_days");
        
        $user = new User();
        $this->users = $user->find_all("access_level = 2");
        
    }
    
    function save() {
        try {
            if (!property_exists($this->arguments, "user_id") || !$this->arguments->user_id) throw new Exception(Language::tag("ERROR_FILESHARE_NO_USER_SELECTED"));
            
            $fileshare = new Fileshare();
            $existing = $fileshare->find_all("user_id = " . $this->arguments->user_id);
            
            if (count($existing)) throw new Exception(Language::tag("ERROR_FILESHARE_ALREADY_CREATED"));
            
            $user = new User();
            $user->find($_SESSION["authorized"]);
            
            $fileshare = new Fileshare();
            $fileshare->user_id = $this->arguments->user_id;
            $fileshare->status = FileshareAssistant::$STATUS_PENDING;
            $fileshare->active_days = $this->arguments->active_days;
            $fileshare->expiration = date("Y-m-d H:i:s", strtotime("+" . $this->arguments->active_days . " DAYS"));
            $fileshare->created = "now()";
            $fileshare->created_by = $user->username;
            $fileshare->create();
            
            FileshareAssistant::queue($fileshare->id, $fileshare->user_id, "CREATE", FileshareAssistant::$STATUS_PENDING);
            
            $this->redirect("index", array("success" => Language::tag("TEXT_FILESHARE_CREATED")));
            
        } catch (Exception $e) {
            $this->redirect(new shUrl('index'), array('error' => $e->getMessage()));
        }
    }
    
}
