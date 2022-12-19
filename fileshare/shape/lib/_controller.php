<?php

/**
 * shape abstract class shController
 *
 */
abstract class shController {
	
	/**
	 * contains dynamically creater attributes 
	 *
	 * @var array
	 */
	private $attributes	= array();
	
	/**
	 * used as stdClass for parameters/arguments
	 *
	 * @var stdClass
	 */
	protected $arguments;

	/**
	 *
	 * @var array
	 */
	private $filterdata = array();
	
	/**
	 * Constructor
	 *
	 * @param array $arguments
	 */
	function __construct(array $arguments = array()){
		
		$this->arguments = new stdClass();
		
		foreach($arguments as $key => $argument){
			$this->arguments->$key = $argument;
		}

        /**
         * Run filters
         */
        $this->filterdata['filters'] = shFilter::GetFilters();
        $this->filterdata['ignores'] = shFilter::GetIgnoreFilters();
				
	}

	/**
	 *
	 * @return array
	 */
	public function getFilters(){
		return $this->filterdata['filters'];
	}

	/**
	 *
	 * @return array
	 */
	public function getIgnorefilters(){
		if (isset($this->filterdate['ignores'])) return $this->filterdate['ignores'];
	}

	/**
	 * magic function to set dynamically attributes
	 *
	 * @param string $name
	 * @param unknown_type $value
	 */
	function __set($name,$value){
		$this->attributes[$name] = $value;
	}
	
	/**
	 * magic function to get dynamically attributes
	 *
	 * @param string $name
	 * @return unknown
	 */
	function __get($name){
		if ( isset($this->attributes[$name]) ) {
			return $this->attributes[$name];
		}
	}
	
	/**
	 * call of view, default is to fetch global layout last
	 *
	 * @param bool $nolayout defaults to FALSE
	 */
	function callView($nolayout = FALSE, array $pathDetails = array()){
		
        /**
         * Set system, module and action
         */
        $pathDetails['system'] = key_exists('system', $pathDetails) ? $pathDetails['system'] : $GLOBALS['system'];
        $pathDetails['module'] = key_exists('module', $pathDetails) ? $pathDetails['module'] : $GLOBALS['module'];
        $pathDetails['action'] = key_exists('action', $pathDetails) ? $pathDetails['action'] : $GLOBALS['action'];

		/**
		 * Create variables for use in view
		 */
		foreach ($this->attributes as $key => $var){
			${$key} = $this->attributes[$key];
		}
		
		/**
		 * fetch who "I" am
		 */
		$controllerName = strtolower( get_class($this) );
        
		// Register special redirect session variables to view
        $error = null;
        $warning = null;
        $success = null;
        
		foreach (array("error", "success", "warning") as $arg) {
			if (!isset($_SESSION[$arg])) continue;
			
			${$arg} = $_SESSION[$arg];

			unset($_SESSION[$arg]);
		}
        
		/**
		 * If a view exists, require it
		 */
        $viewFile = SYSTEMROOT . $pathDetails['system'] . '/modules/' . $pathDetails['module'] . '/view/' . $pathDetails['action'] . '.php';
		if( file_exists( $viewFile ) ){
			require_once $viewFile;
		}
		
		/**
		 * this variable is used in the main view (global view)
		 */
		$content = ob_get_clean();
		ob_start();
		
		if( $nolayout || !isset($GLOBALS['_CONFIG']['LAYOUT'])){
			print $content;
		} else {
			/**
			 * main layout file
			 */
			$shr = $GLOBALS['_CONFIG']['SHAPEROOT'];
			
			switch(TRUE){
				case (@file_exists($shr . '/view/' . $GLOBALS['_CONFIG']['LAYOUT']));
					require_once $shr . '/view/' . $GLOBALS['_CONFIG']['LAYOUT'];
					break;
				default:
					require_once $shr . '/systems/' . $GLOBALS['system'] .  '/view/default.php';
					break;
			}
		}
	}
	
	function callLayout(){
		
	}
	
	/**
	 * redirects using header location
	 *
	 * @param string $url
	 * @param array $sessionvars
	 */
	protected function redirect($url, array $sessionvars = array()){
		$url_info = explode("://",$url);
		$url_header = substr($url_info[0], 0, 4);
		if (!is_a($url,"shUrl") && $url_header != 'http') {
			$url = new shUrl($url);
			$url = $url->__toString();
		}
		
		if ( isset($_SESSION['warning']) ) unset($_SESSION['warning']);
		if ( isset($_SESSION['error']) ) unset($_SESSION['error']);
		if ( isset($_SESSION['success']) ) unset($_SESSION['success']);
		
		if ( $sessionvars ) {
			foreach ($sessionvars as $key => $val){
				$_SESSION[$key] = $val;
			}
		}
		
		header("Location: " . $url);
		exit;
	}
	
	/**
	 * forwards to a new module and/or action
	 *
	 * @param string $path
	 * @param array $arguments
	 */
	protected function forward($path = "index", array $arguments = null){
		
		/**
		 * split path on / for module and action
		 */
		$paths = explode("/", $path);
		if(!$arguments){
			$arguments = $this->arguments;
		}
        
		switch(count($paths)){
			case 3:
				$GLOBALS['system'] = $paths[0];
				$GLOBALS['module'] = $paths[1];
				$GLOBALS['action'] = $paths[2];
				break;
			case 2: 
				$GLOBALS['module'] = $paths[0];
				$GLOBALS['action'] = $paths[1];
				break;
			case 1:
				if( method_exists($this, $paths[0]) ){
					$GLOBALS['action'] = $paths[0];					
				} else {
					$GLOBALS['module'] = $paths[0];
					$GLOBALS['action'] = 'index';
				}
				break;
			default:
				throw new Exception("Invalid action and/or controller");
			break;
		}
        
		/**
		 * 
		 */
		require_once $GLOBALS['_CONFIG']['SHAPEROOT'] . '/systems/' . $GLOBALS['system'] . '/modules/' . $GLOBALS['module'] . '/' . strtolower($GLOBALS['module']) . '_controller.php';
		$_classname = ucfirst($GLOBALS['module']) . "Controller";
		$arguments = $arguments ? $arguments : get_object_vars($this->arguments);
		$_class = new $_classname((array)$arguments);
		$_class->{$GLOBALS['action']}();
		$GLOBALS['viewclass'] = $_class;
	}
	
	function __destruct(){

	}

	function argument_verify($argument){
	    if ( $this->argument_set($argument) && $this->arguments->$argument ){
		return TRUE;
	    } else {
		return FALSE;
	    }
	}

	function argument_set($argument){
	    if ( isset ( $this->arguments->$argument ) ){
		return TRUE;
	    } else {
		return FALSE;
	    }
	}
}
