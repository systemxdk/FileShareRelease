<?php

class vsftpd {
    
    static $config = "/etc/vsftpd.conf";
    static $password_file = "/etc/ftpd.passwd";
    
    static $base_directory = "/var/www/ftpchroot";
    static $home_directory = "/var/www/ftpchroot/%s";
    
    static $daemon_usr = "vsftpd";
    static $daemon_grp = "nogroup";
    
    static $chroot_perms = 0755; //oct
    
    static function password_generate() {
        
        //The CRYPT algorithm we're using with vsftpd restricts us from longer passwords than 8
        //This is due to the pam module we're using, todo: consider another.
        
        $length = 8; //We go to max of 8 in fileshare, of course.
        
        //We want the format [aaaddaaa], a=alpha, d=number
        
        $alpha = array(
            "a", "b", "c", "d", "e", 
            "f", "g", "h", "i", "j", 
            "k", "l", "m", "n", "o", 
            "p", "q", "r", "s", "t", 
            "u", "v", "w", "x", "y", "z"
        );
        
        $numbers = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
        
        $password = "";
        for ($i = 0; $i <= 7; $i++) {
            if ($i <= 2) $password .= $alpha[array_rand($alpha)]; // 3 first
            if ($i > 2 && $i < 5) $password .= $numbers[array_rand($numbers)]; // 2 mid
            if ($i >= 5) $password .= $alpha[array_rand($alpha)]; // 3 last
        }
        
        return $password;
    }
    
    static function get_users() {
        $users = array();
        
        $password_file_content = file_get_contents(self::$password_file);
        $password_users = array_filter(explode("\n", $password_file_content));
        
        foreach ($password_users as $user) {
            list($username, $crypt_hash) = explode(":", $user);
            
            $users[$username] = $crypt_hash;
        }
        
        return array_filter($users);
    }
    
    static function get_chrooted_dirs() {
        $chroots = array();
        foreach (glob(sprintf(self::$home_directory, "*"), GLOB_ONLYDIR) as $directories) {
            $chroots[] = basename($directories);
        }
        return $chroots;
    }
    
    function delete_user($username) {
        
        //Delete the htpasswd entry
        $cmd_template = "htpasswd -D %s %s 2>&1";
        $cmd = sprintf($cmd_template, self::$password_file, $username);
        $output = shell_exec($cmd);
        
        $htpasswd_deleted = (bool)strstr($output, "Deleting password for user");
        if (!$htpasswd_deleted) throw new Exception("Could not remove the htpasswd entry");
        
        //Remove chroot directory
        $cmd = sprintf("/bin/rm -Rf /var/www/ftpchroot/%s", $username);
        $output = shell_exec($cmd);
        
        return true; //No excp. so far, assume success
    }
    
    function change_password($username, $password) {
        $cmd_template = "echo %s | htpasswd -id %s %s 2>&1";
        $cmd = sprintf($cmd_template, $password, self::$password_file, $username);
        $output = shell_exec($cmd);
        
        return (bool)strstr($output, "Updating password for user");
    }
    
    function verify_password($username, $password) {
        $cmd_template = "echo %s | htpasswd -vid %s %s 2>&1";
        $cmd = sprintf($cmd_template, $password, self::$password_file, $username);
        $output = shell_exec($cmd);
        
        return (bool)strstr($output, "correct");
    }
    
    function create_user($username, $password) {
        $cmd_template = "echo %s | htpasswd -id %s %s 2>&1";
        $cmd = sprintf($cmd_template, $password, self::$password_file, $username);
        $output = shell_exec($cmd);
        
        return (bool)strstr($output, "Adding password for user");
    }
    
    function create_chroot($username) {
        $directory = sprintf(self::$home_directory, $username);
        
        if (is_dir($directory)) throw new Exception("Chroot directory already exists");
        mkdir($directory);
        if (!is_dir($directory)) throw new Exception("Chroot directory could not be created");
        
        chown($directory, self::$daemon_usr);
        chgrp($directory, self::$daemon_grp);
        chmod($directory, self::$chroot_perms);
        
        //Validate proper permission
        $perms = substr(sprintf('%o', fileperms($directory)), -4);
        if (octdec($perms) != self::$chroot_perms) throw new Exception("Chroot directory was created but has the wrong perms.");
        
        return true; //No excp. so far, assume success      
    }
    
}