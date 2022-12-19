<?php

class shAddressPicker extends shHelper {

	/**
	 * array of properties for this button
	 * @var array
	 */
	private $htmlprops;

	/**
	 * array of popup window properties
	 * @var array
	 */
	private $popupprops;
	
	/**
	 * content text
	 * @var string
	 */
	private $name;
	
	/**
	 * container value id
	 * @var string
	 */
	private $container_value_id;
	
	public function __construct($container_id, $caption, $callback = false, $window_name = null, array $popupprops = array(), array $htmlprops = array()){
		if ( !$container_id ) throw new Exception("Missing container id");
		if ( !$caption ) throw new Exception("Missing caption");
		$this->window_name = $window_name !== null ? $window_name : "addressPickerWindow";
		$this->caption = $caption;
		$this->htmlprops = $htmlprops;
		$this->popupprops = $popupprops;
		$this->container_id = $container_id;
		$this->callback = $callback;
	}
	
	function __toString(){
		
		$htmlprops = '';
		foreach ($this->htmlprops as $key => $prop){
			$htmlprops .= " " . $key . "='" . $prop . "'";
		}
		
		$popupprops = '';
		foreach ($this->popupprops AS $key => $prop){
			$popupprops .= " " . $key . "=" . $prop . ",";
		}
		$popupprops = trim($popupprops, ", ");
		if ( !$popupprops ) $popupprops = 'width=415,height=375';
		
		
		$script = NULL;
		if ( !isset($GLOBALS['shape_address_picker_loaded'])) {
			$GLOBALS['shape_address_picker_loaded'] = TRUE;
		}
		
		ob_start();
		print "$script<button$htmlprops onclick='window.open(\"/address/picker/index?container=".$this->container_id."&callback=".(int)$this->callback."\", \"".$this->window_name."\",\"$popupprops\"); return false;'>{$this->caption}</button>";	
		return ob_get_clean();
	}
	
}