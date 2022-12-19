#! /usr/bin/php
<?php

$baseShapeRoot = dirname ( __FILE__ ) . "/../../";

//ASk for base path
echo "base path of project: (deafults to " . $baseShapeRoot . "):";
$pathinput = trim( fread ( STDIN, 1024 ) );

$baseShapeRoot = $pathinput ? $pathinput : $baseShapeRoot;

$baseShapeRoot .= preg_match('/\/^/',$baseShapeRoot) ? '' : '/';

define ( "SHAPEROOT",$baseShapeRoot );
define ( "SYSTEMROOT", $baseShapeRoot . "systems/" );

$libPath = SHAPEROOT . 'shape/lib/';

require_once $libPath . '_helper.php';
require_once $libPath . '_controller.php';
require_once $libPath . '_shape_filter.php';
require_once $libPath . '_crud_controller.php';
require_once $libPath . '_webservice_class.php';
require_once $libPath . '_webservice_controller.php';
require_once $libPath . 'selectbox_helper.php';

echo "Velkommen til SHAPE builder";
$done = false;
while ( ! $done ) {
	echo <<<EOM
	
Hvad vil du generere ?
1. System
2. Modul
3. Helper
4. Action
5. Model
6. Model and CRUD
7. Webservice module
20. New Base Project Project
0. Exit
Indtast dit valg: 
EOM;
	$input = fread ( STDIN, 2 );
	$done = true;
	
	switch ( $input) {
        case 1 :
            echo "Indtast system navn: ";
            $systemname = strtolower( trim( fread(STDIN, 1024)) );
            CreateSystem($systemname);
            break;
		case 2 :
            echo "Indtast system navn: ";
            $systemname = strtolower( trim( fread(STDIN, 1024) ) );
            echo "Indtast Modulnavn: ";
			$input = strtolower( trim ( fread ( STDIN, 1024 ) ) );
			CreateModule ( $input, $systemname );
            break;
		case 3 :
			echo "Indtast Helper navn: ";
			$input = fread ( STDIN, 1024 );
			createhelper ( trim ( $input ) );
		break;
		case 4 :
            echo "Indtast system navn: ";
            $systemname = strtolower( trim( fread(STDIN, 1024) ) );
			echo "Indtast Modulnavn: ";
			$moduleName = strtolower( trim ( fread ( STDIN, 1024 ) ) );
			echo "Indtast Actionnavn: ";
			$actionName = trim ( fread ( STDIN, 1024 ) );
			createAction ( $moduleName, $actionName, $systemname );
		break;
		case 5 :
			echo "Indtast tabel  navn: ";
			$tablename = trim ( fread ( STDIN, 1024 ) );
			$tableDesc = array ( );
			require_once SHAPEROOT . 'shape/scripts/modelbuilder.php';
		break;
		case 6 :
            echo "Indtast system navn: ";
            $systemname = strtolower( trim( fread(STDIN, 1024) ) );
			echo "Indtast tabel  navn: ";
			$tablename = trim ( fread ( STDIN, 1024 ) );
			$tableDesc = array ( );
			require_once SHAPEROOT . 'shape/scripts/modelbuilder.php';
			
			$modulename = trim ( strtolower ( camelcase ( $tablename ) ) );
			
			CreateModule ( $modulename, $systemname, camelcase ( $modulename ) );
			echo "adding action create";
			createAction ( $modulename, 'create', $systemname, 'ormcreate' );
			echo "adding action update";
			createAction ( $modulename, 'update', $systemname, 'ormupdate' );
			echo "adding action select";
			createAction ( $modulename, 'select', $systemname, 'ormselect' );
			echo "adding action delete";
			createAction ( $modulename, 'delete', $systemname, 'ormdelete' );
			echo "adding enum methods";
			createEnumMethods ( $modulename, $systemname, $tableDesc );
		break;
		case 7 :
            echo "Indtast system navn: ";
            $systemname = strtolower( trim( fread(STDIN, 1024) ) );
			echo "Indtast Webservice Modulnavn: ";
			$input = trim( fread ( STDIN, 1024 ) );
			echo "creating module....";
			CreateModule ( $input, $systemname, NULL, 1 );
			CreateWebserviceFiles($input,$systemname);
		break;
		case 20 :
		    echo "Indtast system navn: ";
		    $projectname = strtolower( trim( fread(STDIN, 1024) ) );
		    CreateNewProject ( $projectname );
		break;
		case 0 :
			echo "Exiting";
		break;
		default :
			echo "Invalid selection - please try again";
			$done = false;
		break;
	}
}

