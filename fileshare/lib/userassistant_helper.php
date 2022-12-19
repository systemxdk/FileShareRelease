<?php

class Userassistant {

	static private $user = null;

	static function is_logged_in(){
		return isset($_SESSION['authorized']) && $_SESSION['authorized'];
	}
	
    static function generate_salt(){ //xxxnnxxx
        $alpha = array(
            "a", "b", "c", "d", "e", 
            "f", "g", "h", "i", "j", 
            "k", "l", "m", "n", "o", 
            "p", "q", "r", "s", "t", 
            "u", "v", "w", "x", "y", "z"
        );
        
        $numbers = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
        
        $salt = "$";
        for ($i = 0; $i <= 7; $i++) {
            if ($i <= 2) $salt .= $alpha[array_rand($alpha)]; // first 3
            if ($i > 2 && $i < 5) $salt .= $numbers[array_rand($numbers)]; // mid 2
            if ($i >= 5) $salt .= $alpha[array_rand($alpha)];
        }
        
        $salt .= "$";
        return $salt;
    }
    
    static function generate_hash($password) {
        $salt = self::generate_salt();
        
        return array($salt, sha1($salt . $password));
    }
    
    static function user_exist($username) {
		$user = new User();
		$user->find_by_username($username);
        
        return $user->username !== null;
    }
    
	static function get_user(){
        if (!isset($_SESSION['authorized']) || !$_SESSION['authorized']) return;
        
		$user = new User();
		$user->find($_SESSION['authorized']);
        
        return $user;
	}
	
	static function login($username, $password){
		$user = new User();
		$user->find_by_username($username);
        
		if ($user->password && $user->password == sha1($user->salt . $password)) {
            
			//Invalidate active session if any
			self::logout();
            
			//Register the authorized user id
			$_SESSION['authorized'] = $user->id;
			$_SESSION['access_level'] = $user->access_level;
			$_SESSION['default_page'] = $user->default_page;
            
			return $user;
		} else {
			throw new Exception(Language::tag("ERROR_LOGIN_INVALID"));
		}
	}
	
	static function logout(){
        unset($_SESSION['authorized']);
        unset($_SESSION['access_level']);
		self::$user = null;
	}
    
    static function render_username($row, $value) {
        $user = new User();
        $user->find($row->user_id);
        return $user->username;
    }
    
}
