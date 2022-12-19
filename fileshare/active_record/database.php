<?

class Database{

	static $conn = null;
	static $host = null;
	static $db 	 = null; 
	static $user = null;
	static $pass = null;

    /**
     *
     * @param string $driver
     * @return PDO
     */
	static function connect($driver = "mysql"){
		try {
			$driver = $driver ? $driver : "mysql";
			$driver = strtoupper($driver) . "Database";	
			switch($driver){
				case "MYSQLDatabase":
					self::$host ? MYSQLDatabase::$host 	= self::$host 	: "";
					self::$db 	? MYSQLDatabase::$db 	= self::$db 	: ""; 
					self::$user ? MYSQLDatabase::$user 	= self::$user 	: "";
					self::$pass ? MYSQLDatabase::$pass 	= self::$pass 	: "";	
					return MYSQLDatabase::connect();
				break;
				default: 
					throw new Exception("Unknown database driver: $driver");
					break; 
			}
		} catch ( Exception $e ) {
			error_log($e->getMessage());
			die();
		}
	}
}


class MYSQLDatabase{
	static $conn = null;
	static $host = "localhost";
	static $db = "fileshare";
	static $user = "fileshare";
	static $pass = "";

    /**
     *
     * @return PDO
     */
	static function connect()
	{	
		if(self::$conn == null) {
	self::$conn = new PDO('mysql:host=' . self::$host . ';dbname=' . self::$db, self::$user, self::$pass);

		}
		return self::$conn;
	}
	
}
