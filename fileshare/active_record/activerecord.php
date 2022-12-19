<?php

/*
* 	Base class for uteeni active record
* 	Developed by Kristian Nissen and Michael Als @ eteneo ApS
*/

require_once dirname(__FILE__) . '/sql_syntaxor.php';
require_once dirname(__FILE__) . '/criterion.php';
require_once dirname(__FILE__) . '/criteria.php';

class ActiveRecord {
	protected $table_name;
	protected $database; 
	protected $properties;
	public $unmodified_properties;
	public $raw;
	protected $meta;
	protected $associations;
	static public $db;
        protected $affected_rows = null;

	function __construct(){
	}

	function __set($name, $value){
		if(in_array($name, array_keys($this->properties))){
			$this->properties[$name] = $this->validate_data($name, $value);
		}elseif($this->associations[$name]){
			$this->properties[$name] = $value;
		}
		else{
			throw new Exception("$name not valid field for ". get_class($this));
		}
	}

	function __get($name){
		if(in_array($name, array_keys($this->properties))){
			return $this->properties[$name];
		}
		else if (isset($this->associations[$name])){
		    return $this->fetch_assoc($name);
		} else {
		    return null;
		}
		
	}
	
	function __call($function, $args){
		if(stripos($function, 'find_by_') !== false){
			$method_name = substr($function, 8);
			return $this->find_by_property($method_name, $args[0]);
		}
		elseif(stripos($function, 'find_all_by_') !== false){
			$method_name = substr($function, 12);
			return $this->find_all_by_property($method_name, $args[0]);
		}
		else if (isset($this->associations[$function])){
		    return $this->fetch_assoc($function, $args[0]);
		}
		else{
		    throw new Exception("Call to undefined method $function in class: '" . get_class($this) . "'");
		}
	}

	private function fetch_assoc($name,array $parms = array() ){
		
	    if($ass = $this->associations[$name]){
			$extra_conditions = isset($ass['conditions']) ? " AND " . $ass['conditions'] : "";
			$second_class_condition = "";
			if( isset($ass['second_class_property']) && isset($ass['second_local_property']) ){
				$second_class_condition = " AND " . $ass['second_class_property'] . " = " . $this->prepare_property($ass['second_local_property']);
			}
			
			switch($ass['ass_type'])
			{
			    case 'has_many':
				$optionsArray = array(
				    "TABLE" => $ass['class'],
				    "WHERE" => $ass['class_property'] . " = " . $this->prepare_property($ass['local_property'])	. $extra_conditions . $second_class_condition,
				    "OFFSET" => 0
				);
				break;
			    case 'belongs_to':
				$optionsArray = array(
				    "TABLE" => $ass['class'],
				    "WHERE" => $ass['class_property'] . " = " . $this->prepare_property($ass['local_property']) . $extra_conditions . $second_class_condition,
				    "LIMIT" => 1,
				    "OFFSET" => 0
				);
				break;
			    case 'has_one':
				$optionsArray = array(
				    "TABLE" => $ass['class'],
				    "WHERE" => $ass['class_property'] . " = " . $this->prepare_property($ass['local_property']) . $extra_conditions . $second_class_condition,
				    "LIMIT" => 1,
				    "OFFSET" => 0
				);
				break;
			    case 'has_and_belongs_to_many':
				$optionsArray = array(
				    "TABLE" => $this->table_name . " t2 ",
				    "JOINS" => "LEFT JOIN " . $ass['join_table'] . " t3 ON t2." . $ass['local_property'] . " = t3." . $ass['join_local_property']
							. " LEFT JOIN " . $ass['class'] . " t1 ON t3." . $ass['join_class_property'] . " = t1." . $ass['class_property'],
				    "WHERE" => "t2.". $ass['local_property'] ." = ". $this->prepare_property($ass['local_property']) . $extra_conditions,
				    "OFFSET" => 0,
				    "SELECT" => "t1.*"
				);
				break;
			    default:
				return null;
			}
			foreach ( $parms as $parm => $value ){
			    $optionsArray[$parm] = $value;
			}
	    }
	    else{
	    	return null;
	    }
	    $db = Database::connect($this->database);
	    $sql = SQLSyntaxor::getSelectSQL($optionsArray, $db->getAttribute(PDO::ATTR_DRIVER_NAME));
	    $result = $db->query($sql);
	    //var_dump($sql);
	    if(!$result || ($result->rowCount() == 0 && in_array($ass['ass_type'], array("has_one, belongs_to")))){
	    	return null;
	    }else{
	    	$tmparr = array();
	    }
	    while($row = $result->fetch(PDO::FETCH_ASSOC))
	    {
		    // Determine if we get something useful
			if($row[$ass['class_property']] == null){
			    continue;
			}
			$className = str_replace("_", "", $ass['class']);
			$tmp = new $className;
			$tmp->hydrate($row);
			if(in_array($ass['ass_type'], array("has_one", "belongs_to"))){
			    $tmparr = $tmp;
			    break;
			}
			$tmparr[] = $tmp;
	    }
	    if(!$tmparr){
	    	return null;
	    }
	    $this->properties[$name] = $tmparr;
	    if($tmparr && $this->unmodified_properties !== null){
	    	$this->unmodified_properties[$name] = $tmparr;
	    }

	    return $tmparr;
	}

