<?php


class shCheckbox extends shHelper {
	
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
	 * array of properties for this input field
	 *
	 * @var array
	 */
	private $htmlprops;
	
	/**
	 * Constructor
	 *
	 * @param string $name
	 * @param string $id
	 * @param bool $selected
	 * @param array $htmlprops
	 */
	function __construct($name, $id = NULL, $checked = FALSE, array $htmlprops = array()){
		$this->name = $name;
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

			<input <?php echo $this->checked ? 'checked="checked"' : ''; ?> <?php echo $this->id ? 'id="' . $this->id . '"' : ''; ?> type='checkbox' name='<?php echo $this->name; ?>'  <?php echo $htmlprops; ?>  />
		
		<?
		
		return ob_get_clean();
	}
	
}