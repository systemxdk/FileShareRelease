<?php

class UsersSubmenu {
	
	public static function menu() {
		
    	$menu = array();
    	$menu[] = '<a href="/administration/users/add" style="color: #fff; text-decoration: none;">'.Language::tag('TEXT_USER_ADD').'</a>';
    	
    	return implode('&nbsp;|&nbsp;', $menu);
		
	}
}
