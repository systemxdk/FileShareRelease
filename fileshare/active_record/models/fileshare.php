<?php
/**
* Class generated using Uteeni ORM generator
* Created: 2022-12-02 12:29:11
 * @property integer $id
 * @method bool find_by_id(integer $id)
 * @method array find_all_by_id(integer $id)
 * @property integer $user_id
 * @method bool find_by_user_id(integer $user_id)
 * @method array find_all_by_user_id(integer $user_id)
 * @property integer $active_days
 * @method bool find_by_active_days(integer $active_days)
 * @method array find_all_by_active_days(integer $active_days)
 * @property date $expiration
 * @method bool find_by_expiration(date $expiration)
 * @method array find_all_by_expiration(date $expiration)
 * @property enum $status
 * @method bool find_by_status(enum $status)
 * @method array find_all_by_status(enum $status)
 * @property date $updated
 * @method bool find_by_updated(date $updated)
 * @method array find_all_by_updated(date $updated)
 * @property string $updated_by
 * @method bool find_by_updated_by(string $updated_by)
 * @method array find_all_by_updated_by(string $updated_by)
 * @property date $created
 * @method bool find_by_created(date $created)
 * @method array find_all_by_created(date $created)
 * @property string $created_by
 * @method bool find_by_created_by(string $created_by)
 * @method array find_all_by_created_by(string $created_by)
**/
class Fileshare extends ActiveRecord {


		public $table_name = 'fileshare';
		public $database = 'MYSQL';

		protected $properties = array(
								'id' => null,
								'user_id' => null,
								'active_days' => null,
								'expiration' => null,
								'status' => null,
								'updated' => null,
								'updated_by' => null,
								'created' => null,
								'created_by' => null
		);
		protected $meta = array(
								'id' => array( 'type' => 'integer', 'primary' => true, 'required' => true, 'default' => '', 'extra' => 'auto_increment'),
								'user_id' => array( 'type' => 'integer', 'primary' => false, 'required' => true, 'default' => '', 'extra' => ''),
								'active_days' => array( 'type' => 'integer', 'primary' => false, 'required' => false, 'default' => '0', 'extra' => ''),
								'expiration' => array( 'type' => 'date', 'primary' => false, 'required' => false, 'default' => '', 'extra' => ''),
								'status' => array( 'type' => 'enum', 'primary' => false, 'required' => false, 'default' => 'ACTIVE', 'extra' => ''),
								'updated' => array( 'type' => 'date', 'primary' => false, 'required' => false, 'default' => '', 'extra' => ''),
								'updated_by' => array( 'type' => 'string', 'primary' => false, 'required' => false, 'default' => '', 'extra' => ''),
								'created' => array( 'type' => 'date', 'primary' => false, 'required' => true, 'default' => '', 'extra' => ''),
								'created_by' => array( 'type' => 'string', 'primary' => false, 'required' => true, 'default' => '', 'extra' => '')
		);

/* end_auto_generate
do_not_delete_this_comment */

}