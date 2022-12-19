<?php

class UserAccessFilter {

    static function Verify($required_access_level, $redirect = false){
		
        //Pessimistic default false, no access
        $access_met = false;
        
        //Check users access level with the required on
        if ($_SESSION["access_level"] & $required_access_level) {
            $access_met = true;
        }
        
        if (!$access_met && $redirect) {
            $_SESSION['error'] = Language::tag("TEXT_USERACCESS_NOT_GRANTED");
			header("Location: /" . $_SESSION["default_page"]);
			die();
        }
    
        return $access_met;
    }
	
}