	protected function find_by_property($name, $value){
		$optionsArray = array(
							"TABLE" => $this->table_name, 
							"WHERE" => "$name = " . $this->prepare_property($name, $value),
							"LIMIT" => 1,
							"OFFSET" => 0	
		);
		$db = Database::connect($this->database);
		$sql = SQLSyntaxor::getSelectSQL($optionsArray, $db->getAttribute(PDO::ATTR_DRIVER_NAME));
		$result = $db->query($sql);
		if(!$result){
			return false;
		}

		if(false === ($row = $result->fetch())){
			return false;
		}
		$this->hydrate($row);
		return true;
	}
	
	protected function find_all_by_property($name, $value){
		$sql =  "SELECT * FROM $this->table_name WHERE $name = ". $this->prepare_property($name, $value);
		$db = Database::connect($this->database);
		$result = $db->query($sql);
		$arr = array();
		if(!$result){
			return false;
		}
		while($row = $result->fetch())
		{
			$tmp = new $this();
			$tmp->hydrate($row);
			$arr[] = $tmp;
		}
		return $arr;
	}
	
	function prepare_property($name, $value = null){
	    $db = Database::connect($this->database);
	    $value = is_null($value) ? $this->properties[$name] : $value;
	    if(is_null($value)){
		return "NULL";
	    }

	    if ( is_array($value) ){
		if ( $value['value'] && $value['method'] ){
		    return $value['value'];
		} else {
		    throw new Exception('Cannot set array as value');
		}
	    }

	    switch($this->meta[$name]['type'])
	    {
		case 'datetime':
		case 'time':
		case 'timestamp':
		case 'date':
		    if(strtolower($value) != "now()" && !preg_match("/^to_date\(/",$value))
		    {
			$value = $db->quote($value);
		    }
		    break;
		case 'string':
		case 'blob':
		case 'text':
		case 'enum':
		    if(isset($this->meta[$name]['sprintf'])){
			$value = sprintf($this->meta[$name]['sprintf'], $value);
		    }
		    $value = $db->quote($value);
		    break;
		case 'integer':
		    $value = intval($value);
		    break;
		case 'double':
		case 'float':
		    $value = floatval($value);
		    break;
		default:
		    break;
	    }
	    return $value;
	}

