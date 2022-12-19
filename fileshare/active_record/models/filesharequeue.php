<?php
/**
* Class generated using Uteeni ORM generator
* Created: 2022-12-02 12:29:11
 * @property integer $id
 * @method bool find_by_id(integer $id)
 * @method array find_all_by_id(integer $id)
 * @property integer $fileshare_id
 * @method bool find_by_fileshare_id(integer $fileshare_id)
 * @method array find_all_by_fileshare_id(integer $fileshare_id)
 * @property integer $user_id
 * @method bool find_by_user_id(integer $user_id)
 * @method array find_all_by_user_id(integer $user_id)
 * @property enum $action
 * @method bool find_by_action(enum $action)
 * @method array find_all_by_action(enum $action)
 * @property enum $status
 * @method bool find_by_status(enum $status)
 * @method array find_all_by_status(enum $status)
 * @property string $message
 * @method bool find_by_message(string $message)
 * @method array find_all_by_message(string $message)
 * @property date $created
 * @method bool find_by_created(date $created)
 * @method array find_all_by_created(date $created)
 * @property date $started
 * @method bool find_by_started(date $started)
 * @method array find_all_by_started(date $started)
 * @property date $completed
 * @method bool find_by_completed(date $completed)
 * @method array find_all_by_completed(date $completed)
**/
class FileshareQueue extends ActiveRecord {


		public $table_name = 'fileshare_queue';
		public $database = 'MYSQL';

		protected $properties = array(
								'id' => null,
								'fileshare_id' => null,
								'user_id' => null,
								'action' => null,
								'status' => null,
								'message' => null,
								'created' => null,
								'started' => null,
								'completed' => null
		);
		protected $meta = array(
								'id' => array( 'type' => 'integer', 'primary' => true, 'required' => true, 'default' => '', 'extra' => 'auto_increment'),
								'fileshare_id' => array( 'type' => 'integer', 'primary' => false, 'required' => true, 'default' => '', 'extra' => ''),
								'user_id' => array( 'type' => 'integer', 'primary' => false, 'required' => true, 'default' => '', 'extra' => ''),
								'action' => array( 'type' => 'enum', 'primary' => false, 'required' => false, 'default' => '', 'extra' => ''),
								'status' => array( 'type' => 'enum', 'primary' => false, 'required' => false, 'default' => 'PENDING', 'extra' => ''),
								'message' => array( 'type' => 'string', 'primary' => false, 'required' => false, 'default' => '', 'extra' => ''),
								'created' => array( 'type' => 'date', 'primary' => false, 'required' => true, 'default' => '', 'extra' => ''),
								'started' => array( 'type' => 'date', 'primary' => false, 'required' => false, 'default' => '', 'extra' => ''),
								'completed' => array( 'type' => 'date', 'primary' => false, 'required' => false, 'default' => '', 'extra' => '')
		);

/* end_auto_generate
do_not_delete_this_comment */
  
    function error($message) {
        if (!$this->id) return; //Model not loaded
        
        //Update the queue row with ERROR status.
        $this->status = "ERROR";
        $this->message = $message;
        $this->update();
        
        //Update the ancestor fileshare row with ERROR status
        //This will effectively bring it out of cronjob reach until a dev has looked into the logged error.
        $fileshare = new Fileshare();
        $fileshare->find($this->fileshare_id);
        $fileshare->status = "ERROR";
        $fileshare->update();
    }
    
    function mark($column) {
        if (!$this->id) return; //Model not loaded
        
        //Fileshare queue
        $this->$column = "now()";
        
        //Fileshare
        $fileshare = new Fileshare();
        $fileshare->find($this->fileshare_id);
        
        //Toggle status column appropriately
        switch ($column) {
            case "started":
                $this->status = "STARTED";
            break;
            case "completed":
                $this->status = "COMPLETED";
                $fileshare->status = Fileshareassistant::$STATUS_ACTIVE;
            break;
        }
        
        $this->update(); //Update queue model
        $fileshare->update(); //Update ancestor fileshare model
    }
    
}