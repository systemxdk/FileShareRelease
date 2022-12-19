<?php

require_once dirname(__FILE__) . "/activerecord.php";
require_once dirname(__FILE__) . "/database.php";

spl_autoload_register('ar_autoloader');

function ar_autoloader($class_name){
	require_once dirname(__FILE__) . "/models/" . strtolower($class_name) . ".php";
}

?>