        /**
         *
         * @param string $where
         * @param int $limit
         * @param int $limit_start
         * @param string $order_by_field
         * @param string $order
         * @param string $joins
         * @param string $select
         * @return array
         */
	function find_all($where = null, $limit = null, $limit_start = 0, $order_by_field = '', $order = 'ASC', $joins = "", $select = ""){
		
		$optionsArray = array("TABLE" => $this->table_name);			
		if($where){
			switch ( strtolower( $where ) ){
				case 'criteria':
					$where->setmodel($this);
					break;
				case 'arcriterion':
				case 'criterion':
					$where->model = $this;
					break;
			}
			$optionsArray['WHERE'] 		= $where;
		}
		if($order_by_field){
			$optionsArray['ORDERFIELD'] = $order_by_field;
			$optionsArray['ORDERTYPE'] 	= $order;
		}
		if($limit){
			$limit_start = is_numeric($limit_start) ? $limit_start : 0;
			$limit = is_numeric($limit) ? $limit : 0;
			if($limit > 0){
				$optionsArray['LIMIT'] 	= $limit;
				$optionsArray['OFFSET'] = $limit_start;
			}
		}
		/**
		 * TODO: Make more advanced join features. 
		 */
		if($joins){
			$optionsArray['JOINS']	= $joins;
		}
		if($select){
			$optionsArray['SELECT']	= $select;
		}
		$db = Database::connect($this->database);
		$sql = SQLSyntaxor::getSelectSQL($optionsArray, $db->getAttribute(PDO::ATTR_DRIVER_NAME));
		//print $sql."<br />";
		$result = $db->query($sql);
		$arr = array();
		if($result){
			while($row = $result->fetch(PDO::FETCH_ASSOC))
			{
				$tmp = new $this();
				$tmp->hydrate($row);
				$arr[] = $tmp;
			}
		}
		return $arr;
	}

        /**
         *
         * @param string $where
         * @return int
         */
	function select_count($where = null){
		$sql = "SELECT count(*) as myCount FROM $this->table_name";
		if($where){
			$sql .= " WHERE $where ";
		}
		$db = Database::connect($this->database);
		$result = $db->query($sql);
		$arr = array();
		if($row = $result->fetch(PDO::FETCH_NUM)){
			return $row[0];
		}
		return false;
				
	}
	
	function find_by_sql($sql){
		if(stripos($sql, "select") !== 0 || preg_match("/\b(update|delete|insert)\b/i", $sql)){
			throw new Exception("Only Select statements are allowed in find_by_sql!");
		}
		
		$db = Database::connect($this->database);
		$result = $db->query($sql);
		if(!$result){
			$error = $db->errorInfo();
			trigger_error($error[2] . ": $sql");
			return false;
		}
		$return_val = array(); 
		while($row = $result->fetchObject()){
			$return_val[] = $row;
		}
		return $return_val;
		
		 

	}
	
	function is_new(){
		if($this->properties[$this->find_primary()])
			return false;
		else
			return true;
	}
	
	function create($include_assoc = true){
		$sql = "INSERT INTO $this->table_name ";
		$sql_fields = array();
		$sql_values = array();

		foreach($this->find_timestamps() as $timestamp){
                        if($this->unmodified_properties[$timestamp] == $this->properties[$timestamp]){
                            $this->$timestamp = "now()";
                        }
		}

		foreach($this->properties as $key => $value){
			if(($this->meta[$key]['required'] == false || $this->meta[$key]['extra'] == 'auto_increment' || $this->meta[$key]['default'] != '')  && ( isset($this->properties[$key]) === false || $this->properties[$key] === "NULL") )
			{
				continue;
			}
			elseif(is_array($value) && !isset($this->meta[$key])){
				continue;
			}
			elseif($this->properties[$key] === null || $this->properties[$key] === "NULL"){
				throw new Exception("$key is required  for table . {$this->table_name}!");
			}

			if (!isset($this->meta[$key])){
			    continue;
			}
			array_push($sql_fields, "$key");
			array_push($sql_values, $this->prepare_property($key));		
		}

		$sql .= "(". join(", ", $sql_fields).") VALUES (". join(", ", $sql_values).")";
		$fields = join(", ", $sql_fields);
		$values = join(", ", $sql_values);
		$optionsArray = array(
						"TABLE" => $this->table_name,
						"FIELDS" => $fields,
						"VALUES" => $values
		);
		$conn = Database::connect($this->database);
		$sql = SQLSyntaxor::getCreateSQL($optionsArray, $conn->getAttribute(PDO::ATTR_DRIVER_NAME));
		//die($sql);
		$result = $conn->query($sql);
		
		if($result && $this->find_primary())
		{
			$pri = $this->find_primary();
			if($this->$pri === null){
				$this->$pri = $this->getLastInsertID($conn);
            }
			$this->find($this->$pri);
		}
		elseif(!$result){
			$errorInfo = $conn->errorInfo();
			throw new Exception("Failed to create the row in database - " . $errorInfo[2] . " - $sql");
		}
		if($include_assoc){
			$this->update_assoc();
		}
	}
	
