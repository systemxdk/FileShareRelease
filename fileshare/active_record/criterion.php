<?php

class Criterion {
	
	public $model;
	
	public $property;
	
	public $operator;
	
	public $value;
	
	function __construct($property = NULL,$value = NULL,$operator = '=',$model = NULL){
		$this->property	= $property;
		$this->value	= $value;
		$this->operator	= $operator;
		$this->model	= $model;
		
	}
	
	function __toString(){
		if ($this->model){
			$meta = $this->model->getProperties();
			$value = $this->meta[$this->property] ? $this->model->prepare_property($this->property,$this->value) : $this->value;
			return $this->property . ' ' .$this->operator . ' ' .$value;
		} else {
			return $this->property . ' ' .$this->operator . ' ' .$this->value;
		}
	}
}