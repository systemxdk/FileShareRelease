<?php
function shCallComponent($path, array $arguments = null){

    $pathRules = array(
        'system' => $GLOBALS['system'],
        'module' => $GLOBALS['module'],
        'action' => $GLOBALS['action'],
    );

    /**
     * split path on / for module and action
     */
    $paths = explode("/", $path);

    switch(count($paths)){
        case 3:
            $pathRules['system'] = $paths[0];
            $pathRules['module'] = $paths[1];
            $pathRules['action'] = $paths[2];
            break;
        case 2:
            $pathRules['module'] = $paths[0];
            $pathRules['action'] = $paths[1];
            break;
        case 1:
            $pathRules['action'] = $paths[0];
            break;
        default:
            throw new Exception("Invalid action and/or controller");
        break;
    }

    /**
     *
     */
    require_once SYSTEMROOT . $pathRules['system'] . '/modules/' . $pathRules['module'] . '/' . strtolower($pathRules['module']) . '_controller.php';
    $_classname = ucfirst($pathRules['module']) . "Controller";
    $arguments = $arguments ? $arguments : array();
    if( !key_exists("shPath", $arguments)){
        $arguments['shPath'] = $pathRules;
    }
    $_class = new $_classname($arguments);

    foreach($pathRules as $pathKey => $pathVal){
        $_class->$pathKey = $pathVal;
    }

    $_class->$pathRules['action']();
    return $_class->callView(TRUE,$pathRules);
}
