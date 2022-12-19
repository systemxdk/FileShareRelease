<?php
/**
* Class generated using Uteeni ORM generator
* Created: 2022-11-23 09:00:52
 * @property integer $id
 * @method bool find_by_id(integer $id)
 * @method array find_all_by_id(integer $id)
 * @property string $section
 * @method bool find_by_section(string $section)
 * @method array find_all_by_section(string $section)
 * @property string $setting
 * @method bool find_by_setting(string $setting)
 * @method array find_all_by_setting(string $setting)
 * @property string $setting_value
 * @method bool find_by_setting_value(string $setting_value)
 * @method array find_all_by_setting_value(string $setting_value)
 * @property string $updated_by
 * @method bool find_by_updated_by(string $updated_by)
 * @method array find_all_by_updated_by(string $updated_by)
 * @property date $updated
 * @method bool find_by_updated(date $updated)
 * @method array find_all_by_updated(date $updated)
**/
class Setting extends ActiveRecord {


		public $table_name = 'setting';
		public $database = 'MYSQL';

		protected $properties = array(
								'id' => null,
								'section' => null,
								'setting' => null,
								'setting_value' => null,
								'updated_by' => null,
								'updated' => null
		);
		protected $meta = array(
								'id' => array( 'type' => 'integer', 'primary' => true, 'required' => true, 'default' => '', 'extra' => 'auto_increment'),
								'section' => array( 'type' => 'string', 'primary' => false, 'required' => true, 'default' => '', 'extra' => ''),
								'setting' => array( 'type' => 'string', 'primary' => false, 'required' => true, 'default' => '', 'extra' => ''),
								'setting_value' => array( 'type' => 'string', 'primary' => false, 'required' => false, 'default' => '', 'extra' => ''),
								'updated_by' => array( 'type' => 'string', 'primary' => false, 'required' => false, 'default' => '', 'extra' => ''),
								'updated' => array( 'type' => 'date', 'primary' => false, 'required' => false, 'default' => '', 'extra' => '')
		);

/* end_auto_generate
do_not_delete_this_comment */

}