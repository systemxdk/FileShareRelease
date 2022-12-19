<?php

class shPassword extends shHelper {

	static function generate($length = 8){
		// start with a blank password
		$password = "";

		// define possible characters
		$possible = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		
		// set up a counter
		$i = 0;

		// add random characters to $password until $length is reached
		while ($i < $length) {

			// pick a random character from the possible ones
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);

			// we don't want this character if it's already in the password more than once
			if (substr_count($password, $char) < 2) {
				$password .= $char;
				$i++;
			}

		}

		// done!
		return $password;

	}

}
