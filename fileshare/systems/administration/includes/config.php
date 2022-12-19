<?php 

$GLOBALS['_CONFIG']['_shape_system'] = 'administration';

//Make sure user is logged in throughout the administration codespace.
shFilter::AddFilter(new UserauthFilter());

// Register access levels on user
$sth = MYSQLDatabase::connect()->query("SELECT id, access_key FROM user_access");
$access_declarations = $sth->fetchAll(PDO::FETCH_OBJ);

if ( $access_declarations ) {
	foreach ( $access_declarations AS $access_declaration ) {
		define($access_declaration->access_key, $access_declaration->id);
		$_SESSION['claims'][$access_declaration->access_key] = $access_declaration->id;
	}
}

//Load FS settings
$sth = MYSQLDatabase::connect()->query("SELECT setting, setting_value FROM setting");
$settings = $sth->fetchAll(PDO::FETCH_OBJ);
foreach ($settings as $setting) {
   $GLOBALS['_CONFIG']['SETTING'][$setting->setting] = $setting->setting_value;
}

// SMTP settings
shEmail::setSMTPAuth(true);
shEmail::setSMTPHost($GLOBALS['_CONFIG']['SETTING']['outgoing_smtp']);
shEmail::setSMTPPort($GLOBALS['_CONFIG']['SETTING']['outgoing_smtp_port']);
shEmail::setSMTPUsername($GLOBALS['_CONFIG']['SETTING']['outgoing_email']);
shEmail::setSMTPPassword($GLOBALS['_CONFIG']['SETTING']['outgoing_email_password']);
shEmail::setFrom($GLOBALS['_CONFIG']['SETTING']['outgoing_email_name'] . ' <'.$GLOBALS['_CONFIG']['SETTING']['outgoing_email'].'>');

$GLOBALS['_CONFIG']['LAYOUT'] = 'administration.php';
$GLOBALS['_CONFIG']['draw_menu'] = AdminMenu::draw_menu();
