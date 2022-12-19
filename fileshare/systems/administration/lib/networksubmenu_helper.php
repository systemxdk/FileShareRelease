<?php

class NetworkSubmenu {
	
	public static function menu() {
		
    	$menu = array();
    	$menu[] = '<a href="' . new shUrl("ping") . '" style="color: #fff; text-decoration: none;">'.Language::tag('SUBMENU_NETWORK_PING').'</a>';
    	$menu[] = '<a href="' . new shUrl("nslookup") . '" style="color: #fff; text-decoration: none;">'.Language::tag('SUBMENU_NETWORK_NSLOOKUP').'</a>';
    	$menu[] = '<a href="' . new shUrl("dig") . '" style="color: #fff; text-decoration: none;">'.Language::tag('SUBMENU_NETWORK_SIG').'</a>';
    	
    	return implode('&nbsp;|&nbsp;', $menu);
		
	}
}
