<?php

date_default_timezone_set('Europe/Copenhagen');

require_once "./class_loader.php";

/*
 * Available databases, must match the database.php
 */
$databases = array(1 => "MYSQL", 0 => "CANCEL");

echo <<<EOM

Velkommen til class builder
This script generates models based on existing tables. Edit database.php and this file to set available databases. 
\n
EOM;

$options = array("Database", "Table name");
foreach($options as $key => $option){
	switch($option){
		case 'Database':
			echo "Select database: \n";
			foreach($databases as $db_key => $database){
				echo $db_key . ": " . $database  . "\n";
			}
			$database = $databases[trim(fread(STDIN, 2))];
			if($database == "CANCEL")
				exit;		
			break;
		case 'Table name':
			echo "Enter tablename(komma seperates): ";
			$table = trim(fread(STDIN, 1024));		
			break;
	}
}

echo "\n";
$tables = explode(",", $table);
foreach($tables as $table){
	$builder = new class_builder($table, $database);
	$builder->parseTable();
	echo $builder->buildClass();
}
echo "\n";
class class_builder{

	private $fields = array();
	private $tableName; 
	private $repository;
	private $conn;
	private $database;
	private $meta_indent = "\n\t\t\t\t\t\t\t\t";
	private $indent = "\n\t\t";
	private $doubleLineBreak = "\n\n";
	private $lineBreak = "\n";
	
	public function __construct($tableName, $database = "mysql", $repository = "models"){
		$this->tableName = $tableName;
		$this->database  = strtoupper($database);
		$this->conn = Database::connect($database);
		$this->repository = $repository;
	}
	
	public function parseTable(){
		$functionName = "parse" . ucfirst($this->database) . "Table";
		if ( $this->database == 'CRACK' ) {
			$functionName = "parseMysqlTable";
		}
		$this->$functionName();
	}
	
	private function parseMysqlTable(){
		$sql = "DESCRIBE {$this->tableName};";
		$result = $this->conn->query($sql);
		
		foreach($result as $rows){
			$meta = array();
			$meta['type'] 		= $this->parse_mysql_field_type($rows['Type']);
			$meta['primary'] 	= $rows['Key'] 	== 'PRI' ? 'true' : 'false';
			$meta['required'] 	= $rows['Null'] == 'NO' ? 'true' : 'false';
			$meta['default'] 	= "'$rows[Default]'";
			$meta['extra']		= "'$rows[Extra]'";
			
			$this->fields[$rows['Field']] = $meta;
		}		
	}
	
	private function parseOCITable(){

		$primaryField = $this->getOCIPrimaryKey();
		$sql = "SELECT * FROM user_tab_columns WHERE TABLE_NAME = '{$this->tableName}'";
		$result = $this->conn->query($sql);
		$meta = array();
		while($row = $result->fetch()){
			$meta['type'] 		= $this->parse_oci_field_type(strtolower($row['DATA_TYPE']));
			$meta['primary']	= $primaryField == $row['COLUMN_NAME'] ? 'true' : 'false';
			$meta['required']	= $row['NULLABLE'] == 'N' ? 'true' : 'false';
			$meta['default']	= "'$row[DATA_DEFAULT]'";
			$meta['extra']		= "''";
			
			$this->fields[$row['COLUMN_NAME']] = $meta;
		}
	}
	
	private function getOCIPrimaryKey(){
		$sql = "SELECT cols.table_name, cols.column_name, cols.position, cons.status, cons.owner
				FROM all_constraints cons, all_cons_columns cols
				WHERE cols.table_name = '{$this->tableName}'
				AND cons.constraint_type = 'P'
				AND cons.constraint_name = cols.constraint_name
				AND cons.owner = cols.owner
				ORDER BY cols.table_name, cols.position";
		$result = $this->conn->query($sql);
		if(!$result){
			var_dump($this->conn->errorInfo());exit;
			return false;
		}
		if(false !== ($row = $result->fetchObject())){
			if($row->STATUS == 'ENABLED')
				return $row->COLUMN_NAME;
		}
		return false;
				
	}
	
	private function parseCONCORDETable(){

		$primaryField = $this->getOCIPrimaryKey();
		$sql = "SELECT * FROM user_tab_columns WHERE TABLE_NAME = '{$this->tableName}'";
		$result = $this->conn->query($sql);
		$meta = array();
		while($row = $result->fetch()){
			$meta['type'] 		= $this->parse_oci_field_type(strtolower($row['DATA_TYPE']));
			$meta['primary']	= $primaryField == $row['COLUMN_NAME'] ? 'true' : 'false';
			$meta['required']	= $row['NULLABLE'] == 'N' ? 'true' : 'false';
			$meta['default']	= "'$row[DATA_DEFAULT]'";
			$meta['extra']		= "''";
			
			$this->fields[$row['COLUMN_NAME']] = $meta;
		}
	}