	function read($where = null, $limit = null, $limit_start = 0, $order_by_field = '', $order = 'ASC'){
		return $this->find_all($where, $limit, $limit_start, $order_by_field, $order);
	}
	
	function find($value){	
	
		$primary_key = $this->find_primary();
		$value = $this->prepare_property($primary_key, $value);
		$optionsArray = array(
							"TABLE" => $this->table_name, 
							"WHERE" => "$primary_key = " .  $value,
							"LIMIT" => 1,
							"OFFSET" => 0	
		);		
		$db = Database::connect($this->database);
		$sql = SQLSyntaxor::getSelectSQL($optionsArray, $db->getAttribute(PDO::ATTR_DRIVER_NAME));
		if(!$result = $db->query($sql)){
			error_log('could not execute query on table '.$this->table_name.', (query: '.$sql.')');
			return false;
		}
		$row = $result->fetch();
		
		if(!$row){
			return false;
		}
		$this->hydrate($row);
		return true;
		
	}
	
	function update($include_assoc = true, $guess = false, $extra_cond = ''){
		if(!$this->is_dirty()){
			return true;
		}
		$sql_values = array();
                foreach($this->find_timestamps() as $key => $timestamp){
                        if($this->unmodified_properties[$timestamp] == $this->properties[$timestamp] && $key == "update"){
                            $this->$timestamp = "now()";
                        }
		}
		foreach($this->dirty_fields() as $key => $value){
			if($this->meta[$key]['required'] == true && ($value === null || $value === "NULL")){
				throw new Exception("$key is required for table . {$this->table_name}");
			}
			if(false !== $this->prepare_property($key)){
				
				array_push($sql_values, "$key = ". $this->prepare_property($key));
			}
		}
		if($sql_values){
			$values = join(", ", $sql_values);
                        if($this->find_primaries()){
                            $where = array();
                            foreach($this->find_primaries() as $primary){
                               if(!isset($this->properties[$primary]) || !isset($this->unmodified_properties[$primary])){
                                   throw new Exception("Primary field {$primary} cannot be empty on UPDATE");
                               }
                               $where[] = "$primary" . ' = ' . $this->prepare_property($primary, $this->unmodified_properties[$primary]);
                            }
                            $where = join(" and ", $where);
                        }
			elseif(count($this->unmodified_properties) > 0 && $guess){
				$sql_values = array();
				foreach($this->unmodified_properties as $key => $value){
					if($value){
						array_push($sql_values, "$key = " . $this->prepare_property($key,$value));
					}
				}
				$where = join(' and ' , $sql_values);
			}
			else{
				throw new Exception("Cannot update object - primary key or unmodified_properties unknown."); 
			}
			if ($extra_cond != '') $where .= ' '.$extra_cond;
			$optionsArray = array("TABLE" => $this->table_name, "WHERE" => $where, "VALUES" => $values);
			$db = Database::connect($this->database);
			$sql = SQLSyntaxor::getUpdateSQL($optionsArray, $db->getAttribute(PDO::ATTR_DRIVER_NAME));
			$result = $db->query($sql);
			if(!$result){
				throw new Exception(print_r($db->errorInfo(),1) . $sql . "\n"); 
			}
                        $this->affected_rows = $result->rowCount();
			
		}
		if($include_assoc){
			$this->update_assoc();
		}
		$this->unmodified_properties = $this->properties;
	}
	
	function save($include_assoc = true){
	
		if(($this->find_primary() && $this->properties[$this->find_primary()]) || $this->unmodified_properties)
		{
			return $this->update($include_assoc);
		} 
		else
			return $this->create($include_assoc);
	}
	
