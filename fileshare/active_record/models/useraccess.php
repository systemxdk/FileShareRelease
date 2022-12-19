<?php
/**
* Class generated using Uteeni ORM generator
* Created: 2022-11-17 20:17:39
 * @property integer $id
 * @method bool find_by_id(integer $id)
 * @method array find_all_by_id(integer $id)
 * @property string $access_key
 * @method bool find_by_access_key(string $access_key)
 * @method array find_all_by_access_key(string $access_key)
 * @property string $description
 * @method bool find_by_description(string $description)
 * @method array find_all_by_description(string $description)
 * @property date $created
 * @method bool find_by_created(date $created)
 * @method array find_all_by_created(date $created)
**/
class UserAccess extends ActiveRecord {


		public $table_name = 'user_access';
		public $database = 'MYSQL';

		protected $properties = array(
								'id' => null,
								'access_key' => null,
								'description' => null,
								'created' => null
		);
		protected $meta = array(
								'id' => array( 'type' => 'integer', 'primary' => true, 'required' => true, 'default' => '', 'extra' => ''),
								'access_key' => array( 'type' => 'string', 'primary' => false, 'required' => true, 'default' => '', 'extra' => ''),
								'description' => array( 'type' => 'string', 'primary' => false, 'required' => true, 'default' => '', 'extra' => ''),
								'created' => array( 'type' => 'date', 'primary' => false, 'required' => true, 'default' => '', 'extra' => '')
		);

/* end_auto_generate
do_not_delete_this_comment */

}