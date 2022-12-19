<?php
/**
* Class generated using Uteeni ORM generator
* Created: 2022-11-22 21:29:39
 * @property integer $id
 * @method bool find_by_id(integer $id)
 * @method array find_all_by_id(integer $id)
 * @property string $username
 * @method bool find_by_username(string $username)
 * @method array find_all_by_username(string $username)
 * @property string $password
 * @method bool find_by_password(string $password)
 * @method array find_all_by_password(string $password)
 * @property string $salt
 * @method bool find_by_salt(string $salt)
 * @method array find_all_by_salt(string $salt)
 * @property string $firstname
 * @method bool find_by_firstname(string $firstname)
 * @method array find_all_by_firstname(string $firstname)
 * @property string $lastname
 * @method bool find_by_lastname(string $lastname)
 * @method array find_all_by_lastname(string $lastname)
 * @property string $email
 * @method bool find_by_email(string $email)
 * @method array find_all_by_email(string $email)
 * @property integer $access_level
 * @method bool find_by_access_level(integer $access_level)
 * @method array find_all_by_access_level(integer $access_level)
 * @property string $default_page
 * @method bool find_by_default_page(string $default_page)
 * @method array find_all_by_default_page(string $default_page)
 * @property integer $logins
 * @method bool find_by_logins(integer $logins)
 * @method array find_all_by_logins(integer $logins)
 * @property integer $phone
 * @method bool find_by_phone(integer $phone)
 * @method array find_all_by_phone(integer $phone)
 * @property timestamp $created
 * @method bool find_by_created(timestamp $created)
 * @method array find_all_by_created(timestamp $created)
 * @property date $last_login
 * @method bool find_by_last_login(date $last_login)
 * @method array find_all_by_last_login(date $last_login)
**/
class User extends ActiveRecord {


		public $table_name = 'user';
		public $database = 'MYSQL';

		protected $properties = array(
								'id' => null,
								'username' => null,
								'password' => null,
								'salt' => null,
								'firstname' => null,
								'lastname' => null,
								'email' => null,
								'access_level' => null,
								'default_page' => null,
								'logins' => null,
								'phone' => null,
								'created' => null,
								'last_login' => null
		);
		protected $meta = array(
								'id' => array( 'type' => 'integer', 'primary' => true, 'required' => true, 'default' => '', 'extra' => 'auto_increment'),
								'username' => array( 'type' => 'string', 'primary' => false, 'required' => true, 'default' => '', 'extra' => ''),
								'password' => array( 'type' => 'string', 'primary' => false, 'required' => true, 'default' => '', 'extra' => ''),
								'salt' => array( 'type' => 'string', 'primary' => false, 'required' => true, 'default' => '', 'extra' => ''),
								'firstname' => array( 'type' => 'string', 'primary' => false, 'required' => false, 'default' => '', 'extra' => ''),
								'lastname' => array( 'type' => 'string', 'primary' => false, 'required' => false, 'default' => '', 'extra' => ''),
								'email' => array( 'type' => 'string', 'primary' => false, 'required' => false, 'default' => '', 'extra' => ''),
								'access_level' => array( 'type' => 'integer', 'primary' => false, 'required' => true, 'default' => '1', 'extra' => ''),
								'default_page' => array( 'type' => 'string', 'primary' => false, 'required' => false, 'default' => '', 'extra' => ''),
								'logins' => array( 'type' => 'integer', 'primary' => false, 'required' => false, 'default' => '0', 'extra' => ''),
								'phone' => array( 'type' => 'integer', 'primary' => false, 'required' => false, 'default' => '', 'extra' => ''),
								'created' => array( 'type' => 'timestamp', 'primary' => false, 'required' => true, 'default' => 'current_timestamp()', 'extra' => ''),
								'last_login' => array( 'type' => 'date', 'primary' => false, 'required' => false, 'default' => '', 'extra' => '')
		);

/* end_auto_generate
do_not_delete_this_comment */

}