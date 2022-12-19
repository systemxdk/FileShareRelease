<?php

class shPear {
	
	public static function check_installed_package($pear_package_name) {
		$packages = `/usr/bin/pear list`; //system('/usr/bin/pear list', $packages);
		$packages = explode("\n",$packages);
		foreach ( $packages AS $package ) {
			if ( strstr($package, $pear_package_name) ) return true;
		}
		return false;
	}
	
}

?>