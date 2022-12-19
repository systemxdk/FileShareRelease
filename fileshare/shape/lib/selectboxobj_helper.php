<?php


class shSelectboxobj extends shSelectbox {

	/**
	 * creates (and if echo'ed return an selectobox)
	 * Ex:
	 * 		new shSelectbox( array("option1" => "OPTION1", "option2" => "OPTION2"), 'test', NULL, 'javascript:alert("something")' )
	 *
	 * @param array $options
	 * @param string $valueKey
	 * @param string $captionKey
	 * @param string $name
	 * @param integer $selected
	 * @param string $css_class
	 * @param array $htmlProps
	 */
	function __construct(array $options = array(), $valueKey, $captionKey, $name = NULL, $selected = NULL, $css_class = NULL, array $htmlProps = array(), $startBlank = null){

		$newOptions = array();

		if($startBlank){
			$newOptions[0] = $startBlank;
		}

		$this->glue = '';
		$this->captionKey = array($captionKey);
		if( is_array($captionKey) ){
			$this->glue = array_shift($captionKey);
			$this->captionKey = $captionKey;
		}

		foreach($options as $val){
			$display = array();
			foreach($this->captionKey as $captionKey){
				$display[] = $val->$captionKey;
			}
			$newOptions[$val->$valueKey] = implode($this->glue,$display);
				//error_log("DATA FOR SCH: ".implode($this->glue,$display));
		}
		parent::__construct($newOptions,$name, $selected, $css_class, $htmlProps);
	}
}
