<?php


class shTextfield extends shHelper {
	
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
     *
     * @var bool
     */
    private $password;

	/**
	 * constructor
	 *
	 * @param string $name
	 * @param string $id
	 * @param string $value
	 * @param array $htmlprops
	 */
	function __construct($name, $id = NULL, $value = NULL, array $htmlprops = array(), $password = FALSE ){
		$this->name = $name;
		$this->id = $id;
		$this->value = $value;
		$this->htmlprops = $htmlprops;
        $this->password = $password;
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
        $type = $this->password ? "type='password'" : "type='text'";
        ob_start();
		?>
			<input <?php echo $this->id ? 'id="' . $this->id . '"' : ''; ?> <?php echo $type; ?> name='<?php echo $this->name; ?>' value='<?php echo $this->value; ?>' <?php echo $htmlprops; ?>  />
		<?
		
		return ob_get_clean();
	}
	
}