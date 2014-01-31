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
 * @method static \cs\modules\Home\Drivers instance($check = false)
 */
class Drivers {
	use	CRUD,
		Singleton;

	protected $table		= '[prefix]drivers';
	protected $data_model	= [
		'id'			=> 'int',
		'timeout'		=> 'id',
		'lat'			=> 'float',
		'lng'			=> 'float',
		'busy'			=> 'int'
	];

	protected function cdb () {
		return '0';
	}
	/**
	 * Get driver
	 *
	 * @param int|int[]	$id
	 *
	 * @return array|array[]|bool
	 */
	function get ($id) {
		return $this->read_simple($id);
	}
	/**
	 * Set driver
	 *
	 * @param $lat
	 * @param $lng
	 * @param $busy
	 *
	 * @return bool|int
	 */
	function set ($lat, $lng, $busy) {
		$User	= User::instance();
		return $this->db_prime()->q(
			"INSERT INTO `$this->table`
				(`id`, `timeout`, `lat`, `lng`, `busy`)
			VALUES
				('%s', '%s', '%s', '%s', '%s')
			ON DUPLICATE KEY UPDATE
				`timeout`	= VALUES(`timeout`),
				`lat`		= VALUES(`lat`),
				`lng`		= VALUES(`lng`),
				`busy`		= VALUES(`busy`)",
			$User->id,
			TIME + 30,
			$lat,
			$lng,
			(int)(bool)$busy
		);
	}
	/**
	 * Get all drivers
	 *
	 * @return array|bool
	 */
	function get_all () {
		return $this->get(
			$this->db()->qfas([
				"SELECT `id`
				FROM `$this->table`
				WHERE `timeout` > '%s'",
				TIME
			])
		);
	}
}
