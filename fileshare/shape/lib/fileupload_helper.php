<?php

/**
 * Helper class to upload files (They will be saved localy)
 *
 */
class shFileupload extends shHelper {
	
	/**
	 * Name of file input field
	 *
	 * @var string
	 */
	private $fileRef;
	
	/**
	 * Upload directory
	 *
	 * @var string
	 */
	private $uploaddir;
	
	/**
	 * Name of destination file
	 *
	 * @var string
	 */
	private $filename;
	
	/**
	 * name of source filename
	 *
	 * @var string
	 */
	private $clientFileName;
	
	/**
	 * name of file MIME type
	 *
	 * @var string
	 */
	private $clientMimeType;
	
	/**
	 * size
	 *
	 * @var int
	 */
	private $size;
	
	/**
	 * name of temporary file
	 *
	 * @var string
	 */
	private $tmpName;
	
	/**
	 * error string
	 *
	 * @var string
	 */
	private $stateString;
	
	/**
	 * places an uploaded file where you whish
	 * Helper class to upload files (They will be saved localy)
	 *
	 * @param string $fileRef
	 * @param string $uploaddir
	 * @param string $filename
	 * @param int $maxSize
	 * @throws Exception
	 */
	function __construct($fileRef,$uploaddir,$filename = NULL,$maxSize = NULL, $mimesOnly = Array()){

		$this->uploaddir		= $uploaddir;
		
		$this->clientFileName	= $_FILES[$fileRef]['name'];
		$this->clientMimeType	= $_FILES[$fileRef]['type'];
		$this->size				= $_FILES[$fileRef]['size'];
		$this->tmpName			= $_FILES[$fileRef]['tmp_name'];
		$this->stateString		= $_FILES[$fileRef]['error'];
		
		/**
		 * set file name (use source if none defined
		 */
		$this->filename			= $filename ? $filename : $this->clientFileName;
		$this->prepare_filename(); // 
		
		if ( $mimesOnly && !in_array($this->clientMimeType, $mimesOnly) ) {
			throw new Exception("Illegal MIME type on uploaded file (".$this->clientMimeType.")");
		}
		
		/**
		 * Validations
		 */
		if( !is_dir($this->uploaddir) ){
			throw new Exception("Upload dir does not exist! (" . $this->uploaddir . ")");
		}
		
		if( $maxSize && $this->size > $maxSize){
			throw new Exception("File received larger than allowed!");
		}
		
		if($this->stateString && $this->stateString != 'UPLOAD_ERR_OK'){
			throw new Exception("ERROR: " . $this->stateString );
		}
		
		if( file_exists($this->uploaddir . '/' . $this->filename)){
			throw new Exception("A file with name " . $this->filename . " already exists in " . $this->uploaddir . "!", 99 );
		}
		
		/**
		 * Write file
		 */
		move_uploaded_file($this->tmpName,$uploaddir . '/' . $this->filename);
	}
	
	private function prepare_filename() {
		if ( file_exists($this->uploaddir . $this->filename) ){
			for ( $i = 1; $i <= 150; $i++ ) {
				$cur_filename = NULL;
				$filename_bits = explode(".", $this->filename);
				foreach ( $filename_bits AS $key => $filename_bit ) {
					if ( $key == count($filename_bits) - 1) {
						$cur_filename = trim($cur_filename, ".");
						$cur_filename .= "_" . $i . ".";
					}
					$cur_filename .= $filename_bit . "."; 
				}
				$cur_filename = trim($cur_filename, ".");
				if ( !file_exists($this->uploaddir . $cur_filename) ) {
					$this->filename = $cur_filename;
					return;
				}
			}
			throw new Exception("Filename could not be iterated.");
		}
		return;
	}
	
	/**
	 * get error string
	 *
	 * @return string
	 */
	function getError(){
		return $this->stateString;
	}
	
	/**
	 * Get File name
	 *
	 * @return string
	 */
	function getFileName(){
		return $this->filename;
	}
	
	/**
	 *  Get File Mimetype
	 *  
	 *  @return string
	 */
	function getMimetype(){
		return $this->clientMimeType;
	}
	
	/**
	 * Get Filesize
	 * 
	 * @return int
	 */
	function getFilesize(){
		return $this->size;
	}
	
}
