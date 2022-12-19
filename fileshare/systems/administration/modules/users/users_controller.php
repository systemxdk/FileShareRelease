<?

class UsersController extends shController {
	
	function __construct(array $arguments = array()){
		parent::__construct($arguments);
		
		$this->className = 'Users';
        
        UserAccessFilter::Verify(USER_ADMIN, true);
		$GLOBALS['_CONFIG']['draw_submenu'] = UsersSubmenu::menu();
	}
	
	function index(){
		try {
			$list = new shListbox();
			$list->SetTable('user');
            $list->SetOrderBy('username DESC');
			$list->SetHeaderClass('td_border_top_bottom bold');
			$list->SetRowClass('td_border_bottom');
            $list->SetWhere("access_level = " . USER_REGULAR);
			$list->SetSelect(
				Array(
					'id' => Array('header' => Language::tag('TABLE_HEADER_ID'), 'attributes' => Array('hidden' => true, 'width' => 125)),
					'username' => Array('header' => Language::tag('TABLE_HEADER_USERNAME'), 'attributes' => Array('width' => 125)),
					'firstname' => Array('header' => Language::tag('TABLE_HEADER_FIRSTNAME'), 'attributes' => Array('width' => 150)),
					'lastname' => Array('header' => Language::tag('TABLE_HEADER_LASTNAME'), 'attributes' => Array('width' => 150)),
					'logins' => Array('header' => Language::tag('TABLE_HEADER_LOGINS'), 'attributes' => Array('width' => 125)),
					'email' => Array('header' => Language::tag('TABLE_HEADER_EMAIL'), 'attributes' => Array()),
					'created' => Array('header' => Language::tag('TABLE_HEADER_CREATED'), 'attributes' => Array('width' => 125), 'render' => Array('class' => 'Dateassistant', 'method' => 'render_date_dk')),
					'last_login' => Array('header' => Language::tag('TABLE_HEADER_LAST_LOGIN'), 'attributes' => Array('width' => 125)),
				)
			);
			$list->SetButton(
				Array(
					'image' 	=> '/images/email.png',
					'width'		=> 15, 
					'height'	=> 15, 
					'caption' 	=> Language::tag('TABLE_HEADER_EMAIL'),
					'href'		=> new shUrl('administration/users/email?user_id={id}')
				)
			);
			$list->SetButton(
				Array(
					'image' 	=> '/images/edit.png',
					'width'		=> 15, 
					'height'	=> 15, 
					'caption' 	=> Language::tag('TABLE_HEADER_EDIT'),
					'href'		=> new shUrl('administration/users/edit?id={id}')
				)
			);
			$this->list = $list->Render();
		} catch (Exception $e) {
            $this->redirect(new shUrl('administration/network/index'), array('error' => $e->getMessage()));
		}
	}
	
    function email(){
        try {
            if (!$this->arguments->user_id) throw new Exception(Language::tag("ERROR_MISSING_ID"));
            
            $user = new User();
            $user->find($this->arguments->user_id);
            
            if ($user->id === null) throw new Exception(Language::tag("ERROR_COULD_NOT_FIND_RECORD"));
            
            $this->user = $user;
        } catch (Exception $e) {
            $this->redirect("index", array("error" => $e->getMessage()));
        }
    }
    
    function email_send(){
        try {
            if (!$this->arguments->user_id) throw new Exception(Language::tag("ERROR_MISSING_ID"));
            
            $user = new User();
            $user->find($this->arguments->user_id);
            
            if ($user->id === null) throw new Exception(Language::tag("ERROR_COULD_NOT_FIND_RECORD"));
            
            shEmail::send_smtp(array($user->email), $GLOBALS['_CONFIG']['SETTING']['outgoing_email_name'], $GLOBALS['_CONFIG']['SETTING']['outgoing_email'], $this->arguments->subject, $this->arguments->body);
            
            $this->redirect("index", array("success" => Language::tag("TEXT_EMAIL_SENT")));
        } catch (Exception $e) {
            $this->redirect("index", array("error" => $e->getMessage()));
        }
    }
    
    function add(){}
    
    function edit() {
        try {
            if (!$this->arguments->id) throw new Exception(Language::tag("ERROR_MISSING_ID"));
            
            $user = new User();
            $user->find($this->arguments->id);
            
            if ($user->id === null) throw new Exception(Language::tag("ERROR_COULD_NOT_FIND_RECORD"));
            
            $this->user = $user;
            
        } catch (Exception $e) {
            $this->redirect("index", array("error" => $e->getMessage()));
        }
    }
    
    function save_edit() {
        try {
            if (!$this->arguments->id) throw new Exception("ERROR_MISSING_ID");
            if (!$this->arguments->email) throw new Exception("ERROR_MISSING_EMAIL");
            
            $user = new User();
            $user->find($this->arguments->id);
            
            if ($user->id === null) throw new Exception(Language::tag("ERROR_COULD_NOT_FIND_RECORD"));
            
            if ($this->arguments->password) { //Password needs to be updated.
                list($salt, $hash) = Userassistant::generate_hash($this->arguments->password);
                
                $user->salt = $salt;
                $user->password = $hash;
            }
            
            //Always always always validate email.
            //We need email to send fileshare connection details to customers
            if (!filter_var($this->arguments->email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception(Language::tag("ERROR_INVALID_EMAIL"));
            }
            
            $user->firstname = $this->arguments->firstname;
            $user->lastname = $this->arguments->lastname;
            $user->email = $this->arguments->email;
            $user->update();
            
            $this->redirect("index", array("success" => sprintf(Language::tag("TEXT_ACCOUNT_SAVED"), $user->username)));
        } catch (Exception $e) {
            $this->redirect("index", array("error" => $e->getMessage()));
        }
    }
    
    function save(){
        try {
            if (!$this->arguments->password) throw new Exception("ERROR_ACCOUNT_PASSWORD_MISSING");
            
            $user = new User();
            $user->username = AccountAssistant::get_next_username();
            $user->access_level = USER_REGULAR;
            $user->default_page = "administration/account/index";
            
            list($salt, $hash) = Userassistant::generate_hash($this->arguments->password);
            
            $user->salt = $salt;
            $user->password = $hash;
            $user->created = "now()";
            
            $user->create();
            
            $this->redirect("index", array("success" => sprintf(Language::tag("TEXT_ACCOUNT_SAVED"), $user->username)));
        } catch (Exception $e) {
            $this->redirect("index", array("error" => $e->getMessage()));
        }
    }
}
