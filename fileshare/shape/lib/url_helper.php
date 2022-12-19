<?php
class shUrl extends shHelper {
	function __construct($url, $getVars = null){
		
        if($url instanceof shUrl){
            ob_start();
            echo $url;
            $url = ob_get_clean();
        }
		
		$this->url = $url;
		$this->queryString = "";
		
		/**
		 * if no / url, we define if it is an action in this module, or a module name
		 */
		if( !preg_match("/\//",$this->url) ){
			$this->url = $GLOBALS['system'] . '/' . $GLOBALS['module'] . '/' . $this->url;
		    /**
             * Else if two parameters, we set current system as system
             */
        } else if( count( explode('/', $this->url) ) == 2 ){
            $this->url = $GLOBALS['system'] . '/' . $this->url;
        }

		
		/**
		 * hack in case public dir is not project public folder
		 */
		if( !preg_match('/^http:/',$this->url) ){
			$prepend = dirname($_SERVER['PHP_SELF']) == '/' ? '' : dirname($_SERVER['PHP_SELF']);
			$this->url = $prepend . '/' . $this->url;
		}		
		
		if(is_array($getVars)){
			foreach($getVars as $key => $value){
				if(!is_numeric($key)){
					$params[] = "$key=$value";
				}
			}
			if(count($params) > 0){
				$this->queryString = "?";
			}
			foreach($params as $param){
				$this->queryString .= $param;
			}
			
		}
	}
	
	function __toString(){
		return $this->url . $this->queryString;
	}
}