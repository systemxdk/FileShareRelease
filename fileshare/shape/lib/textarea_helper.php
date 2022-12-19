<?php


class shTextarea extends shHelper {
	
	/**
	 * name of input field
	 *
	 * @var string
	 */
	private $name;
	
	/**
	 * columns
	 *
	 * @var int
	 */
	private $cols;
	
	/**
	 * rows
	 *
	 * @var int
	 */
	private $rows;
	
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
	 * @param unknown_type $name
	 * @param unknown_type $cols
	 * @param unknown_type $rows
	 * @param unknown_type $id
	 * @param unknown_type $value
	 * @param array $htmlprops
	 */
	function __construct($name, $cols = 10, $rows = 5, $id = NULL, $value = NULL, array $htmlprops = array()){
		$this->name = $name;
		$this->cols = $cols;
		$this->rows = $rows;
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
<textarea cols="<?php echo $this->cols; ?>" rows="<?php echo $this->rows; ?>" <?php echo $this->id ? 'id="' . $this->id . '"' : ''; ?> type='text' name='<?php echo $this->name; ?>' <?php echo $htmlprops; ?>><?php echo $this->value; ?></textarea>
		<?
		
		return ob_get_clean();
	}
	
}