echo "\n";

function CreateNewProject($projectname,$path = ''){
    echo "Testing if path to project exists..\n";
    if( is_dir( $path.$projectname) ){
        Die ( "Project folder already exists, cannot create project there!" );
    }
    echo " create Directories.....\n";
    createDir ( SYSTEMROOT . $systemname , $systemname);
    createDir ( SYSTEMROOT . $systemname . '/includes', "includes" );
    createDir ( SYSTEMROOT . $systemname . '/lib', "lib" );
    createDir ( SYSTEMROOT . $systemname . '/modules', "module" );
    createDir ( SYSTEMROOT . $systemname . '/public', "public" );
    createDir ( SYSTEMROOT . $systemname . '/view', "view");

    echo "Creating symlink to systems public directory!";
    if( is_dir(SHAPEROOT . "public/" . $systemname) || file_exists(SHAPEROOT . "public/" . $systemname) ){
        echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n";
        echo "ERROR, can not create symbolic link, " . SHAPEROOT . "public/" . $systemname . " exists.\n";
        echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n";
    } else {
        try {
            if(!symlink(SYSTEMROOT . $systemname . "/public", SHAPEROOT . "public/_" . $systemname) ){
                    echo "ERROR\n";
            }
        } catch (Exception $e){
            echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n";
            echo "Exception, can not create symbolic link, " . SHAPEROOT . "public/" . $systemname . " exists.\n";
            echo $e->getMessage() . "\n";
            echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n";
        }
    }

}

function ReadTrimLowercase(){
    return strtolower( trim( fread(STDIN, 1024) ) );
}

function CreateWebserviceFiles($module, $systemname){
	$wsdlContent	= file_get_contents ( SHAPEROOT . "/shape/templates/webservice_wsdl_template.txt" );
	$classContent	= file_get_contents ( SHAPEROOT . "/shape/templates/webservice_class_template.txt" );
	$classContent	= str_replace ( "<<MODULE_NAME>>", ucfirst( $module ), $classContent );
	
	$includeDir = SYSTEMROOT . $systemname . "/modules/" . strtolower($module) . "/includes/";
	echo "Include dir : " . $includeDir . "\n";
	$wsdlPath	= $includeDir . "webservice.wsdl";
	$classPath	= $includeDir . "webservice.class.php";
	createFile ( $wsdlPath, 'webservice wsdl', $wsdlContent );
	createFile ( $classPath, 'webservice class', $classContent );
}

function createhelper($helper) {
	$includeDir = SHAPEROOT . "lib/";
	$helperFileName = $helper . '_helper.php';
	
	$fullFilepath = $includeDir . $helperFileName;
	
	if (@file_exists ( $fullFilepath )) {
		die ( "helperfile already exists! try with another name" );
	}
	
	$helperContent = file_get_contents ( SHAPEROOT . "/shape/templates/helper_template.txt" );
	$helperContent = str_replace ( "<<HELPER_NAME>>", ucfirst ( $helper ), $helperContent );
	createFile ( $fullFilepath, $helperFileName, $helperContent );
}

