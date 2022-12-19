<?php

class shSession {

    static private $log = FALSE;

    static function get($key){
	return $_SESSION[$key];
    }

    static function set($key,$value, $caller = NULL){
	$_SESSION[$key] = $value;
	self::errorlog($key, $value, $caller);
    }

    static function startlog(){
	self::log(TRUE);
    }

    static function stoplog(){
	self::log(FALSE);
    }

    static private function log($state = FALSE){
	if( !is_bool($state) ){
	    throw new Exception('to enable/disable errorlogging, call with bool value');
	}
	self::$log = $state;
    }

    static private function errorlog($key,$value, $caller){
	if (self::$log){
	    if ( !is_string($value) && !is_numeric($value) ){
		$value = 'value of type '.gettype($value);
	    }
	    $caller = $caller ? 'Caller: '.$caller : '';
	    error_log('SHAPELOG: Session key '.$key.' set to '.$value.' '.$caller );
	}
    }
}