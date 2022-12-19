
<?php
define("SHAPEROOT", dirname(__FILE__) . "/../../");
require_once(SHAPEROOT . "active_record/database.php");

$repository = SHAPEROOT . "active_record/models";
$conn = Database::connect();
$meta_indent = "\n\t\t\t\t\t\t\t\t";

$tables[] = $tablename;


//var_dump(count($tables));exit;

$files = array();
$tableArr = array();
//Load schema
foreach($tables as $table){
	
	$tableArr[$table]++;
	$str_buffer = "<?php\n";
	$str_buffer .= "/**\n";
	$str_buffer .= "* Class generated using Uteeni ORM generator\n";
	$str_buffer .= "* Created: ". date("Y-m-d H:i:s", time()) ."\n";	
	$str_buffer .= "**/\n";
	$str_buffer .= "class ". camelcase($table) ." extends ActiveRecord {\n";
	
	$sql = "DESCRIBE $table;";
	
	$result = $conn->query($sql);
	
	$fields = array();
	$meta_str = "";
	foreach($result as $rows){
		array_push($fields, $rows['Field']);

		$tableDesc[$rows['Field']]['Type']			= $rows['Type'];
		$tableDesc[$rows['Field']]['Null']			= $rows['Null'];
		$tableDesc[$rows['Field']]['PrimaryKey']	= $rows['Key']; 
		$tableDesc[$rows['Field']]['Default']		= $rows['Default'];
		$tableDesc[$rows['Field']]['Extra']			= $rows['Extra'];
		
		$primary = ($rows['Key'] == 'PRI') ? 'true' : 'false';
		$required = ($rows['Null'] == 'NO') ? 'true' : 'false';
		$meta_str .= "$meta_indent'".$rows['Field']."' => array('type' => ".parse_field_type($rows['Type']).", 'primary' => $primary, 'required' => $required, 'default' => '".$rows['Default']."', 'extra' => '".$rows['Extra']."'),";
	}
	
	//Remove trailing ,
	$meta_str = preg_replace("/,$/", "", $meta_str);

	$str_buffer .= "\t\tpublic \$table_name = '$table';\n";
	
	$property_fields = "";
	//var_dump($fields);
	foreach($fields as $field){
		$property_fields .= "$meta_indent'$field' => null,";
	}
	//Remove trailing ,
	$property_fields = preg_replace("/,$/", "", $property_fields);
	$str_buffer .= "\t\tprotected \$properties = array($property_fields);\n";
	$str_buffer .= "\t\tprotected \$meta = array($meta_str);\n\n\n";
	
	


	$end_generate_comment = "/* end_auto_generate\ndo_not_delete_this_comment */";
	$str_buffer .= $end_generate_comment; 

	$filename = "{$repository}/".str_replace("_", "", strtolower($table)).".php";
	
	if(file_exists($filename))
	{
	  
	  $old_contents = file_get_contents($filename);
	  $preserve = substr($old_contents, (strpos($old_contents, $end_generate_comment) + strlen($end_generate_comment)));
	  $str_buffer .= $preserve;
	  /**
	   * TODO: ADD CHECK TO SEE IF THE FINISHING BRACKET IS MISSING!
	   */
	  
	}
  else
  {
    	$str_buffer .= "\n\n\n}\n";
	}

	file_put_contents($filename,$str_buffer);
}


/*Helpers*/
// Remember to use !== false to make sure you catch all occurrences of the type.
function parse_field_type($type){
	switch($type){
		case strpos($type, 'enum') !== false:
			//return parse_enum($type);
			return "'enum'";
		case strpos($type, 'int') !== false:
			return "'integer'";
		case strpos($type, 'date') !== false:
			return "'date'";
		case strpos($type, 'varchar') !== false:
			return "'string'";
		case strpos($type, 'datetime') !== false:
			return "'date'";
		case strpos($type, 'mediumint') !== false:
			return "'integer'";
		case strpos($type, 'decimal') !== false:
		case strpos($type, 'float') !== false;
			return "'double'";//PHP blunder
		case strpos($type, 'char') !== false:
			return "'string'";
		case strpos($type, 'tinyint') !== false:
			return "'integer'";
		case strpos($type, 'blob') !== false:
			return "'blob'";	
		case strpos($type, 'text') !== false:
			return "'text'";
		default:
			return "'$type'";						
	}
}
function parse_enum($enum){
	return str_replace("enum", "array", $enum);
}




?>
