<?php

class shListbox extends shHelper {
	
	private $buttons = Array();
	private $footer_buttons = Array();
	private $db_identifier = NULL;
	private $header_class = NULL;
	private $row_class = NULL;
	private $sql_template = 'SELECT #SELECT# FROM #TABLE# a #JOINS# WHERE #WHERE# #GROUPBY# #ORDERBY#';
	private $sql_table = NULL;
	private $sql_select = NULL;
	private $sql_select_array = Array();
	private $sql_where = '1';
	private $sql_joins = Array();
	private $sql_orderby = NULL;
	private $sql_groupby = NULL;
	private $sql = NULL;
	private $pagination = NULL;
	private $pagination_break = NULL;
	private $pagination_break_after = 0;
	public $debug = FALSE;
	
	public function __construct($database = 'MYSQLDatabase'){
		
		// Check for active record existence
		if ( !class_exists('ActiveRecord')){
			throw new Exception('Active Record not present');
		}
		// Verify target database of the list
		$this->db_identifier = $database;
		if ( !class_exists($this->db_identifier)){
			throw new Exception('Target database not present');
		}
		
	}
	
	public function SetTable($table_name) {
		$this->sql_table = $table_name;
	}
	
	public function SetSelect(Array $select_arr = Array()){
		
		if ( $select_arr ) {
			$this->sql_select_array = $select_arr;
			foreach ( $select_arr AS $field => $header ) {
				if(strstr($field, '#')){
					if(isset($header['attributes']['field_name'])){
						$field = str_replace('#', '', $field) . $header['attributes']['field_name'];
					} else {
						$field = str_replace('#', '', $field);
					}
					$prefix = NULL;
				} else {
					$prefix = !strstr($field, '.') ? 'a.' : NULL;
				}
				$this->sql_select .= $prefix.$field . ', ';
			}
		}
		$this->sql_select = trim($this->sql_select, ', ');
	}
	
	public function SetWhere($where_clause = NULL){
		$this->sql_where = $where_clause;
	}
	
	public function SetJoins($join_clause){
		$this->sql_joins[] = $join_clause;
	}
	
	public function SetOrderBy($orderby_clause){
		$this->sql_orderby = ' ORDER BY '.$orderby_clause;
	}
	
	public function SetGroupBy($groupby_clause){
		$this->sql_groupby = ' GROUP BY '.$groupby_clause;
	}
	
	public function SetHeaderClass($class_name) {
		$this->header_class = $class_name;
	}
	
	public function SetRowClass($class_name) {
		$this->row_class = $class_name;
	}
	
	public function SetButton(Array $button){
		$this->buttons[] = $button;
	}
	
	public function SetButtonFooter(Array $button){
		$this->footer_buttons[] = $button;
	}
	
	public function SetPagination($limit){
		$this->pagination = $limit;
	}
	
	public function SetPaginationBreak($break){
		$this->pagination_break = (bool)$break;
	}
	
	public function SetPaginationBreakAfter($count){
		$this->pagination_break_after = $count;
	}
	
	public function draw(){
		echo $this->render();
	}
	
	public function Debug($state){
		$this->debug = $state;
	}
	
