<?php

require_once $GLOBALS['_CONFIG']['SHAPEROOT'] . '/active_record/activerecord.php';
require_once $GLOBALS['_CONFIG']['SHAPEROOT'] . '/active_record/database.php';

spl_autoload_register('autoloader');

function autoloader($class_name){
    $shr = $GLOBALS['_CONFIG']['SHAPEROOT'];
	
	/**
	 * path to webservicedir
	 */
    switch(TRUE){
        case ($GLOBALS['SHAPE_WEBSERVICE'] && @file_exists($webservice_obj_dir . $class_name . '.php')):
            require_once $webservice_obj_dir . $class_name . '.php';
            break;
        case (@file_exists($shr . '/systems/' . @$GLOBALS['system'] .  '/modules/' . @$GLOBALS['module'] . "/helpers/" . strtolower($class_name) . '.php')):
            require_once $shr . '/systems/' . @$GLOBALS['system'] .  '/modules/' . @$GLOBALS['module'] . "/helpers/" . strtolower($class_name) . '.php';
            break;
        case (@file_exists($shr . '/lib/' . strtolower($class_name) . '.php')):
            require_once $shr . '/lib/' . strtolower($class_name) . '.php';
            break;
        case (@file_exists($shr . '/lib/' . strtolower($class_name) . '_helper.php')):
            require_once $shr . '/lib/' . strtolower($class_name) . '_helper.php';
            break;
        case (@file_exists($shr . '/systems/' . @$GLOBALS['system'] .  '/lib/' . $class_name . '_helper.php')):
            require_once $shr . '/systems/' . @$GLOBALS['system'] .  '/lib/' . $class_name . '_helper.php';
            break;
        case (@file_exists($shr . '/systems/' . @$GLOBALS['system'] .  '/lib/' . strtolower($class_name) . '_helper.php')):
            require_once $shr . '/systems/' . @$GLOBALS['system'] .  '/lib/' . strtolower($class_name) . '_helper.php';
            break;
        case (@file_exists($shr . '/shape/lib/' . substr(strtolower($class_name),2,strlen($class_name)-2) . '_helper.php')):
            require_once $shr . '/shape/lib/' . substr(strtolower($class_name),2,strlen($class_name)-2) . '_helper.php';
            break;
        case (file_exists($shr . "/active_record/models/" . strtolower($class_name) . ".php")):
            require_once $shr . "/active_record/models/" . strtolower($class_name) . ".php";
            break;
        default:
            throw new Exception("Class " . $class_name . " not found");
    }
}

function shLoadFilter($filter){ 
    switch(TRUE){
        case ( file_exists( SYSTEMROOT . @$GLOBALS['system'] . '/modules/' . @$GLOBALS['module'] . '/lib/' . $filter . '_filter.php') ):
            require_once SYSTEMROOT . @$GLOBALS['system'] . '/modules/' . @$GLOBALS['module'] . '/lib/' . $filter . '_filter.php';
            break;
        case ( file_exists( SYSTEMROOT . @$GLOBALS['system'] . '/lib/' . $filter . '_filter.php') ):
            require_once SYSTEMROOT . @$GLOBALS['system'] . '/lib/' . $filter . '_filter.php';
            break;
        case ( file_exists( @$GLOBALS['_CONFIG']['SHAPEROOT'] . '/lib/' . $filter . '_filter.php' ) ):
            require_once @$GLOBALS['_CONFIG']['SHAPEROOT'] . '/lib/' . $filter . '_filter.php';
            break;
        default:
            throw new Exception("Filter " . $filter . " not found!");
    }
}