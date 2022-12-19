<?php


class shSubmitbutton extends shHelper {

	
	/**
	 * id (and name) of input field
	 *
	 * @var string
	 */
	private $id;	
	
	/**
	 * value of textfield
	 *
	 * @var string
	 */
	private $value;
	
	/**
	 * array of properties for this input field
	 *
	 * @var array
	 */
	private $htmlprops;
	
	/**
	 * constructor
	 *
	 * @param string $value
	 * @param string $id
	 * @param array $htmlprops
	 */
	function __construct($value = 'Submit', $id = NULL, array $htmlprops = array()){
		$this->id = $id;
		$this->value = $value;
		$this->htmlprops = $htmlprops;
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

			<input <?php echo $this->id ? 'id="' . $this->id . '"' : ''; ?> type='submit' name='<?php echo $this->id; ?>' value='<?php echo $this->value; ?>' <?php echo $htmlprops; ?>  />
		
		<?
		
		return ob_get_clean();
	}
	
}