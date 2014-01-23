<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use			cs\User,
			cs\CRUD,
			cs\Singleton;
/**
 * @method static \cs\modules\Home\Events_categories instance($check = false)
 */
class Events_categories {
	use	CRUD,
		Singleton;

	protected $table		= '[prefix]events_categories';
	protected $data_model	= [
		'id'	=> 'int',
		'name'	=> 'text'
	];

	protected function cdb () {
		return '0';
	}
	/**
	 * Get events category
	 *
	 * @param $id
	 *
	 * @return array|bool
	 */
	function get ($id) {
		return $this->read_simple($id);
	}
	/**
	 * Get all events categories
	 *
	 * @return array|bool
	 */
	function get_all () {
		return $this->db()->qfa(
			"SELECT *
			FROM `$this->table`"
		);
	}
}
