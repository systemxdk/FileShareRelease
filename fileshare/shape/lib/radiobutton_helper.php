<?php


class shRadiobutton extends shHelper {
	
	/**
	 * name of input field
	 *
	 * @var string
	 */
	private $name;
	
	/**
	 * id (and name) of input field
	 *
	 * @var string
	 */
	private $id;	
	
	/**
	 * selected?
	 *
	 * @var bool
	 */
	private $checked;
	
	/**
	 * value of button
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
	 * @param string $name
	 * @param string $value
	 * @param string $id
	 * @param bool $checked
	 * @param array $htmlprops
	 */
	function __construct($name, $value, $id = NULL, $checked = FALSE, array $htmlprops = array()){
		$this->name = $name;
		$this->value = $value;
		$this->id = $id;
		$this->checked = $checked;
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

			<input <?php echo $this->checked ? 'checked="checked"' : ''; ?> <?php echo $this->id ? 'id="' . $this->id . '"' : ''; ?> type='radio' name='<?php echo $this->name; ?>' value='<?php echo $this->value; ?>' <?php echo $htmlprops; ?>  />
		
		<?
		
		return ob_get_clean();
	}
	
}