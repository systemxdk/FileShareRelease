<?php


class shLink extends shHelper {
	
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
	private $url;

	/**
	 * parameters (for get)
	 *
	 * @var string
	 */
	private $parameters;
	
	/**
	 * Link text
	 *
	 * @var string
	 */
	private $text;

	/**
	 * add a link
	 *
	 * @param string $url
	 * @param string $text
	 * @param string $parameters
	 * @param string $css_class
	 * @param array $htmlProps
	 */
	function __construct($url, $text = NULL, $parameters = NULL, $css_class = NULL, array $htmlProps = array()){
		
		$this->url			= $url;
		$this->text			= $text;
		$this->css_class	= $css_class;
		$this->htmlProps 	= $htmlProps;
		$this->parameters	= $parameters;
		
		$this->text = $this->text ? $this->text : $url;
		$this->parameters = $this->parameters ? '?' . $this->parameters : '';
		
		$this->url = new shUrl($this->url);
	}
	
	/**
	 * magic function
	 *
	 */
	function __toString(){

		$htmlProps = $this->htmlProps;
		foreach($htmlProps as $key => $value){
			$htmlProps[$key] = "$key='$value'";
		}		
		
		return "<a href='" . $this->url . $this->parameters . "' " . implode(" ", $htmlProps) . ">" . $this->text . "</a>";
	}
		
}


