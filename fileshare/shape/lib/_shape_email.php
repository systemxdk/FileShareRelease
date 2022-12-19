<?php

class shEmail {

    public static $from = NULL;
    private static $reply_to = NULL;
    private static $charset = 'UTF-8';
    public static $content_type = 'html';
	
	public static $smtp_host = NULL;
	public static $smtp_port = 25;
	public static $smtp_auth = FALSE;
	public static $smtp_username = NULL;
	public static $smtp_password = NULL;
	
    static function setFrom($from){
        self::$from = $from;
    }

    static function setReplyTo($reply_to){
        self::$reply_to = $reply_to;
    }

    static function setCharset($charset){
        self::$charset = $charset;
    }
	
	/** SMTP SETTERS **/
    static function setSMTPHost($host){
        self::$smtp_host = $host;
    }
	
    static function setSMTPPort($port){
        self::$smtp_port = $port;
    }
	
	static function setSMTPAuth($auth) {
		self::$smtp_auth = $auth ? true : false;
	}
	
    static function setSMTPUsername($username){
        self::$smtp_username = $username;
    }
	
    static function setSMTPPassword($password){
        self::$smtp_password = $password;
    }
	
    static function send_smtp(Array $recipients, $from_name, $from_address, $subject, $content, $html = false, $reply_to = false, $charset = null, $ccs = Array(), $bccs = Array(), $attachments = Array(), $smtp_username_override = NULL, $smtp_password_override = NULL){
		
        $reply_to = $reply_to ? $reply_to : self::$reply_to;
        $charset = $charset ? $charset : self::$charset;
        $content_type = self::$content_type;
		
		if ( !shPear::check_installed_package('Mail')) throw new Exception('PEAR Package Mail is required for SMTP mail');
		if ( !shPear::check_installed_package('Net_SMTP')) throw new Exception('PEAR Package Net_SMTP is required for SMTP mail');
		if ( !self::$smtp_host ) throw new Exception('SMTP Host has not been setup');
        if ( !$recipients && !$ccs && !$bccs ) throw new Exception('Missing recipient(s)');
        if ( !$from_name || !$from_address ) throw new Exception('Missing sender information');
        if ( !$charset ) throw new Exception('Missing Charset');
        if ( !$content_type ) throw new Exception('Missing Content-Type');
		
		// Validate recipients
		foreach ( $recipients AS $recipient ) {
			if ( !filter_var($recipient, FILTER_VALIDATE_EMAIL) ) throw new Exception("Could not validate recipient email {$recipient}");
		}
		
		// Validate from address
		if ( !filter_var($from_address, FILTER_VALIDATE_EMAIL) ) throw new Exception("Could not validate from email {$from_address}");
		
		// Validate CC
		if ( $ccs ) {
			foreach ( $ccs AS $cc ) {
				if ( !filter_var($cc, FILTER_VALIDATE_EMAIL) ) throw new Exception("Could not validate cc email {$cc}");
			}
		}
		
		// Validate BCC
		if ( $bccs ) {
			foreach ( $bccs AS $bcc ) {
				if ( !filter_var($bcc, FILTER_VALIDATE_EMAIL) ) throw new Exception("Could not validate bcc email {$bcc}");
			}
		}
		
		// Include PEAR Ext.
		require_once "Mail.php";

		// Generate Headers
		$headers = Array();
		$headers['From'] = sprintf('%s <%s>', mb_encode_mimeheader(utf8_decode($from_name), "ISO-8859-1", "Q"), $from_address);
		$headers['To'] = implode(",", $recipients);
		$headers['Subject'] = mb_encode_mimeheader(utf8_decode($subject), "ISO-8859-1", "Q");
		$headers['Date'] = date('r');
		$headers['MIME-Version'] = '1.0';
		if ( $html ) {
        	$message_content_type = 'Content-Type: text/html; charset=' . $charset;
		} else {
        	$message_content_type = 'Content-Type: text/plain; charset=' . $charset;
		}
		
		if ($reply_to) $headers['Reply-to'] = $reply_to;
		
		// Prepare mime multipart
		$semi_rand = md5(time());
		$boundary = md5(date('r', time())); 
		$headers['Content-type'] = "multipart/mixed;\n boundary=\"_1_$boundary\"";
		
		// Declare multipart email
		$final_content = "\n";
		$final_content .= "--_1_$boundary\n";
		$final_content .= "Content-Type: multipart/alternative; boundary=\"_2_$boundary\"\n\n";
		
		// Content part
		$final_content .= "--_2_$boundary\n";
		$final_content .= $message_content_type."\n";
		$final_content .= "Content-Transfer-Encoding: 7bit\n\n" . $content . "\n";
		$final_content .= "--_2_$boundary--\n\n";
		
		// Attachments
		if ( is_array($attachments) && count($attachments)!=0 ) {
			foreach ($attachments AS $attachment) {
				if(!is_array($attachment) || !isset($attachment['data'], $attachment['content-type'], $attachment['name'])) continue;
				$final_content .= "--_1_$boundary\n";
				$data = chunk_split(base64_encode($attachment['data']));
				$final_content .= "Content-Type: {$attachment['content-type']} name=\"{$attachment['name']}\"\n";
				$final_content .= "Content-Description: {$attachment['name']}\n";
    	        $final_content .= "Content-Disposition: attachment; filename=\"{$attachment['name']}\"; size=".strlen($attachment['data']).";\n";
	            $final_content .= "Content-Transfer-Encoding: base64\n\n" . $data . "\n";
				
			}
		}
		
		$final_content .= "--_1_$boundary--\n";
		
		// Generate SMTP params
		$params = Array();
		$params['host'] = self::$smtp_host;
		$params['auth'] = self::$smtp_auth;
		$params['port'] = self::$smtp_port;
		$params['username'] = $smtp_username_override ? $smtp_username_override : self::$smtp_username;
		$params['password'] = $smtp_password_override ? $smtp_password_override : self::$smtp_password;
		
		// SMTP Factory setup
		$smtp = Mail::factory('smtp', $params);
		
		// Send
		$mail = $smtp->send(implode(",", $recipients), $headers, $final_content);
		
		if (PEAR::isError($mail)) error_log("SMTP Mail transport error detected: " . $mail->getMessage());
		
	}
	
