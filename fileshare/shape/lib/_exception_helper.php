<?php

class shException extends Exception {

    static private $exception_id = 0;
    static private $error_log = false;
    static private $trace_log = false;
    static private $email = NULL;
    static private $only_email = FALSE;

    static function errorlog(){
        self::$error_log = TRUE;
    }

    static function stop_errorlog(){
        self::$error_log = FALSE;
    }

    static function errortrace(){
        self::$trace_log = TRUE;
    }

    static function stop_errortrace(){
        self::$trace_log = FALSE;
    }

    static function setemail($email){
        self::$email = $email;
    }

    static function no_email(){
        self::$email = NULL;
    }

    static function only_email(){
        self::$only_email = TRUE;
    }

    function  __construct($message = null,$code = null, $previous = null) {
        parent::__construct($message,$code);
        self::$exception_id++;
        $debug_array = array();
        if(self::$error_log){
            $debug = $code ? '(code: '.$code.') '.$message : $message;
            array_push($debug_array,'shDebug '.self::$exception_id.' message: '.$debug);
            array_push($debug_array,'shDebug '.self::$exception_id.' source info: [source file:] '.$this->getFile().' - [source line:] '.$this->getLine().'');
        }
        if(self::$trace_log){    
            foreach (array_reverse($this->getTrace()) as $trace){
                array_push($debug_array,"shDebug ".self::$exception_id."===================================== TRACE: ".++$traceline."==========================================");
                foreach($trace as $key => $value){
                    if ( is_array($value)){
                        array_push($debug_array,'shDebug '.self::$exception_id.': '.$key.' subvalues:');
                        foreach($trace as $subkey => $subvalue){
                            array_push($debug_array,"shDebug ".self::$exception_id."      : ".$subkey." = ".$subvalue);
                        }
                    } else {
                        array_push($debug_array,"shDebug ".self::$exception_id.": ".$key." = ".$value);
                    }
                }
            }
        }

        if ( self::$email && $debug_array){
            $email_content = implode("\n", $debug_array);
            mail(self::$email, "Shape Exeception", $email_content);
        }
        if( $debug_array && !self::$only_email ){
            foreach ($debug_array as $debug){
                error_log($debug);
            }
        }

    }

}