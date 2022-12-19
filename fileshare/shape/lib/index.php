<?php
ob_start();
if( !defined("SHAPEROOT") ){
	if ( isset($_REQUEST['PHPSESSID']) ){ // Load Existing Session - Payment Module etc.
		session_id($_REQUEST['PHPSESSID']);
		session_start();
	} else {
		session_start();
	}
}
$GLOBALS['SHAPE_WEBSERVICE'] = FALSE;

/**
 * Framework loader
 */
require_once $GLOBALS['_CONFIG']['SHAPEROOT'] . '/shape/lib/_shape_loader.php';
require_once $GLOBALS['_CONFIG']['SHAPEROOT'] . '/shape/lib/_shape_filter.php';

/**
 * Set system root dir (constant)
 */
define("SYSTEMROOT", $GLOBALS['_CONFIG']['SHAPEROOT'] . "/systems/");

/**
 * Requieres
 */
$libPath = dirname(__FILE__).'/';
require_once $libPath . '_helper.php';
require_once $libPath . '_controller.php';
require_once $libPath . '_crud_controller.php';
require_once $libPath . '_webservice_class.php';
require_once $libPath . '_webservice_controller.php';
require_once $libPath . '_shape_component_loader.php';
require_once $libPath . '_shape_email.php';
require_once $libPath . '_stdPage.php';

/**
 * set constant document root
 */
$docroot = '';
if(dirname($_SERVER['PHP_SELF']) != '/'){
	$docroot = dirname($_SERVER['PHP_SELF']);
}
define("PUBLIC_DIR", $docroot);

/**
 * requires
 * 	Here we run through this dir and load all php files
 */
require_once $GLOBALS['_CONFIG']['SHAPEROOT'] . "/includes/config.php";

$libPath = $GLOBALS['_CONFIG']['SHAPEROOT'] . "/shape/lib/";
$dirHandle = dir($libPath);


/**
 * split arguments (part of url)
 */

$args = isset($_REQUEST['args']) && strstr($_REQUEST['args'], '/') ? explode('/',$_REQUEST['args']) : NULL;

/**
 * default module implementation
 */
if(!isset($args[0]) && !isset($GLOBALS['_CONFIG']['DEFAULT_MODULE'])){
	error404();
	exit;
}

/**
 * shift module and action from arguments
 */

$system = isset($args[0]) ? array_shift($args) : $GLOBALS['_CONFIG']['DEFAULT_SYSTEM'];
$module = isset($args[0]) ? array_shift($args) : $GLOBALS['_CONFIG']['DEFAULT_MODULE'];
$action	= isset($args[0]) ? array_shift($args) : "index" ;

/**
 * Read system and module configs
 */
if( file_exists(SYSTEMROOT . $system . "/includes/config.php") ){
    require_once SYSTEMROOT . $system . "/includes/config.php";
}
if( file_exists( SYSTEMROOT . $system ."/modules/" . $module . "/includes/config.php" ) ){
    require_once SYSTEMROOT . $system ."/modules/" . $module . "/includes/config.php";
}
if( file_exists( SYSTEMROOT . $system . '/modules/' . $module . '/' . strtolower($module) . '_controller.php') ){
	$module = strtolower($module);
} elseif(isset($GLOBALS['_CONFIG']['DEFAULT_ERROR_MODULE'])) {
	$module = $GLOBALS['_CONFIG']['DEFAULT_ERROR_MODULE'];
	$action = isset($GLOBALS['_CONFIG']['DEFAULT_ERROR_ACTION']) ? $GLOBALS['_CONFIG']['DEFAULT_ERROR_ACTION'] : $action;
} else {
	error404();
	exit;
}

$jscripts = NULL;
$css = NULL;

/**
 * require and instantiate main controller
 */
if( file_exists(SYSTEMROOT . $system . '/modules/' . $module . '/' . strtolower($module) . '_controller.php') ){
	require_once SYSTEMROOT . $system . '/modules/' . $module . '/' . strtolower($module) . '_controller.php';
} else {
	error404();
	exit;
}

$_classname = ucfirst($module) . "Controller";

$_class = new $_classname($_REQUEST);

/**
 * view var, used below
 */
$viewclass = NULL;

/*
 * Handle javascripts
 */
$js_paths = array(
    $GLOBALS['_CONFIG']['SHAPEROOT'].'/public/_'.$system.'/javascripts/'.$system.'.js' => PUBLIC_DIR.'/_'.$system.'/javascripts/'.$system.'.js',
    $GLOBALS['_CONFIG']['SHAPEROOT'].'/public/_'.$system.'/javascripts/'.$module.'.js' => PUBLIC_DIR.'/_'.$system.'/javascripts/'.$module.'.js',
    $GLOBALS['_CONFIG']['SHAPEROOT'].'/public/_'.$system.'/javascripts/'.$action.'.js' => PUBLIC_DIR.'/_'.$system.'/javascripts/'.$action.'.js',
);
foreach ($js_paths as $local_path => $js_path){
    //error_log('PATH:'.$local_path);
    if ( file_exists($local_path) ){
	    $jscripts .= '<script type="text/javascript" src="'.$js_path.'" ></script>';
    }
}

/*
 * Start output buffering to catch all output (controllers just print)
 */
if( method_exists($_class, $action) ){

	/**
	 * run filters
	 */
	$pathData = array(
		'system'	=> $system,
		'module'	=> $module,
		'action'	=> $action,
		'component'	=> FALSE
	);
	foreach (shFilter::GetFilters() as $filter){
		$ignorefilters = $_class->getIgnorefilters();
		if ( is_array($ignorefilters) ){
			if( !in_array(strtolower( get_class($filter) ), $ignorefilters) ){
				$filter->execute($_REQUEST,$pathData);
			}
		} else {
			$filter->execute($_REQUEST,$pathData);
		}
	}
	$_class->$action();
} else {
	error404();
}

if (!$GLOBALS['SHAPE_WEBSERVICE']) {
	$viewclass = $viewclass ? $viewclass : $_class;
	$viewclass->callView();
	
	/**
	 * flush out the page
	 */
	ob_end_flush();
}