	function destroy($guess = false){
		if($this->find_primaries()){
                        $where = array();
                        foreach($this->find_primaries() as $primary){
                           if(!isset($this->properties[$primary])){
                               throw new Exception("Primary field {$primary} cannot be empty on DELETE");
                           }
                           $where[] = $primary . ' = ' . $this->prepare_property($primary);
                        }
                        $where = join(" and ", $where);
		}elseif($guess){
			$sql_values = array();
			foreach($this->unmodified_properties as $key => $value){
				array_push($sql_values, $key. "=" . $this->prepare_property($key,$value));
			}
			$where = join(" and ", $sql_values);
		}
		else{
			throw new Exception('Need primary key or $guess in order to destroy');
		}
		$optionsArray = array(
					"TABLE" => $this->table_name,
					"WHERE" => $where
		);
		$db = Database::connect($this->database);
		$sql = SQLSyntaxor::getDestroySQL($optionsArray, $db->getAttribute(PDO::ATTR_DRIVER_NAME));
		$result = $db->query($sql);
                if(!$result){
                        throw new Exception(print_r($db->errorInfo(),1) . $sql . "\n");
                }
                $this->affected_rows = $result->rowCount();
	}
	
	public function find_primary(){
		foreach($this->meta as $key => $value){
			if($value["primary"] === true)
				return $key;
		}
			return null;
	}

        public function find_primaries(){
                $primaries = array();
		foreach($this->meta as $key => $value){
			if($value["primary"] === true)
				$primaries[] = $key;
		}
			return $primaries;
        }
	
	protected function find_timestamps(){
		$timestamps = array();
		foreach($this->meta as $key => $value){
			if(isset($value["timestamp_update"]) && $value["timestamp_update"] === true)
				$timestamps['update'] = $key;
			if(isset($value["timestamp_create"]) && $value["timestamp_create"] === true)
				$timestamps['create'] = $key;
		}
			return $timestamps;
	}
	
	/*
	 * TODO: Review this function
	 */
	protected function validate_data($name, $value)
	{
                if(is_null($value)){
                    return NULL;
                }
		switch($this->meta[$name]['type'])
		{	
			case 'blob':
			case 'text':
			case 'date':
			case 'string':
				return $value;
				break;
			case 'integer':
				$intval = intval($value);
				if($intval == $value){
					return $intval;
				}
				break;
			case 'double':
			case 'float':
				//$doubleval = doubleval($value) ;
				$doubleval = $value ;
				if($doubleval == $value){
					return $doubleval;
				}				
				break;
			default:
				return $value;
				break;
		}
		throw new Exception("Invalid value for $name");
	}
	
	function getProperties(){
		return $this->meta;
	}
	
        function getValues(){
            return $this->properties;
        }

        function getNumberOfAffectedRows(){
            return $this->affected_rows;
        }