function createEnumMethods($module, $systemname, $tableDesc) {
	
	$moduleDir = SYSTEMROOT . $systemname . "/modules";
	$moduleSubDir = $moduleDir . "/" . $module;
	if (! is_dir ( $moduleSubDir )) {
		Die ( "Module doesn't exist" );
	}
	if (@file_exists ( $moduleSubDir . '/' . strtolower ( $module ) . '_controller.php' )) {
		$module = strtolower ( $module );
	} else {
        echo "ERROR : " . $moduleSubDir . '/' . strtolower ( $module ) . '_controller.php' . "\n";
        echo "Module is missing controller - Please recreate the module!/n";
		die ( "Module is missing controller - Please recreate the module!" );
	}
	
	$controllerPath = $moduleSubDir . '/' . strtolower ( $module ) . '_controller.php';
	require_once $controllerPath;
	
	$moduleName = $module . "Controller";
	$moduleObject = new $moduleName ( );
	if (method_exists ( $moduleObject, $action )) {
		die ( "Method already exists" );
	}
	
	/**
	 * add methods
	 */
	foreach ( $tableDesc as $key => $val ) {
		if (preg_match ( "/^enum\((.+)\)/", $val ['Type'], $match )) {
			$choices = $val ['Type'];
			$choices = preg_replace ( "/('|\(|\)|enum)/", '', $choices );
			$list = explode ( ",", $choices );
			
			$replace = '';
			foreach ( $list as $k => $v ) {
				$replace .= <<<OPTIONS
			'{$v}' => '{$v}',
							
OPTIONS;
			}
			$arrContent = file_get_contents ( SHAPEROOT . "/shape/templates/ormenum_method_template.txt" );
			$arrContent = str_replace ( "<<ACTION_NAME>>", $key . 'Enum', $arrContent );
			$arrContent = str_replace ( "<<ARRAY>>", $replace, $arrContent );
			
			$controllerContent = file_get_contents ( $controllerPath );
			$pos = strripos ( $controllerContent, "}" );
			$controllerContent = substr ( $controllerContent, 0, $pos ) . $arrContent;
			
			if (! file_put_contents ( $controllerPath, $controllerContent )) {
				die ( "Could not update controller with the new action!" );
			}
			
		//echo new shSelectbox($list,$key);
		}
	}

}

function createAction($module, $action, $systemname, $predefined = NULL) {
	
	global $tableDesc;
	
	$moduleDir = SYSTEMROOT . $systemname . "/modules";
	$moduleSubDir = $moduleDir . "/" . $module;
	if (! is_dir ( $moduleSubDir )) {
		Die ( "Module doesn't exist" );
	}
	$GLOBALS ['_CONFIG'] ['SHAPEROOT'] = SHAPEROOT;
	//require_once $GLOBALS ['_CONFIG'] ['SHAPEROOT'] . "includes/config.php";
	
	//require_once $GLOBALS['_CONFIG']['SHAPEROOT'] . '/active_record/class_loader.php';
	$libPath = $GLOBALS ['_CONFIG'] ['SHAPEROOT'] . "shape/lib/";
	$dirHandle = dir ( $libPath );
	
//	while ( $file = $dirHandle->read () ) {
//		if (preg_match ( "/.php$/i", $file ) && ! preg_match ( "/index.php$/i", $file )) {
//			require_once ($libPath . $file);
//		}
//	}
	
	if (@file_exists ( $moduleSubDir . '/' . strtolower ( $module ) . '_controller.php' )) {
		$module = strtolower ( $module );
	} else {
        echo $moduleSubDir . '/' . $module . '/' . strtolower ( $module ) . '_controller.php\n';
		die ( "Module is missing controller - Please recreate the module!" );
	}
	
	$controllerPath = $moduleSubDir . '/' . strtolower ( $module ) . '_controller.php';
	require_once $controllerPath;
	
	$moduleName = $module . "Controller";
	$moduleObject = new $moduleName ( );
	if (method_exists ( $moduleObject, $action ) && ! preg_match ( "/^orm/", $predefined )) {
		die ( "Method already exists" );
	}
	
	/*
	 * support of predefined action templates
	 */
	$predefined = $predefined ? $predefined . "_" : '';
	
	$actionContent = file_get_contents ( SHAPEROOT . "/shape/templates/" . $predefined . "action_template.txt" );
	$actionContent = str_replace ( "<<ACTION_NAME>>", $action, $actionContent );
	$actionContent = str_replace ( "<<TABLE_NAME>>", camelcase ( $module ), $actionContent );
	$actionContent = str_replace ( "<<MODULE_NAME>>", strtolower ( $module ), $actionContent );
	$controllerContent = file_get_contents ( $controllerPath );
	$pos = strripos ( $controllerContent, "}" );
	$controllerContent = substr ( $controllerContent, 0, $pos ) . $actionContent;
	
	if (! file_put_contents ( $controllerPath, $controllerContent )) {
		die ( "Could not update controller with the new action!" );
	}
	
	if ($predefined == 'ormselect_') {
		ob_start ();
		require_once SHAPEROOT . 'shape/view/_dbadmin_view.php';
		$viewContent = ob_get_clean ();
	} else {
		$viewContent = file_get_contents ( SHAPEROOT . "/shape/templates/view_template.txt" );
	}
	$viewPath = $moduleSubDir . "/view/" . $action . ".php";
	createFile ( $viewPath, $action . ".php", $viewContent );

}

