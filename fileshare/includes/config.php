<?php

//Shape framework settings
$GLOBALS['_CONFIG']['DEFAULT_ERROR_SYSTEM'] = "error";
$GLOBALS['_CONFIG']['DEFAULT_ERROR_MODULE'] = "page";
$GLOBALS['_CONFIG']['DEFAULT_ERROR_ACTION'] = "index";

$GLOBALS['_CONFIG']['DEFAULT_SYSTEM'] = "auth";
$GLOBALS['_CONFIG']['DEFAULT_MODULE'] = "login";

//Danish timezone setting for fileshare
date_default_timezone_set('Europe/Copenhagen');

//Build i18n definitions from files
Language::set_default_language("danish");
Language::set_path($GLOBALS["_CONFIG"]["SHAPEROOT"] . "i18n/");
Language::load();

//Fileshare globals
$GLOBALS['_CONFIG']['CREATOR'] = "Steffen Beck";