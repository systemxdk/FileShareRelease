<?php

class AdminMenu {
	
    static public function draw_menu() {
    	
        $menu = array();
        if (UserAccessFilter::Verify(USER_REGULAR)) $menu[] = '<a href="' . new shUrl('administration/account/index') . '" style="color: #fff; text-decoration: none;">'.Language::tag('MENU_ACCOUNT').'</a>';
    	if (UserAccessFilter::Verify(USER_ADMIN)) $menu[] = '<a href="' . new shUrl('administration/fileshare/index') . '" style="color: #fff; text-decoration: none;">'.Language::tag('MENU_FILESHARE').'</a>';
    	if (UserAccessFilter::Verify(USER_ADMIN)) $menu[] = '<a href="' . new shUrl('administration/users/index') . '" style="color: #fff; text-decoration: none;">'.Language::tag('MENU_USERS').'</a>';
    	if (UserAccessFilter::Verify(USER_ADMIN)) $menu[] = '<a href="' . new shUrl('administration/administrators/index') . '" style="color: #fff; text-decoration: none;">'.Language::tag('MENU_ADMINISTRATOR').'</a>';
    	if (UserAccessFilter::Verify(USER_ADMIN)) $menu[] = '<a href="' . new shUrl('administration/network/ping') . '" style="color: #fff; text-decoration: none;">'.Language::tag('MENU_NETWORK').'</a>';
    	if (UserAccessFilter::Verify(USER_ADMIN)) $menu[] = '<a href="' . new shUrl('administration/settings/index') . '" style="color: #fff; text-decoration: none;">'.Language::tag('MENU_SETTINGS').'</a>';
    	if (UserAccessFilter::Verify(USER_ADMIN | USER_TRANSLATOR)) $menu[] = '<a href="' . new shUrl('administration/language/index') . '" style="color: #fff; text-decoration: none;">'.Language::tag('MENU_LANGUAGE').'</a>';
    	$menu[] = '<a href="' . new shUrl('administration/about/index') . '" style="color: #fff; text-decoration: none;">'.Language::tag('MENU_ABOUT').'</a>';
    	
    	return implode(' | ', $menu);
    }
}
