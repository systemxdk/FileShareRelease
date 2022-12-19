<?php


class shList extends shHelper {

	/**
	 *
	 * @var array
	 *
	 */
	private $listItems;

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
	 * constructor
	 *
	 * @param string $name
	 * @param string $id
	 * @param string $value
	 * @param array $htmlprops
	 */
	function __construct(array $listItems , array $ulhtmlprops = array(), array $lihtmlprops = array() ){
		$this->listItems = $listItems;
		$this->ulhtmlprops = $ulhtmlprops;
		$this->lihtmlprops = $lihtmlprops;
	}

	/**
	 * magic function, echoes output
	 *
	 */
	function __toString(){
		$ulhtmlprops = '';
		foreach ($this->ulhtmlprops as $key => $prop){
			$ulhtmlprops .= " " . $key . "='" . $prop . "'";
		}
		$lihtmlprops = '';
		foreach ($this->lihtmlprops as $key => $prop){
			$lihtmlprops .= " " . $key . "='" . $prop . "'";
		}
		ob_start();
		?>
			<ul <?= $ulhtmlprops; ?>>
				<?php foreach($this->listItems as $listKey => $listValue): ?>
					<li <?= $ulhtmlprops; ?>><?= $listValue; ?></li>
				<?php endforeach;?>
			</ul>
		<?

		return ob_get_clean();
	}

}