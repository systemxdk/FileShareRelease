<?

class AdministratorsController extends shController {
	
	function __construct(array $arguments = array()){
		parent::__construct($arguments);
		UserAccessFilter::Verify(USER_ADMIN, true);
		$this->className = 'Administrators';
		$GLOBALS['_CONFIG']['draw_submenu'] = AdministratorsSubmenu::menu();
	}
	
	function index(){
		try {
			$list = new shListbox();
			$list->SetTable('user');
			$list->SetHeaderClass('td_border_top_bottom bold');
			$list->SetRowClass('td_border_bottom');
			$list->SetWhere('access_level != 2');
			$list->SetSelect(
				Array(
					'id' => Array('header' => Language::tag('TABLE_HEADER_ID'), 'attributes' => Array('width' => 45)),  
					'username' => Array('header' => Language::tag('TABLE_HEADER_USERNAME'), 'attributes' => Array('width' => 125)),
					'access_level' => Array('header' => Language::tag('TABLE_HEADER_ACCESS'), 'attributes' => Array('width' => 125), 'render' => Array('class' => 'AdministratorsController', 'method' => 'render_access')),
					'logins' => Array('header' => Language::tag('TABLE_HEADER_LOGINS'), 'attributes' => Array()),
					'last_login' => Array('header' => Language::tag('TABLE_HEADER_LAST_LOGIN'), 'attributes' => Array(), 'render' => Array('class' => 'Dateassistant', 'method' => 'render_date_dk')),
				)
			);
			$list->SetButton(
				Array(
					'image' 	=> '/images/edit.png',
					'width'		=> 15, 
					'height'	=> 15, 
					'caption' 	=> Language::tag('TABLE_HEADER_EDIT'),
					'href'		=> new shUrl('administration/administrators/edit?id={id}')
				)
			);
			$this->list = $list->Render();
		} catch (Exception $e) {
            $this->redirect(new shUrl('administration/network/index'), array('error' => $e->getMessage()));
		}
	}
	
    static function render_access($row, $value) {
        $access = "";
        foreach ($_SESSION["claims"] as $claim => $bit) {
            if ($bit & $value) {
                $access .= $claim . "<br />";
            }
        }
        return $access;
    }
    
	function add(){
		$user = NULL;
		if ($this->arguments->id) {
			$user = new User();
			$user->find($this->arguments->id);
		}
		$this->user = $user;
	}
	
	function edit(){
		$this->forward("add");	
	}
	
    function save(){
        try {
            $edit = property_exists($this->arguments, "id");
            
            $user = new User();
            if ($edit) {
                
                //Administrator being edited, so look up the record.
                $user->find($this->arguments->id);
                
                //Administrator could not be found by the posted id?
                if (!$user->username) throw new Exception(Language::tag("TEXT_ADMINISTRATOR_COULD_NOT_FIND_RECORD"));
                
            } else { //Add
                
                 //When adding administrator username and password are required
                if (!$this->arguments->username) throw new Exception(Language::tag("TEXT_ADMINISTRATOR_USERNAME_EMPTY"));
                if (!$this->arguments->password) throw new Exception(Language::tag("TEXT_ADMINISTRATOR_PASSWORD_EMPTY"));
                if (Userassistant::user_exist($this->arguments->username)) throw new Exception(Language::tag("TEXT_ADMINISTRATOR_USERNAME_EXIST"));
                
                $user->username = $this->arguments->username;
            }
            
            //Password handling
            if ($this->arguments->password) {
                list($salt, $hash) = Userassistant::generate_hash($this->arguments->password);
                
                $user->password = $hash;
                $user->salt = $salt;
            }
            
            $user->default_page = $this->arguments->default_page;
            $user->save();
            
            $this->redirect("index", array("success" => Language::tag("TEXT_ADMINISTRATOR_SAVED")));
        } catch (Exception $e) {
            $this->redirect("index", array("error" => $e->getMessage()));
        }
    }
    
	function useraccess(){
		try {
			$list = new shListbox();
			$list->SetTable('user_access');
			$list->SetHeaderClass('td_border_top_bottom bold');
			$list->SetRowClass('td_border_bottom');
			$list->SetSelect(
				Array(
					'id' => Array('header' => Language::tag('TABLE_HEADER_ID'), 'attributes' => Array('width' => 45)),  
					'description' => Array('header' => Language::tag('TABLE_HEADER_DESCRIPTION'), 'attributes' => Array('width' => 125)),
					'access_key' => Array('header' => Language::tag('TABLE_HEADER_ACCESS_KEY'), 'attributes' => Array()),
				)
			);
			$list->SetButton(
				Array(
					'image' 	=> '/images/edit.png',
					'width'		=> 15, 
					'height'	=> 15, 
					'caption' 	=> Language::tag('TABLE_HEADER_EDIT'),
					'href'		=> new shUrl('administration/administrators/useraccess_edit?id={id}')
				)
			);
			$this->list = $list->Render();
		} catch (Exception $e) {
			$this->redirect('/', array('error' => $e->getMessage()));
		}
	}
	
	function useraccess_add(){
		$useraccess = NULL;
		if ($this->arguments->id) {
			$useraccess = new Useraccess();
			$useraccess->find($this->arguments->id);
		}
		$this->useraccess = $useraccess;
	}
	
	function useraccess_edit(){
		$this->forward("useraccess_add");	
	}
	
	function useraccess_save(){
		try {
			$useraccess = new Useraccess();
			if ($this->arguments->id){
				$useraccess->find($this->arguments->id);
				$action = "update";
			} else {
				$currentuseraccess = new Useraccess();
				$maxuser = $currentuseraccess->find_by_sql("SELECT MAX(id) AS id FROM user_access");
				
				if ( !$maxuser[0]->id ) {
					$useraccess->id = 1;	
				} else {
					$maxuser = array_pop($maxuser);
					$useraccess->id = $maxuser->id * 2;
				}
				$useraccess->access_key = preg_replace('/[^A-Z_]/', '', strtoupper($this->arguments->access_key));
				$useraccess->created = 'now()';
				$action = "create";
			}
			$useraccess->description = $this->arguments->description;
			$useraccess->$action();
			$this->redirect("useraccess", array("success" => Language::tag("TEXT_USERACCESS_SAVED")));
		} catch ( Exception $e ) {
			$this->redirect("useraccess", array("error" => $e->getMessage()));
		}
	}
    
}
