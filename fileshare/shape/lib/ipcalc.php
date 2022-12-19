<?php

class shIpcalc {



  /**
   * Calculate subnet, netmask and HostMin/Max
   *
   * @param string $my_net_info
   * @param bool $binary
   * @return array
   */
  static function calculate_subnet($my_net_info, $binary = false){
  	$result = array();
  	$errString = '';
  	$my_net_info=rtrim($my_net_info);

	  if (! ereg('^([0-9]{1,3}\.){3}[0-9]{1,3}(( ([0-9]{1,3}\.){3}[0-9]{1,3})|(/[0-9]{1,2}))$',$my_net_info)){
			$errString = "Invalid Input.<br>";
			$errString .= "Use IP & CIDR Netmask:&nbsp;, '10.0.0.1/22'<br>";
			$errString .= "Or IP & Netmask:,'10.0.0.1 255.255.252.0'<br>";
			$errString .= "Or IP & Wildcard Mask:,'10.0.0.1 0.0.3.255'<br>";
			throw new shException($errString);
	  }

	  if (ereg("/",$my_net_info)){  //if cidr type mask
			$dq_host = strtok("$my_net_info", "/");
			$cdr_nmask = strtok("/");
			if (!($cdr_nmask >= 0 && $cdr_nmask <= 32)){
				$errString = "Invalid CIDR value. Try an integer 0 - 32.";
				throw new shException($errString);
			}
			$bin_nmask = self::cdrtobin($cdr_nmask);
			$bin_wmask = self::binnmtowm($bin_nmask);
	  } else { //Dotted quad mask?
	    $dqs=explode(" ", $my_net_info);
			$dq_host=$dqs[0];
			$bin_nmask = self::dqtobin($dqs[1]);
			$bin_wmask = self::binnmtowm($bin_nmask);
			if (ereg("0",rtrim($bin_nmask, "0"))) {  //Wildcard mask then? hmm?
				$bin_wmask = self::dqtobin($dqs[1]);
				$bin_nmask = self::binwmtonm($bin_wmask);
				if (ereg("0",rtrim($bin_nmask, "0"))){ //If it's not wcard, whussup?
					$errString = "Invalid Netmask";
					throw new shException($errString);
				}
			}
			$cdr_nmask = self::bintocdr($bin_nmask);
	  }

		//Check for valid $dq_host
		if(! ereg('^0.',$dq_host)){
			foreach( explode(".",$dq_host) as $octet ){
		 		if($octet > 255){
					$errString = "Invalid IP Address";
					throw new shException($errString);
				}
			}
		}

		$bin_host = self::dqtobin($dq_host);
		$bin_bcast=(str_pad(substr($bin_host,0,$cdr_nmask),32,1));
		$bin_net=(str_pad(substr($bin_host,0,$cdr_nmask),32,0));
		$bin_first=(str_pad(substr($bin_net,0,31),32,1));
		$bin_last=(str_pad(substr($bin_bcast,0,31),32,0));
		$host_total=(bindec(str_pad("",(32-$cdr_nmask),1)) - 1);

	  if ($host_total <= 0){  //Takes care of 31 and 32 bit masks.
			$bin_first="N/A" ; $bin_last="N/A" ; $host_total="N/A";
			if ($bin_net === $bin_bcast){
				$bin_bcast="N/A";
			}
	  }

	  // Subtract 2 from the broadcast address instead of just 1.
	  	$dec = explode(" ", chunk_split($bin_bcast, 8, " "));
	  	$dec[3] = decbin(bindec($dec[3]) - 2) . "\n";
	  	$bin_last =  implode("", $dec) . "\n";
	  	$host_total=(bindec(str_pad("",(32-$cdr_nmask),1)) - 2);

		/**
		 * Push calculation result to array
		 */
	  if ($binary){
			$result['subnet'] 	 = trim(self::dotbin($bin_host));
			$result['netmask'] 	 = self::dotbin($bin_nmask);
			$result['wildcard']  = self::dotbin($bin_wmask);
			$result['Network']   = $dotbin_net;
			$result['Broadcast'] = self::dotbin($bin_bcast);
			$result['HostMin'] 	 = self::dotbin($bin_first);
			$result['HostMax'] 	 = self::dotbin($bin_last);
	  } else {
			$result['subnet'] 	 = $dq_host;
			$result['netmask'] 	 = self::bintodq($bin_nmask);
			$result['cdr_nmask'] = $cdr_nmask;
			$result['wildcard']  = self::bintodq($bin_wmask);
			$result['Network']   = self::bintodq($bin_net);
			$result['Broadcast'] = self::bintodq($bin_bcast);
			$result['HostMin'] 	 = self::bintodq($bin_first);
			$result['HostMax'] 	 = self::bintodq($bin_last);
			$result['Hosts/Net'] = $host_total;
	  }

		return $result;
  }

  /**
   * The following static functions are supporting subnet/netmask calculation
   */
	static function binnmtowm($binin){
		$binin=rtrim($binin, "0");
		if (!ereg("0",$binin) ){
			return str_pad(str_replace("1","0",$binin), 32, "1");
		} else return "1010101010101010101010101010101010101010";
	}

	static function bintocdr ($binin){
		return strlen(rtrim($binin,"0"));
	}

	static function bintodq ($binin) {
		if ($binin=="N/A") return $binin;
		$binin=explode(".", chunk_split($binin,8,"."));
		for ($i=0; $i<4 ; $i++) {
			$dq[$i]=bindec($binin[$i]);
		}
	        return implode(".",$dq) ;
	}

	static function bintoint ($binin){
	        return bindec($binin);
	}

	static function binwmtonm($binin){
		$binin=rtrim($binin, "1");
		if (!ereg("1",$binin)){
			return str_pad(str_replace("0","1",$binin), 32, "0");
		} else return "1010101010101010101010101010101010101010";
	}

	static function cdrtobin ($cdrin){
		return str_pad(str_pad("", $cdrin, "1"), 32, "0");
	}

	static function dotbin($binin){
		// splits 32 bit bin into dotted bin octets
		if ($binin=="N/A"){
			return $binin;
		}
		$oct=rtrim(chunk_split($binin,8,"."),".");
		return $oct;
	}

	static function dqtobin($dqin) {
	        $dq = explode(".",$dqin);
	        for ($i=0; $i<4 ; $i++) {
	           $bin[$i]=str_pad(decbin($dq[$i]), 8, "0", STR_PAD_LEFT);
	        }
	        return implode("",$bin);
	}

	static function inttobin ($intin) {
	        return str_pad(decbin($intin), 32, "0", STR_PAD_LEFT);
	}

	static function validate_ip ($ip_addr, $filter_opt = 'FILTER_FLAG_IPV4') {
		return filter_var($ip_addr, FILTER_VALIDATE_IP, $filter_opt);
	}
	
}