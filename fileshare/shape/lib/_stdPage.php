<?php

function error404(){

	$system = isset($GLOBALS['_CONFIG']['DEFAULT_ERROR_SYSTEM']) ? $GLOBALS['_CONFIG']['DEFAULT_ERROR_SYSTEM'] : NULL;
	$module = isset($GLOBALS['_CONFIG']['DEFAULT_ERROR_MODULE']) ? $GLOBALS['_CONFIG']['DEFAULT_ERROR_MODULE'] : NULL;
	$action = isset($GLOBALS['_CONFIG']['DEFAULT_ERROR_ACTION']) ? $GLOBALS['_CONFIG']['DEFAULT_ERROR_ACTION'] : 'index';
	
	if($system && $module){
		$_SESSION['requested_url'] = $_SERVER['REQUEST_URI'];

		if (file_exists($GLOBALS['_CONFIG']['SHAPEROOT']."/systems/".$system.'/modules/'.$module)) {
			header('Location: '. new shUrl($system.'/'.$module.'/'.$action));
			die();
		} else {
			require_once $GLOBALS['_CONFIG']['SHAPEROOT'] . "shape/templates/error.html";
			die();
		}
	} else {
		require_once $GLOBALS['_CONFIG']['SHAPEROOT'] . "/shape/templates/welcome.html";
	}
}
?>
