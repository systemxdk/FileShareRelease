<?php


class shDatepicker extends shHelper {
	
	static public $called = false;
	/**
	 * id (and name) of input field
	 *
	 * @var string
	 */
	private $id;
	
	/**
	 * selected date
	 *
	 * @var string
	 */
	private $selectedDate;
	
	/**
	 * array of properties for this input field
	 *
	 * @var array
	 */
	private $htmlprops;
	
	/**
	 * constructor
	 *
	 * @param string $id
	 * @param string $selectedDate
	 * @param array $htmlprops
	 */
	function __construct($id, $selectedDate = NULL,array $htmlprops = array()){
		$this->id = $id;
		$this->selectedDate = $selectedDate;
		$this->htmlprops = $htmlprops;
		if(!self::$called){
			$this->include_css_and_js();
			self::$called = true;
		}
	}
	
	private function include_css_and_js(){
		
		/*
		ob_start();
		?>
		
		<link rel="stylesheet" href="<?php echo PUBLIC_DIR; ?>/styles/DatePicker.css" type="text/css" media="screen" title="(Default)" />
		
		<?php
		$GLOBALS['css'] .= ob_get_clean();	
		
		
		// Logic
		ob_start();
		?>
		<script src="<?php echo PUBLIC_DIR; ?>/javascript/DatePicker.js" type="text/javascript"></script>
		<script type="text/javascript">
			window.addEvent('domready', function(){
				$$('input.DatePicker').each( function(el){
					new DatePicker(el);
				});
			});
		</script>
		
		<?
		
		$GLOBALS['jscripts'] .= ob_get_clean();	
		
		*/
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
			<input id='<?php echo $this->id; ?>' class='DatePicker' type='text' name='<?php echo $this->id; ?>' value='<?php echo $this->selectedDate; ?>' <?php echo $htmlprops; ?>  />
			<script type="text/javascript">	$(function() { $('#<?=$this->id?>').datepicker({}); });</script>
		<?
		
		return ob_get_clean();
	}
	
    
    /**
     * return true if all rule comparing observes
     * 
     * @param date $start_date
     * @param date $end_date
     * @param time $start_time
     * @param time $end_time
     * @param int $day_bitmask
     * @return bool
     */
    function is_date_rule_valid($start_date = NULL, $end_date = NULL, $start_time = NULL, $end_time = NULL, $day_bitmask){
        $day_index = date('N', strtotime(date('Y-m-d'))) - 1;
        if(
            (self::is_larger_than(date('Y-m-d'),$start_date) || $start_date == date('Y-m-d') || $start_date == NULL) &&
            (self::is_larger_than($end_date,date('Y-m-d')) || $end_date == NULL) &&
            (self::is_larger_than(date('H:i:s'),$start_time) || $start_time == date('H:i:s') || $start_time == NULL) &&
            (self::is_larger_than($end_time,date('H:i:s')) || $end_time == NULL) &&
            (pow(2,($day_index)) & $day_bitmask)
        ){
            return true;
        }
        return false;
    }
    
    /**
     * return true if time1 is larger than time2
     * 
     * @param date time $time1
     * @param date time $time2
     * @return bool
     */
    private function is_larger_than($time1,$time2){
        $uts['start'] = strtotime($time1);
        $uts['end'] = strtotime($time2);
        if( $uts['start']!==-1 && $uts['end']!==-1 ) {
            if( $uts['start'] >= $uts['end'] ) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
}