	private function getCONCORDEPrimaryKey(){
		$sql = "SELECT cols.table_name, cols.column_name, cols.position, cons.status, cons.owner
				FROM all_constraints cons, all_cons_columns cols
				WHERE cols.table_name = '{$this->tableName}'
				AND cons.constraint_type = 'P'
				AND cons.constraint_name = cols.constraint_name
				AND cons.owner = cols.owner
				ORDER BY cols.table_name, cols.position";
		$result = $this->conn->query($sql);
		if(!$result){
			var_dump($this->conn->errorInfo());exit;
			return false;
		}
		if(false !== ($row = $result->fetchObject())){
			if($row->STATUS == 'ENABLED')
				return $row->COLUMN_NAME;
		}
		return false;
				
	}

	private function parseWEBSELFCARETable(){

		$primaryField = $this->getOCIPrimaryKey();
		$sql = "SELECT * FROM user_tab_columns WHERE TABLE_NAME = '{$this->tableName}'";
		$result = $this->conn->query($sql);
		$meta = array();
		while($row = $result->fetch()){
			$meta['type'] 		= $this->parse_oci_field_type(strtolower($row['DATA_TYPE']));
			$meta['primary']	= $primaryField == $row['COLUMN_NAME'] ? 'true' : 'false';
			$meta['required']	= $row['NULLABLE'] == 'N' ? 'true' : 'false';
			$meta['default']	= "'$row[DATA_DEFAULT]'";
			$meta['extra']		= "''";

			$this->fields[$row['COLUMN_NAME']] = $meta;
		}
	}

	private function getWEBSELFCAREPrimaryKey(){
		$sql = "SELECT cols.table_name, cols.column_name, cols.position, cons.status, cons.owner
				FROM all_constraints cons, all_cons_columns cols
				WHERE cols.table_name = '{$this->tableName}'
				AND cons.constraint_type = 'P'
				AND cons.constraint_name = cols.constraint_name
				AND cons.owner = cols.owner
				ORDER BY cols.table_name, cols.position";
		$result = $this->conn->query($sql);
		if(!$result){
			var_dump($this->conn->errorInfo());exit;
			return false;
		}
		if(false !== ($row = $result->fetchObject())){
			if($row->STATUS == 'ENABLED')
				return $row->COLUMN_NAME;
		}
		return false;

	}
	private function parseKUNDESTYRINGTable(){

		$primaryField = $this->getKUNDESTYRINGPrimary();

		$sql = "exec sp_columns {$this->tableName}";
		$result = $this->conn->query($sql);
		$meta = array();
		while($row = $result->fetch(PDO::FETCH_ASSOC)){
                    var_dump($row['NULLABLE']);
			$meta['type'] 		= $this->parse_ms_field_type(strtolower($row['TYPE_NAME']));
			$meta['primary']	= $primaryField == $row['COLUMN_NAME'] ? 'true' : 'false';
			$meta['required']	= $row['NULLABLE'] == "0" ? 'true' : 'false';
			$meta['default']	= "'{$row['COLUMN_DEF']}'";
			$meta['extra']		= stripos($row['TYPE_NAME'], 'identity') ? "'auto_increment'" : "''";

			$this->fields[$row['COLUMN_NAME']] = $meta;
		}

	}

        private function getKUNDESTYRINGPrimary(){
            $sql = "exec sp_pkeys {$this->tableName}";

            $result = $this->conn->query($sql);
            if(!$result){
                    var_dump($this->conn->errorInfo());exit;
                    return false;
            }
            if(false !== ($row = $result->fetchObject())){
                return $row->COLUMN_NAME;
            }
            return false;
        }

	public function buildClass(){
		$metaString  = "";
		$propString  = "";
                $docString   = "";
		$this->buildPropertyStrings($metaString, $propString, $docString);
		$str_buffer  = "<?php" . $this->lineBreak;
		$str_buffer .= "/**" . $this->lineBreak;
		$str_buffer .= "* Class generated using Uteeni ORM generator" . $this->lineBreak;
		$str_buffer .= "* Created: ". date("Y-m-d H:i:s", time()) . $this->lineBreak;
                $str_buffer .= $docString;
		$str_buffer .= "**/" . $this->lineBreak;
		$str_buffer .= "class ". $this->camelcase($this->tableName) ." extends ActiveRecord {" . $this->doubleLineBreak;	

		$str_buffer .= $this->indent . "public \$table_name = '{$this->tableName}';";
		$str_buffer .= $this->indent . "public \$database = '{$this->database}';" . $this->lineBreak;
		$str_buffer .= $propString . $metaString;
		
		$str_buffer .= $this->doubleLineBreak;
		
		$end_generate_comment = "/* end_auto_generate\ndo_not_delete_this_comment */";
		$str_buffer .= $end_generate_comment;
		
		$filename = "{$this->repository}/".str_replace("_", "", strtolower($this->tableName)).".php";
		if(file_exists($filename))
		{
			  $old_contents = str_replace("\r", "", file_get_contents($filename));
			  $preserve = substr($old_contents, (strpos($old_contents, $end_generate_comment) + strlen($end_generate_comment)));
			  $str_buffer .= $preserve;			  
		}		
		$beginnings = substr_count($str_buffer, "{");
		$endings 	= substr_count($str_buffer, "}");
		$str_buffer .= $beginnings == ($endings + 1) ? $this->doubleLineBreak . "}" : "";
		
		file_put_contents($filename, $str_buffer);
	}
	
