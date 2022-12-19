<?php

/**
 * abstract class for webservice classes (will be handled by the webservice controller)
 */
abstract class shWebserviceClass {
	
	protected $responseXml;
	
	function __construct()
	{
		$this->responseXml = @file_get_contents('php://input');
	}
	
}