	public function render(){
		if ( !$this->sql_select ) {
			throw new Exception('Select statement not setup');
		}
		$db = $this->db_identifier;
		$dbconn = $db::connect();
		$this->sql = preg_replace("/#SELECT#/", $this->sql_select, $this->sql_template);
		$this->sql = preg_replace("/#TABLE#/", $this->sql_table, $this->sql);
		$this->sql = preg_replace("/#JOINS#/", implode("\n", $this->sql_joins), $this->sql);
		$this->sql = preg_replace("/#WHERE#/", $this->sql_where, $this->sql);
		if ( isset($_GET['field_name']) && isset($_GET['direction']) && $_GET['field_name'] && $_GET['direction'] ) {
			$this->sql = preg_replace("/#ORDERBY#/", 'ORDER BY '. $_GET['field_name'] .' '.$_GET['direction'], $this->sql);
		} else {
			$this->sql = preg_replace("/#ORDERBY#/", $this->sql_orderby, $this->sql);
		}
		$this->sql = preg_replace("/#GROUPBY#/", $this->sql_groupby, $this->sql);
		
		// Pagination
		$pagination_total_rows = 0;
		if ( $this->pagination && $this->pagination > 0 ){
			$pagination_total_rows = $dbconn->query($this->sql)->rowCount();
			if ( isset($_GET['offset']) ) {
				$this->sql .= ' LIMIT ' . (int)$_GET['offset'] . ', '.$this->pagination;
			} else {
				$this->sql .= ' LIMIT ' . $this->pagination;
			}
		}
		
		if ( $this->debug ) print "<pre>".$this->sql."</pre>";
		$res = $dbconn->query($this->sql);
		if ( !$res ){
			throw new Exception('SQL ERROR OCCURED: ' . $this->sql );
		}
		$rows = $res->fetchAll(PDO::FETCH_OBJ);
		
		// Build Header
		$class = isset($this->header_class) ? ' class="'.$this->header_class.'"' : NULL;
		$return = '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr>';
		
		if ( $this->buttons ){
			foreach ( $this->buttons AS $butt ) {
				if ( isset($butt['prepend']) && $butt['prepend'] ) $return .= '<td'.$class.'></td>';
			}
		}
		
		foreach ($this->sql_select_array AS $field_name => $properties ) {
			if ( isset($this->sql_select_array[$field_name]['hidden']) && $this->sql_select_array[$field_name]['hidden']) continue;
			$props = NULL;
			if ( isset($properties['attributes']) ) {
				foreach ( $properties['attributes'] AS $key => $value ) {
					if($key == 'field_name'){
						$field_name = $value;
					} else {
						$props .= ' '.$key . '="'.$value.'"';
					}
				}
				$props = rtrim($props);
			}
			$header = NULL;
			if ( isset($properties['header']) && trim($properties['header']) && $properties['header'] != '&nbsp;' ) {
				$header = '<a href="'.self::header_sort_link($field_name, $properties['header']).'" class="admin_header_sort_link">'.$properties['header'].'</a>';
			}
			$return .= '<td'.$props.$class.'>'.$header.'</td>';
		}
		
		if ( $this->buttons ){
			foreach ( $this->buttons AS $butt ) {
				if ( !isset($butt['prepend']) || !$butt['prepend'] ) $return .= '<td'.$class.'></td>';
			}
		}
		$return .= '</tr>';

		// Change field_name
		$local_select_array = array();
		foreach($this->sql_select_array as $key => $val){
			if(isset($this->sql_select_array[$key]['attributes']['field_name'])){
				$local_select_array[$this->sql_select_array[$key]['attributes']['field_name']] = $this->sql_select_array[$key];
			} else {
				$local_select_array[$key] = $this->sql_select_array[$key];
			}
		}
		$this->sql_select_array = $local_select_array;

		// Build Row Data
		if ( $rows ){
			foreach ( $rows AS $row ) {
				$return .= '<tr>';
				foreach ( $this->buttons AS $button ) {
					$return .= $this->render_button($button, $row, true);
				}
				foreach ( array_keys($this->sql_select_array) AS $key ) {
					if ( isset($this->sql_select_array[$key]['attributes']['hidden']) && $this->sql_select_array[$key]['attributes']['hidden']) continue;
					$class = isset($this->row_class) ? $this->row_class : NULL;
					$ext_class = isset($this->sql_select_array[$key]['ext_class']) ? $this->sql_select_array[$key]['ext_class'] : NULL;
					if ( strstr($key, " AS ") ){
						$key = substr($key, strpos($key, " AS ")+4, strlen($key));
					} else if ( strstr($key, ".") ){
						$key = substr($key, strpos($key, ".")+1, strlen($key));
					}
					if ( isset($this->sql_select_array[$key]['render']) && class_exists($this->sql_select_array[$key]['render']['class']) && method_exists($this->sql_select_array[$key]['render']['class'], $this->sql_select_array[$key]['render']['method']) ){
						$render_class = $this->sql_select_array[$key]['render']['class'];
						$render_method = $this->sql_select_array[$key]['render']['method'];
						$value = $render_class::$render_method($row, $row->$key);
					} else {
						$key = strstr($key, '#') ? str_replace('#', '', $key) : $key;
						$value = $row->{$key};
					}
					$return .= '<td class="'.$class.' '.$ext_class.'">'.$value.'</td>';
				}

				foreach ( $this->buttons AS $button ) {
					$return .= $this->render_button($button, $row);
				}
				$return .= '</tr>';
			}
		}
		$return .= '</table>';
		
		// 
		if ( $this->pagination && $this->pagination > 0 && $pagination_total_rows ) {
			if ( $this->pagination_break ) $return .= '<br />';
			$protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
			if ( strstr($_SERVER['REQUEST_URI'], '?') ) {
				$uri_bits = explode("?", $_SERVER['REQUEST_URI']);
				$pagination_uri = $protocol . $_SERVER['HTTP_HOST'] . $uri_bits[0];
			} else {
				$pagination_uri = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			}
			$return .= '<div align="center">';
			for ( $i = 0; $i <= floor($pagination_total_rows / $this->pagination); $i++ ) {
				$cur_i = $i+1;
				if ( $cur_i < 10 ) $cur_i = "0".$cur_i;
				if ( isset($_GET['offset']) && $_GET['offset'] == ($i*$this->pagination) ) {
					$return .= $cur_i.'&nbsp;';
				} else {
					$return .= '<a href="'.$pagination_uri.'?offset='.($i*$this->pagination).'">'.$cur_i.'</a>&nbsp;';
				}
				if ( $this->pagination_break_after && $cur_i % $this->pagination_break_after == 0 && $i > 0) $return .= "<br />";
			}
			$return .= '</div>';
			if ( $this->pagination_break ) $return .= '<br />';
		}
		
		// Footer buttons
		if ( $this->footer_buttons ) {
			$return .= '<div align="right">';
			foreach ( $this->footer_buttons AS $footer_button ) {
				$return .= '<a href="'.$footer_button['href'].'">'.$footer_button['caption'].'</a>';
			}
			$return .= '</div>';
		}
		return $return;
	}
	
