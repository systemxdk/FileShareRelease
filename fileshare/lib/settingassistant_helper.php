<?php

class SettingAssistant {
    
    static public function get($key) {
        
        //Load setting
        $sth = MYSQLDatabase::connect()->query("SELECT setting_value FROM setting WHERE setting = '".addslashes($key)."'");
        $setting = $sth->fetch(PDO::FETCH_OBJ);
        
        if ($setting) {
            return $setting->setting_value;
        }
    }
    
}
