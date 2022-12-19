<?php

class AdministratorsSubmenu {
	
	public static function menu() {
		
    	$menu = array();
    	$menu[] = '<a href="/administration/administrators/useraccess" style="color: #fff; text-decoration: none;">'.Language::tag('TEXT_ADMINISTRATOR_USERACCESS').'</a>';
    	
    	return implode('&nbsp;|&nbsp;', $menu);
		
	}
}