function CreateSystem($systemname){
    echo "Testing if system exists..\n";
    if( is_dir( SYSTEMROOT . $systemname) ){
        Die ( "System already exists exist" );
    }
    echo " create Directories.....\n";
    createDir ( SYSTEMROOT . $systemname , $systemname);
    createDir ( SYSTEMROOT . $systemname . '/includes', "includes" );
    createDir ( SYSTEMROOT . $systemname . '/lib', "lib" );
    createDir ( SYSTEMROOT . $systemname . '/modules', "module" );
    createDir ( SYSTEMROOT . $systemname . '/public', "public" );
    createDir ( SYSTEMROOT . $systemname . '/view', "view");

    echo "Creating symlink to systems public directory!";
    if( is_dir(SHAPEROOT . "public/" . $systemname) || file_exists(SHAPEROOT . "public/" . $systemname) ){
        echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n";
        echo "ERROR, can not create symbolic link, " . SHAPEROOT . "public/" . $systemname . " exists.\n";
        echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n";
    } else {
        try {
            if(!symlink(SYSTEMROOT . $systemname . "/public", SHAPEROOT . "public/_" . $systemname) ){
                    echo "ERROR\n";
            }
        } catch (Exception $e){
            echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n";
            echo "Exception, can not create symbolic link, " . SHAPEROOT . "public/" . $systemname . " exists.\n";
            echo $e->getMessage() . "\n";
            echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n";
        }
    }

}

function CreateModule($moduleName, $systemname, $crud = NULL, $webservice = NULL) {
	$moduleDir = SYSTEMROOT . $systemname . "/modules";
	createDir ( $moduleDir, "modules" );
	
	$moduleSubDir = $moduleDir . "/" . $moduleName;
	createDir ( $moduleSubDir, $moduleName );
	
	$controllerName = "{$moduleName}_controller.php";
	$controllerPath = $moduleSubDir . "/{$moduleName}_controller.php";
	
	if($webservice){
		$controllerContent = file_get_contents ( SHAPEROOT . "/shape/templates/webservice_controller_template.txt" );
	} else {
		$controllerContent = file_get_contents ( SHAPEROOT . "/shape/templates/controller_template.txt" );
	}
	
	$controllerContent = str_replace ( "<<MODULE_NAME>>", ucfirst ( $moduleName ) . "Controller", $controllerContent );
	$controllerContent = str_replace ( "<<CLASS_NAME>>", ucfirst ( $moduleName ), $controllerContent );
	if ($crud) {
		$controllerContent = str_replace ( "extends shController", "extends CrudController", $controllerContent );
	}
	createFile ( $controllerPath, $controllerName, $controllerContent );
	
	$viewFolderPath = $moduleSubDir . "/view";
	createDir ( $viewFolderPath, "view" );
	
	$viewContent = file_get_contents ( SHAPEROOT . "/shape/templates/view_template.txt" );
	$indexViewPath = $viewFolderPath . "/index.php";
	createFile ( $indexViewPath, "index.php", $viewContent );
	
	$includeFolderPath = $moduleSubDir . "/includes";
	createDir ( $includeFolderPath, "includes" );
	
	$helperFolderPath = $moduleSubDir . "/helpers";
	createDir ( $helperFolderPath, "helpers" );
}

function createDir($path, $name) {
	echo "Creating directory $name - ";
	if (! is_dir ( $path )) {
		if (mkdir ( $path ))
			echo "success\n"; else
			die ( "failure\n" );
	} else {
		echo "exists\n";
	}
}

function createFile($path, $name, $content = "") {
	echo "Creating file $name - ";
	if (! is_file ( $path )) {
		if (file_put_contents ( $path, $content ) !== false)
			echo "success\n"; else
			die ( "failure\n" );
	} else {
		echo "exists\n";
	}
}
function camelcase($str) {
	$words = array ( );
	foreach ( explode ( "_", $str ) as $word ) {
		array_push ( $words, ucfirst ( strtolower ( $word ) ) );
	}
	return join ( "", $words );
}
