<?php

class shRestclient {

    /**
     * Implementation of a restful query
     *
     * Example:
     *
     *
     *
     * @param string $url
     * @param mixed $params
     * @param string $verb
     * @param string $optional_headers
     * @param string $format
     * @param array $basic_auth
     * @throws Exception
     * @return mixed
     */
    static function Call($url, $params = null, $verb = 'GET', $optional_headers = null, $format = 'json', array $basic_auth = array()) {
	/*
	 * Headers
	 */
	$cparams = array(
	    'http' => array(
		'method' => $verb,
		'ignore_errors' => true,
		'header' => $optional_headers !== null ? $optional_headers : null,
	    )
	);

	if ($basic_auth && !preg_match("/Authorization: Basic/", $optional_headers)) { // set basic auth if its not defined in custom headers
	    $optional_headers .= sprintf('Authorization: Basic %s', base64_encode($basic_auth['username'] . ':' . $basic_auth['password']));
	}

	if ($optional_headers !== null) {
	    $cparams['http']['header'] = $optional_headers;
	}
	if ($params !== null) {
//error_log( json_encode($params) );
	    if ($verb == 'POST') {
//error_log( $params );
		$cparams['http']['content'] = json_encode($params);
	    } else {
	    	$params = http_build_query($params);
		$url .= '?' . $params;
	    }
	}
//error_log(print_r($cparams['http'],1)); 
	$http_response_header = array();
	$context = stream_context_create($cparams);
//error_log($url); 
	$fp = fopen($url, 'rb', false, $context);
	if (!$fp) {
		//error_log("could not open filehandle");
	    $res = false;
	} else {

	    // If you're trying to troubleshoot problems, try uncommenting the
	    // next two lines; it will show you the HTTP response headers across
	    // all the redirects:
//	    $meta = stream_get_meta_data($fp);
//	    var_dump($meta['wrapper_data']);
	    $res = stream_get_contents($fp);
	}
//exit;
	if ($res === false) {
	    throw new Exception("$verb $url failed: $php_errormsg");
	}

	switch ($format) {
	    case 'json':
		$r = json_decode($res, true);
		if ($r === null) {
		    throw new Exception("failed to decode $res as json");
		}
		$res = $r;
		break;
	    case 'xml':
		$return_value = simplexml_load_string($res);
		if ($return_value === null) {
		    throw new Exception("failed to decode $res as xml");
		}
		$res = $return_value;
		break;
	    default:
		break;
	}
	$response = new stdClass();
	$response->response = $res;
	$response->http_code = $http_response_header[0];
	return $response;
    }

}
