<?php

/**
 * Helper class to upload files (They will NOT be saved localy)
 *
 */
class shFileupload2array extends shHelper {
	
	/**
	 * Reads input of file in $_FILES, and places data in an object 
	 * attributes:
	 * 	returns array with associative arrays of every line, matched against description
	 * @param string $fileRef
	 * @param string $fileType currently csv, soon xml will be supported
	 * @param string $splitBit
	 * @param array $fileDescription
	 * @param int $maxSize
	 */
	private function __construct(){
		
	}
	

	
	static private function _readFile($fileRef,$maxSize = NULL){
		
		$std = new stdClass();
		
		$std->clientFileName	= $_FILES[$fileRef]['name'];
		$std->clientMimeType	= $_FILES[$fileRef]['type'];
		$std->size				= $_FILES[$fileRef]['size'];
		$std->tmpName			= $_FILES[$fileRef]['tmp_name'];
		$std->stateString		= $_FILES[$fileRef]['error'];

			/**
		 * set content, or throw exception
		 */
		if (!file_exists($std->tmpName)) {			
			throw new Exception("File does not exists!");
		}
		
		/**
		 * Validations
		 */
		if( $maxSize && $std->size > $maxSize){
			throw new Exception("File received larger than allowed!");
		}
		
		if($std->stateString && $std->stateString != 'UPLOAD_ERR_OK'){
			throw new Exception("ERROR: " . $std->stateString );
		}
		
		return $std;
	}
	
	static function ReadXml($fileRef, $maxSize = NULL){
		$std = self::_readFile($fileRef,$maxSize);
		/**
		 * set content, or throw exception
		 */
//		if ($std->clientMimeType != "text/plain") {			
//			throw new Exception("File does not exists, or is not a text file (file type: " . $std->clientMimeType . ")");
//		}

		return simplexml_load_file($std->tmpName);
	
	}
		
	static function ReadCsv($fileRef, $splitBit = NULL, array $fileDescription = array(), $maxSize = NULL, $ignore_errorlines = TRUE, $ignore_filetype = FALSE){
	
		$std = self::_readFile($fileRef,$maxSize);

		/**
		 * set content, or throw exception
		 */
		if ( !$ignore_filetype ){
		    if ($std->clientMimeType != "text/plain" && $std->clientMimeType != "application/vnd.ms-excel" && $std->clientMimeType != "text/csv") {
			    throw new Exception("File does not exists, or is not a text file (file type: " . $std->clientMimeType . ")");
		    }
		}
		
		$std->splitBit			= $splitBit;
		$std->fileDescription	= $fileDescription;
		
		/**
		 * set content, or throw exception
		 */		
		if (!$fp = fopen($std->tmpName,"r")) {
			throw new Exception("File could not be opened");
		} else {
			while(!feof($fp)) {
				
				$input = fgetcsv($fp,2048,$std->splitBit);
				
				if( count($input) != count($std->fileDescription)){
					if($ignore_errorlines) {
						continue;
					} else {
						throw new Exception("Line content does not match file description : " . count($input) . " = " . count($std->fileDescription) );
					}
				}
				$lineArr = array();
				foreach ( $std->fileDescription as $key => $desc ){
					$lineArr[$desc] = $input[$key];
				}
				$std->content[] = $lineArr;
			}
		}
		return $std->content;
	}

}