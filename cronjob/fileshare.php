<?php

//Load the activerecord orm
require_once dirname(__FILE__) . "/../fileshare/active_record/activerecord.php";
require_once dirname(__FILE__) . "/../fileshare/active_record/database.php";

//Load models
require_once dirname(__FILE__) . "/../fileshare/active_record/models/fileshare.php";
require_once dirname(__FILE__) . "/../fileshare/active_record/models/filesharequeue.php";
require_once dirname(__FILE__) . "/../fileshare/active_record/models/user.php";

//Load helpers
require_once dirname(__FILE__) . "/../fileshare/lib/fileshareassistant_helper.php";
require_once dirname(__FILE__) . "/../fileshare/lib/settingassistant_helper.php";
require_once dirname(__FILE__) . "/../fileshare/lib/vsftpd_helper.php";
require_once dirname(__FILE__) . "/../fileshare/shape/lib/pear_helper.php";
require_once dirname(__FILE__) . "/../fileshare/shape/lib/_shape_email.php";


try {
    //Load and register settings coming from fileshare admin
    Fileshareassistant::load();
} catch (Exception $e) {
    die("FATAL: Cronjob could not be loaded");
}

//Fetch pending jobs
$queue = new Filesharequeue();
$jobs = $queue->find_all("status = '" . FileshareAssistant::$STATUS_PENDING . "'", NULL, 0, "created", "ASC");

if (!count($jobs)) die(); //No queue.

foreach ($jobs as $job) {
    
    try {
        $method = strtolower($job->action); //create
        if (method_exists("Fileshareassistant", $job->action)) {
            
            //Put a started datetime marker on the job beforehand
            $job->mark("started");
            
            //Perform the job
            Fileshareassistant::$method($job);
            
            //Finish by putting a stamp
            $job->mark("completed");
            
        }
    } catch (Exception $e) {
        $job->error($e->getMessage());
    }
    
}

?>