	function update_assoc()
	{	
	 $db = Database::connect($this->database);
	  if(!is_array($this->associations))
	    return;
		foreach($this->associations as $name => $assoc)
		{
			$assoc_val = isset($this->properties[$name]) ? $this->properties[$name] : null;
			if($assoc_val !== null)
			{
				/*
				 * First we update the associated objects. All objects that are loaded will be saved. 
				 * If the association is an array meaning: has_many or has_and_belongs_to_many
				 * it will loop through and save changes to each object. 
				 */
				if(is_array($assoc_val)){
					$tmparr = array();
					foreach($assoc_val as $obj)
					{
						if($assoc['ass_type'] == 'has_many'){
				            try
				            {
				              	$obj->$assoc['class_property'] = $this->$assoc['local_property'];
				            }
				            catch(Exception $e)
				            {
				              var_dump($e);
				            }
						}
						$obj->save();
						$tmparr[] = $obj;
					}
					$this->properties[$name] = $tmparr;
				}
				else{
					$assoc_val->save();
				}// Update done
				
				/*
				 * If its a has_and_belongs_to_many we have to update the association table also
				 */
				if($assoc['ass_type'] == 'has_and_belongs_to_many')
				{	
					/*
					 * Compare arrays to find associations that needs to be deleted or added
					 */
					$objectsToLink 		= array_udiff($this->properties[$name], $this->unmodified_properties[$name], array("ActiveRecord","cmpFunc")); 
					$objectsToUnlink 	= array_udiff($this->unmodified_properties[$name], $this->properties[$name], array("ActiveRecord","cmpFunc"));

					/*
					 * Objects to be deleted can easily be packed into a single sql statement.
					 */
					if($objectsToUnlink){
						$classProps = array();
						foreach($objectsToUnlink as $obj){
						  	$classProps[] = $obj->prepare_property($assoc['class_property']);
						} 				  
						$optionsArray = array(
						  						"TABLE" => $assoc['join_table'],
						  						"WHERE" => $assoc['join_local_property'] . " = " . 
						  								   $this->prepare_property($assoc['local_property'], $this->unmodified_properties[$assoc['local_property']]) .
						  								   " AND " . $assoc['join_class_property'] . " IN(" . join(",", $classProps) . ");"
						);
						$unlinkSql = SQLSyntaxor::getDestroySQL($optionsArray, $db->getAttribute(PDO::ATTR_DRIVER_NAME));
					}
					/*
					 * Objects to link needs to be set as many different statements
					 * TODO: Optimize the insert sequence. 
					 */
					if($objectsToLink){
						$classProps = array();
						$linkSqls = array(); 
						foreach($objectsToLink as $obj){
						  	$classProps[] = $obj->prepare_property($assoc['class_property']);
						} 	
						foreach($classProps as $classProp){			  
							$optionsArray = array(
							  						"TABLE" => $assoc['join_table'],
													"FIELDS" => $assoc['join_local_property'] . ", " . $assoc['join_class_property'],
													"VALUES" => $this->prepare_property($assoc['local_property']) . ", " . $classProp					
							);
							$linkSql[] = SQLSyntaxor::getCreateSQL($optionsArray, $db->getAttribute(PDO::ATTR_DRIVER_NAME));
						}						
					}
					
					/*
					 * Execute all the SQL. 
					 */
					
					$db->query($unlinkSql);
					foreach($linkSql as $sql){
					  	$db->query($sql);
					}
				}
			}
			
		}
	}

	/*
	 * a more robust compare function used to see if associations have changed. 
	 */
	function cmpFunc($a,$b){
		if(serialize($a) === serialize($b))
			return 0;
		return serialize($a) > serialize($b) ? -1 : 1;
	}
	
	function hydrate($row){
		$this->raw = $row;
		foreach($this->meta as $key => $value)
		{
			if(isset($row[$key]) && $row[$key] !== null){
				$this->unmodified_properties[$key] = $this->properties[$key] = $this->validate_data($key,$row[$key]);
			}else{
				$this->unmodified_properties[$key] = $this->properties[$key] = null;
			}
		}	
	}
	
	function is_dirty(){
		if(serialize($this->properties) != serialize($this->unmodified_properties))
			return true;
		else 	
			return false;
	}
	
	/**
	 * Will return a list of properties that has been changed, since the object was fetched. 
	 * This only works with properties that can be read as strings ie. not arrays and objects, 
	 * since (string) $object will always just return object and same with arrays.
	 *  
	 * @return array
	 */
	function dirty_fields(){
		$props = $this->properties;
		$uprops = $this->unmodified_properties;
		if(is_array($this->associations)){
			foreach($this->associations as $name => $value){
				if(isset($props[$name])){
					unset($props[$name]);
				}
				if(isset($uprops[$name])){
					unset($uprops[$name]);
				}
			}
		}
		return array_diff_assoc($props, $uprops);
	}

        function getLastInsertID($conn){
            $id = $conn->lastInsertId();
            if(!$id){
                $sql = SQLSyntaxor::getLastInsertIdSQL($conn->getAttribute(PDO::ATTR_DRIVER_NAME));
		if($sql == ""){
			return false;
		}
                $id = $conn->query($sql)->fetchColumn();
            }
            return $id;
        }
}

