<?php


class shTimepicker extends shHelper {
	
	static public $called = false;
	/**
	 * id (and name) of input field
	 *
	 * @var string
	 */
	private $id;
	
	/**
	 * selected time
	 *
	 * @var string
	 */
	private $selectedTime;
	
	/**
	 * array of properties for this input field
	 *
	 * @var array
	 */
	private $htmlprops;
	
	/**
	 * constructor
	 *
	 * @param string $id
	 * @param string $selectedTime
	 * @param array $htmlprops
	 */
	function __construct($id, $selectedTime = NULL,array $htmlprops = array()){
		$this->id = $id;
		$this->selectedTime = $selectedTime;
		$this->htmlprops = $htmlprops;
		if(!self::$called){
			$this->include_css_and_js();
			self::$called = true;
		}
	}
	
	private function include_css_and_js(){
		
	}
	
	/**
	 * magic function, echoes output
	 *
	 */
	function __toString(){
	
		$htmlprops = '';
		foreach ($this->htmlprops as $key => $prop){
			$htmlprops .= " " . $key . "='" . $prop . "'";
		}		
		ob_start();
		?>
			<input id='<?php echo $this->id; ?>' class='TimePicker' type='text' name='<?php echo $this->id; ?>' value='<?php echo $this->selectedTime; ?>' <?php echo $htmlprops; ?>  />
			<script type="text/javascript">	$(function() { $('#<?=$this->id?>').timepicker({}); });</script>
		<?
		
		return ob_get_clean();
	}
	
}