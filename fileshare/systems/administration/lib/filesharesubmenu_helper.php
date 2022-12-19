<?php

class FileshareSubmenu {
	
	public static function menu() {
		
    	$menu = array();
    	$menu[] = '<a href="/administration/fileshare/add" style="color: #fff; text-decoration: none;">'.Language::tag('TEXT_CREATE_ACCOUNT').'</a>';
    	
    	return implode('&nbsp;|&nbsp;', $menu);
		
	}
}
