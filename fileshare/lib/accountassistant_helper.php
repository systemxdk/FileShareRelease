<?php

class AccountAssistant {
    
    static function get_next_username() {
        $user = new User();
        list($max) = $user->find_by_sql("SELECT COUNT(*) as next_id FROM user WHERE access_level = 2");
        
        return sprintf("FS%04d", ++$max->next_id);
    }
}
