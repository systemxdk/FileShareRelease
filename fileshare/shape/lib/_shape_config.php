<?php

class shConfig {
	
	public $config = Array();
	
	static function set($key, $value) {
		self::$config[$key] = $value;	
	}
	
}

