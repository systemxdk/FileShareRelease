<?php

class SettingsSubmenu {
	
	public static function menu() {
    	$menu = array();
    	$menu[] = '<a href="' . new shUrl("email") . '" style="color: #fff; text-decoration: none;">'.Language::tag('SUBMENU_SETTINGS_EMAIL').'</a>';
    	$menu[] = '<a href="' . new shUrl("fileshare") . '" style="color: #fff; text-decoration: none;">'.Language::tag('SUBMENU_SETTINGS_FILESHARE').'</a>';
    	
    	return implode('&nbsp;|&nbsp;', $menu);
	}
}
