<?php

class shJson extends shHelper {
	
	/**
	 * 
	 * UTF8 and JSON encode all elements in array
	 * @param $input
	 * @return array
	 */
	static public function prepare_json_object($input) {
		$return = array();
		if (is_array($input)) {
			foreach ($input as $key => $val) {
				if( is_array($val) ) {
					$return[$key] = self::prepare_json_object($val);
				} else {
					$return[$key] = utf8_encode($val);
				}
			}
			return json_encode($return);
		} else {
			return json_encode(utf8_encode($input));
		}
	}
	
}