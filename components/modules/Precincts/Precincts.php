<?php
/**
 * @package        Precincts
 * @category       modules
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
 */
namespace cs\modules\Precincts;

use
	cs\Cache\Prefix,
	cs\User,
	cs\CRUD,
	cs\Singleton;

/**
 * @method static \cs\modules\Precincts\Precincts instance($check = false)
 */
class Precincts {
	use
		CRUD,
		Singleton;

	/**
	 * @var Prefix
	 */
	protected $cache;
	protected $table      = '[prefix]precincts';
	protected $data_model = [
		'id'         => 'int',
		'number'     => 'int',
		'address'    => 'string',
		'lat'        => 'float',
		'lng'        => 'float',
		'district'   => 'int',
		'violations' => 'int'
	];

	protected function construct () {
		$this->cache = new Prefix('precincts');
	}
	protected function cdb () {
		return '0';
	}
	/**
	 * Add new precinct
	 *
	 * @param $number
	 * @param $address
	 * @param $lat
	 * @param $lng
	 * @param $district
	 *
	 * @return bool|int
	 */
	function add ($number, $address, $lat, $lng, $district) {
		$id = $this->create_simple([
			$number,
			$address,
			$lat,
			$lng,
			$district,
			0
		]);
		if ($id) {
			unset($this->cache->all);
			return $id;
		}
		return false;
	}
	/**
	 * Get precinct
	 *
	 * @param int|int[] $id
	 *
	 * @return array|array[]|bool
	 */
	function get ($id) {
		if (is_array($id)) {
			foreach ($id as &$i) {
				$i = $this->get($i);
			}
			return $id;
		}
		return $this->cache->get($id, function () use ($id) {
			return $this->read_simple($id);
		});
	}
	/**
	 * Get array of id of all precincts
	 *
	 * @return bool|int[]
	 */
	function get_all () {
		return $this->cache->get('all/ids', function () {
			return $this->db()->qfas(
				"SELECT `id`
				FROM `$this->table`"
			);
		});
	}
	/**
	 * Set precinct
	 *
	 * @param int|int[] $id
	 * @param           $number
	 * @param           $address
	 * @param           $lat
	 * @param           $lng
	 * @param           $district
	 * @param           $violations
	 *
	 * @return array|array[]|bool
	 */
	function set ($id, $number, $address, $lat, $lng, $district, $violations) {
		if ($this->update_simple([
			$id,
			$number,
			$address,
			$lat,
			$lng,
			$district,
			$violations
		])
		) {
			unset($this->cache->$id);
			return true;
		}
		return false;
	}
	/**
	 * Delete precinct
	 *
	 * @param int|int[] $id
	 *
	 * @return bool
	 */
	function del ($id) {
		if ($this->delete_simple($id)) {
			foreach ((array)$id as $i) {
				unset($this->cache->$i);
			}
			unset(
				$id,
				$this->cache->all
			);
			return true;
		}
		return false;
	}
	function group_by_district () {
		return $this->cache->get('all/group_by_district', function () {
			return $this->db()->qfa(
				"SELECT
					`district`,
					COUNT(`id`) AS `count`,
					AVG(`lat`) AS `lat`,
					AVG(`lng`) AS `lng`
				FROM `$this->table`
				GROUP BY `district`"
			);
		});
	}
}