    static function send($to,$subject,$content,$from=NULL,$reply_to=NULL,$charset=NULL,$cc=NULL,$bcc=NULL,$html=TRUE){

        $from           = $from ? $from : self::$from;
        $reply_to       = $reply_to ? $reply_to : self::$reply_to;
        $charset        = $charset ? $charset : self::$charset;
        $content_type   = self::$content_type;

        if (!$to && !$cc && !$bcc) throw new Exception('Missing recipient(s)');
        if (!$from) throw new Exception('Missing sender');
        if (!$charset) throw new Exception('Missing Charset');
        if (!$content_type) throw new Exception('Missing Content-Type');
		
		
	$to = is_null($to) ? array() : $to;
        $to = is_array($to) ? $to : array($to);

	$cc = is_null($cc) ? array() : $cc;
        $cc = is_array($cc) ? $cc : array($cc);

	$bcc = is_null($bcc) ? array() : $bcc;
        $bcc = is_array($bcc) ? $bcc : array($bcc);

        $tos = array();
        foreach ( $to as $name => $recipient ){
            $name = is_int($name) ? $recipient : $name;
			if ( function_exists('filter_var') ) {
				if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)){
                	throw new Exception($recipient.' is not a valid e-mail (to)');
            	}
			}
		//die("<br />" . $name . "<br />" . $recipient);
		if ( $name && !filter_var($name, FILTER_VALIDATE_EMAIL) ) {
			$tos[] = $name.'<'.$recipient.'>';
		} else {
			$tos[] = $recipient;
		}
        }
        $ccs = array();
        foreach ( $cc as $name => $recipient ){
            $name = is_int($name) ? $recipient : $name;
			if ( function_exists('filter_var') ) {
            	if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)){
					throw new Exception($recipient.' is not a valid e-mail (cc)');
				}
			}
            $ccs[] = $name.'<'.$recipient.'>';
        }
        $bccs = array();
        foreach ( $bcc as $name => $recipient ){
            if ( function_exists('filter_var') ){
				if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)){
                	throw new Exception($recipient.' is not a valid e-mail (bcc)');
            	}
			}
            $bccs[] = $recipient;
        }

        $headers = '';
        $headers .= 'MIME-Version: 1.0' . "\r\n";
		if ( $html ) {
        	$headers .= 'Content-type: text/'.$content_type.'; charset=' . $charset . "\r\n";
		} else {
        	$headers .= 'Content-type: text/plain; charset=' . $charset . "\r\n";
		}
        $headers .= 'From: '. $from . "\r\n";
        if (self::$reply_to) $headers .= 'Reply-to: '. $reply_to . "\r\n";
        if ($tos){
            $headers .= 'To: '. implode(',',$tos) . "\r\n";
        }
        if ($ccs){
            $headers .= 'Cc: '. implode(',',$ccs) . "\r\n";
        }
        if ($bccs){
            $headers .= 'Bcc: '. implode(',',$bccs) . "\r\n";
        }
		
		if ( $html ) {
        $content = <<<EOTHML
<html>
    <body>
        {$content}
    </body>
</html>
EOTHML;
		}
		
        mail('', $subject, $content, $headers) or error_log('FEJL');
    }

}
