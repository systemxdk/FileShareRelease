<?php


class shSelectbox extends shHelper {
	
	/**
	 * css_class
	 *
	 * @var string
	 */
	private $css_class;
	
	/**
	 * onchange ex. javascript 
	 *
	 * @var string
	 */
	private $htmlProps;

	/**
	 * name
	 *
	 * @var string
	 */
	private $name;
	
	/**
	 * selected
	 *
	 * @var string
	 */
	private $selected;
	
	/**
	 * options in array
	 *
	 * @var array
	 */
	private $options = array();
	
	/**
	 * creates (and if echo'ed return an selectobox)
	 * Ex:
	 * 		new shSelectbox( array("option1" => "OPTION1", "option2" => "OPTION2"), 'test', NULL, 'javascript:alert("something")' )
	 *
	 * @param array $options
	 * @param string $name
	 * @param string $css_class
	 * @param array $htmlProps
	 */
	function __construct(array $options = array(), $name = NULL, $selected = NULL, $css_class = NULL, $htmlProps = array(), $startBlank = null){
		
		if($startBlank){
			$newOptions[0] = "";
			foreach($options as $key => $value){
				$newOptions[$key] = $value;
			}
			$options = $newOptions;
		}
 
		$this->options		= $options;
		$this->name			= $name;
		$this->selected		= $selected;
		$this->css_class	= $css_class;	
		$this->htmlProps 	= $htmlProps;
	}
	
	/**
	 * magic function
	 *
	 */
	function __toString(){
		$htmlProps			= $this->htmlProps;
		$htmlProps['class'] = $this->css_class;
		$htmlProps['name'] 	= $this->name;
		
		foreach($htmlProps as $key => $value){
			$htmlProps[$key] = "$key='$value'";
		}
		
		$options	= $this->options;
		$selected	= $this->selected;
		
		$view = $GLOBALS['_CONFIG']['SHAPEROOT'] . '/shape/view/selectbox_helper.php';
		ob_start();
		require $view;
		
		return ob_get_clean();
		
		
	}
		
}