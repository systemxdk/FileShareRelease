<?php

require_once('_helper.php');

/**
 * shInfoTxt
 */
class shInfotxt extends shHelper {

    /**
     *
     * @var string
     */
	private $sessionName;

    /**
     *
     * @var string
     */
	private $class;

    /**
     *
     * @param string $sessionName
     * @param <type> $class
     */
	function __construct($sessionName = 'infotxt', $class = NULL){
		$this->sessionName	= $sessionName;
		$this->txt			= $_SESSION[$this->sessionName] ? $_SESSION[$this->sessionName] : null;
		$this->class		= $class ? 'class="' . $class . '"' : 'style="border: solid #000000;"';
	}
	
	/**
	 * magic function
	 *
	 */
	function __toString(){
		
		// Logic
	
		ob_start();
		
		if( $this->txt ){
			$addStyle	= '';
			unset($_SESSION[$this->sessionName]);
		} else {
			$addStyle = 'style="display: none"';
		}
		?>

			<div id='<?= $this->sessionName; ?>' <?=$this->class ?> <?= $addStyle; ?> >
			<?= $this->txt ?>
			</div>
		
		<?
		return ob_get_clean();
	}
	
}