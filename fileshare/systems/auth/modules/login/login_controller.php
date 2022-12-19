<?php

class LoginController extends shController {
	
	function __construct(array $arguments = array()){
		parent::__construct($arguments);
		
		$this->className = 'Login';

		$GLOBALS['_CONFIG']['LAYOUT'] = "login.php";
	}
	
	function index(){
        if (Userassistant::is_logged_in()){
            $user = new User();
            $user->find($_SESSION["authorized"]);
            
            $this->redirect($user->default_page);
        }
        
        $this->languages = Language::get_available_languages();
	}
    
	function perform(){
		try {
            if (!$this->arguments->username) throw new Exception(Language::tag("ERROR_LOGIN_MISSING_USERNAME"));
            if (!$this->arguments->password) throw new Exception(Language::tag("ERROR_LOGIN_MISSING_PASSWORD"));
            
			$user_record = Userassistant::login($this->arguments->username, $this->arguments->password);
			$user_record->last_login = "NOW()";
			$user_record->logins++;
			$user_record->save();
            
            $default_page = $user_record->default_page ? $user_record->default_page : 'index';
            
            //Language
            $_SESSION["language"] = $this->arguments->lang;
            
            $this->redirect(new shUrl($default_page), array("success" => Language::tag("TEXT_LOGGED_IN")));
		} catch (Exception $e){
			$this->redirect('index', array('error' => $e->getMessage()));
		}
	}
    
	function logout() {
		Userassistant::logout();
		$this->redirect('index', array('success' => Language::tag("TEXT_LOGGED_OUT")));
	}
	
}
