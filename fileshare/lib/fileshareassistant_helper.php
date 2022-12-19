<?php

class FileshareAssistant {
    
    static $STATUS_PENDING = "PENDING";
    static $STATUS_ACTIVE = "ACTIVE";
    static $STATUS_INACTIVE = "INACTIVE";
    static $STATUS_ERROR = "ERROR";
    
    static function create($queue) {
        try {
            
            //Fetch the fileshare properties 
            $fileshare = new Fileshare();
            $fileshare->find($queue->fileshare_id);
            
            //Fetch the user record this is all about
            $user = new User();
            $user->find($queue->user_id);
            
            //Fetch some variables for later user
            $current_ftpusers = vsftpd::get_users();
            $current_chroots = vsftpd::get_chrooted_dirs();
            $password = vsftpd::password_generate();
            
            //Perform some checks
            if (in_array($user->username, $current_ftpusers)) throw new Exception("User already exists in FTP users."); //Inconsistency here, user exists in ftp passwd file but is being created, wth ?
            if (in_array($user->username, $current_chroots)) throw new Exception("Users FTP home-directory already exists on disk."); //Inconsistency here, user homedir exists on disk
            
            //Create the vsftp user
            if (!vsftpd::create_user($user->username, $password)) {
                throw new Exception("Error occured when creating the FTP user.");
            }
            
            //Create the vsftp chroot directory
            if (!vsftpd::create_chroot($user->username)) {
                throw new Exception("Error occured when creating the FTP chrooted env");
            }
            
            //Send password mail to user
            //This is the single only place we leak the password
            
            //TODO: If time allows it, seriously consider a template section in fileshare admin! Wysiwyg and go html maybe.
            
            $subject = "Velkommen til FileShare";
            $body = "";
            $body .= "Hej {$user->firstname}\n\n";
            $body .= "Du er blevet oprettet som bruger på FileShare, og kan nu logge på via FTP.\n\n";
            $body .= "Brugernavn: " . $user->username . "\n";
            $body .= "Adgangskode: " . $password . "\n\n";
            $body .= "FTP-oplysninger:\n";
            $body .= "Hostnavn: fileshare.systemx.dk\n";
            $body .= "Port: 21\n\n";
            $body .= "Med venlig hilsen\n";
            $body .= "FileShare teamet :-)";
            
            shEmail::send_smtp(array($user->email), OUTGOING_EMAIL_NAME, OUTGOING_EMAIL, $subject, $body);
            
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    static function delete($queue) {
        try {
            
            //Fetch the fileshare properties 
            $fileshare = new Fileshare();
            $fileshare->find($queue->fileshare_id);
            
            //Fetch the user record this is all about
            $user = new User();
            $user->find($queue->user_id);
            
            //User exist?
            $current_ftpusers = vsftpd::get_users();
            if (!array_key_exists($user->username, $current_ftpusers)) {
                throw new Exception("User does not exist and can not be deleted");
            }
            
            if (!vsftpd::delete_user($user->username)) {
                throw new Exception("Error occured when deleting the fileshare");
            }
            
            $fileshare->destroy();
            
            //Send notification about deleted acccount to user
            
            $subject = "Din FileShare konto er blevet lukket";
            $body = "";
            $body .= "Hej {$user->firstname}\n\n";
            $body .= "Din FileShare konto er lukket ned.\n\n";
            $body .= "Kom tilbage en anden gang.\n\n";
            $body .= "Med venlig hilsen\n";
            $body .= "FileShare teamet :-)";
            
            shEmail::send_smtp(array($user->email), OUTGOING_EMAIL_NAME, OUTGOING_EMAIL, $subject, $body);
            
        } catch (Exception $e) {
            throw $e; 
        }
    }
    
    static function change_password($queue) {
        try {
            
            //Fetch the fileshare properties 
            $fileshare = new Fileshare();
            $fileshare->find($queue->fileshare_id);
            
            //Fetch the user record this is all about
            $user = new User();
            $user->find($queue->user_id);
            
            //User exist?
            $current_ftpusers = vsftpd::get_users();
            if (!array_key_exists($user->username, $current_ftpusers)) {
                throw new Exception("User does not exist and the password can not be changed");
            }
            
            //Change the password
            $new_password = vsftpd::password_generate();
            
            if (!vsftpd::change_password($user->username, $new_password)) {
                throw new Exception("Error occured when changing the password");
            }
            
            if (!vsftpd::verify_password($user->username, $new_password)) {
                throw new Exception("Error occured when verifying the changed password");
            }
            
            //Send changed password mail to user
            
            $subject = "Din adgangskode til FileShare blev opdateret";
            $body = "";
            $body .= "Hej {$user->firstname}\n\n";
            $body .= "Din adgangskode til FileShare er blevet ændret, og du kan logge ind med følgende.\n\n";
            $body .= "Brugernavn: " . $user->username . "\n";
            $body .= "Adgangskode: " . $new_password . "\n\n";
            $body .= "FTP-oplysninger:\n";
            $body .= "Hostnavn: fileshare.systemx.dk\n";
            $body .= "Port: 21\n\n";
            $body .= "Med venlig hilsen\n";
            $body .= "FileShare teamet :-)";
            
            shEmail::send_smtp(array($user->email), OUTGOING_EMAIL_NAME, OUTGOING_EMAIL, $subject, $body);
            
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    static function queue($fileshare_id, $user_id, $action, $status) {
        try {
            $queue = new Filesharequeue();
            $queue->fileshare_id = $fileshare_id;
            $queue->user_id = $user_id;
            $queue->action = $action;
            $queue->status = $status;
            $queue->created = "now()";
            $queue->create();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    static function load() {
        try {
            
            //Fetch and load settings from database
            //We register them as uppercase constants for use in the scheduled cronjob
            $sth = MYSQLDatabase::connect()->query("SELECT setting as skey, setting_value as sval FROM setting");
            $settings = $sth->fetchAll(PDO::FETCH_OBJ);
            
            foreach ($settings as $s) {
                define(strtoupper($s->skey), $s->sval); //Register settings as constants
            }
            
            //Register smtp settings to the shEmail helper
            //This makes us capable of sending mail with php through 587 secure smtp
            shEmail::setSMTPHost(OUTGOING_SMTP);
            shEmail::setSMTPPort(OUTGOING_SMTP_PORT);
            shEmail::setSMTPAuth(true);
            shEmail::setSMTPUsername(OUTGOING_EMAIL);
            shEmail::setSMTPPassword(OUTGOING_EMAIL_PASSWORD);
            
        } catch (Exception $e) { 
            throw $e; //
        }
    }
}