	private function buildPropertyStrings(&$metaString, &$propString, &$docString){
		$propString = $this->indent . 'protected $properties = array(';
		$metaString = $this->indent . 'protected $meta = array(';
		foreach($this->fields as $field => $meta){
			$propString .= $this->meta_indent . "'$field'" . ' => null,';
			$metaString .= $this->meta_indent;
			$metaString .= "'$field'" . ' => array(';
			foreach($meta as $type => $value){
				$metaString .= " '" . $type . "' => " . "$value,";
			}
			$metaString = preg_replace("/,$/", "", $metaString);
			$metaString .= "),";
                        $docString .= " * @property " . str_replace("'", "", $meta['type']) . " $" . $field . $this->lineBreak;
                        $docString .= " * @method bool find_by_" . $field . "(" . str_replace("'", "", $meta['type']) . " $" . $field . ")" . $this->lineBreak;
                        $docString .= " * @method array find_all_by_" . $field . "(" . str_replace("'", "", $meta['type']) . " $" . $field . ")" . $this->lineBreak;
		}
		$metaString = preg_replace("/,$/", "", $metaString);
		$propString = preg_replace("/,$/", "", $propString);
		$propString = $propString . $this->indent . ");";
		$metaString = $metaString . $this->indent . ");";		
	}
	
	function parse_mysql_field_type($type){
		switch($type){
			case strpos($type, 'enum') !== false:
				//return parse_enum($type);
				return "'enum'";
			case strpos($type, 'date') !== false:
			case strpos($type, 'datetime') !== false:
				return "'date'";
			case strpos($type, 'varchar') !== false:
			case strpos($type, 'char') !== false:
				return "'string'";
			case strpos($type, 'tinyint') !== false:
			case strpos($type, 'mediumint') !== false:
			case strpos($type, 'int') !== false:
				return "'integer'";
			case strpos($type, 'decimal') !== false:
			case strpos($type, 'float') !== false;
				return "'double'";//PHP blunder
			case strpos($type, 'blob') !== false:
				return "'blob'";	
			case strpos($type, 'text') !== false:
				return "'text'";
			default:
				return "'$type'";						
		}
	}
	
	function parse_oci_field_type($type){
		switch($type){
			case strpos($type, 'date') !== false:
			case strpos($type, 'datetime') !== false:
				return "'date'";
			case strpos($type, 'varchar') !== false:
			case strpos($type, 'char') !== false:
				return "'string'";
			case strpos($type, 'tinyint') !== false:
			case strpos($type, 'mediumint') !== false:
			case strpos($type, 'int') !== false:
				return "'integer'";
			case strpos($type, 'number') !== false:				
			case strpos($type, 'decimal') !== false:
			case strpos($type, 'float') !== false;
				return "'double'";//PHP blunder
			case strpos($type, 'blob') !== false:
				return "'blob'";	
			case strpos($type, 'clob') !== false:				
			case strpos($type, 'text') !== false:
				return "'text'";
			default:
				return "'$type'";						
		}
	}

	function parse_ms_field_type($type){
		switch($type){
			case strpos($type, 'date') !== false:
			case strpos($type, 'datetime') !== false:
				return "'date'";
			case strpos($type, 'varchar') !== false:
			case strpos($type, 'char') !== false:
				return "'string'";
			case strpos($type, 'tinyint') !== false:
			case strpos($type, 'mediumint') !== false:
			case strpos($type, 'int') !== false:
				return "'integer'";
			case strpos($type, 'number') !== false:
			case strpos($type, 'decimal') !== false:
			case strpos($type, 'float') !== false;
				return "'double'";//PHP blunder
			case strpos($type, 'blob') !== false:
				return "'blob'";
			case strpos($type, 'clob') !== false:
			case strpos($type, 'text') !== false:
				return "'text'";
			default:
				return "'$type'";
		}
	}
	
	function parse_enum($enum){
		return str_replace("enum", "array", $enum);
	}
	
	function camelcase($str){
	$words = array();
	foreach(explode("_", $str) as $word){
		array_push($words, ucfirst(strtolower($word)));
	}
	return join("", $words);
}
 
}
 