	private function header_sort_link($field_name, $header) {
		$link = "";
		$uri_components = Array();
		$protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
		$uri = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REDIRECT_URL'];
		
		if ( !strstr($link, '?')) $link .= '?';
		$link .= 'field_name='.$field_name.'&';
		$link .= 'direction='.(isset($_GET['direction']) && $_GET['direction'] == 'asc' ? 'desc' : 'asc');
		return $link;
	}
	
	private function render_button($button, $row, $prepend = false){

		if ( $prepend === TRUE ) {
			if ( !isset($button['prepend']) || !$button['prepend']) return false;
		}
		if ( $prepend === FALSE ) {
			if ( isset($button['prepend']) && $button['prepend']) return false;
		}
		$width = isset($button['width']) ? 'width="'.$button['width'].'"' : NULL;
		$height = isset($button['height']) ? 'height="'.$button['height'].'"' : NULL;
		$class = isset($this->row_class) ? ' class="'.$this->row_class.'"' : NULL;
		$href = $button['href'];
		$style = isset($button['style']) ? 'style="'.$button['style'].'"' : NULL;
		foreach ($this->sql_select_array AS $field_name => $props) {
			if ( strstr($field_name, " AS ") ){
				$key = substr($field_name, strpos($field_name, " AS ")+4, strlen($field_name));
			} else {
				$key = $field_name;
			}
			
			if ( strstr($href, '{'.$key.'}') ){
				$href = str_replace('{'.$key.'}', $row->{$key}, $href);
			}
		}
		$rendered_button = '<a href="'.$href.'"><img title="'.$button['caption'].'" alt="'.$button['caption'].'" border="0" src="'.$button['image'].'" '.$width.' '.$height.' /></a>';
		if ( isset($button['render']) && $button['render'] ) {
			$render_class = $button['render']['class'];
			$render_method = $button['render']['method'];
			$value = $render_class::$render_method($row);
			if ( $value === FALSE ) $rendered_button = NULL;
		}
		return '<td'.$class.$width.$style.'>'.$rendered_button.'</td>';
	}
	
}	
