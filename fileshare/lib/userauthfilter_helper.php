<?php

class UserauthFilter {

    function __construct(){}

    function execute($arguments){
        if (!Userassistant::is_logged_in()){
			$this->redirect_login();
        }
    }
	
	function redirect_login($location = null) {
		unset($_SESSION);
		$_SESSION['error'] = Language::tag("ERROR_LOGIN_NOT_LOGGED_IN");
        
        //As we are not in shape controller scope here we redirect to fileshare login page
        //Redirect oldschool
		header("Location: /auth/login/index");
		die();
        
	}
}