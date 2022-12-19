<?php

abstract class shWebserviceController extends shController {

	function __construct(array $arguments = array()){
		
		$GLOBALS['_SHAPE_WEBSERVICE'] = TRUE;
		parent::__construct($arguments);
		
		$this->className = 'Webservice';
	}

	function index(){
		ob_end_clean();
		$wsdl = $GLOBALS['_CONFIG']['SHAPEROOT'] . '/systems/' . $GLOBALS['system'] . '/modules/' . $GLOBALS['module'] . '/includes/webservice.wsdl';
		foreach(array_keys($_GET) as $key){
			if(strcasecmp("wsdl", $key) == 0){
				header("content-type: text/xml");
				$xml = simplexml_load_file($wsdl);
				$address = $xml->xpath("//soap:address");
				$address[0]['location'] = "http://" . $_SERVER['HTTP_HOST'] . PUBLIC_DIR . '/' . $GLOBALS['system'] . '/' . $GLOBALS['module'];
				echo $xml->asXML();   
				exit;
			}
		}
		if($_SERVER["REQUEST_METHOD"] == "POST") {
			ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
			$server = new SoapServer($wsdl);
			$server->setClass($GLOBALS['module'] . '_webservice');
			$server->handle();
		}
	}	
	
}