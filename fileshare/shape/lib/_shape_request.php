<?php

class shRequest {

    static function get($key){
	return $_REQUEST[$key];
    }

    static function exists($keys, $falsed = FALSE){
	if (!is_array($keys)){
	    $keys = array($keys);
	}
	foreach ( $keys as $key ){
	    if(!isset($_REQUEST[$key])){
		throw new Exception('Key '.$key.' is not set in request',$key);
	    }
	    if ( $falsed && !$_REQUEST[$key]){
		throw new Exception('Key '.$key.' is not valid in request',$key);
	    }
	}
    }
    
}