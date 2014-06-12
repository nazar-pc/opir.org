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
	cs\Config,
	cs\Language,
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
		'address_uk' => 'string',
		'address_en' => 'string',
		'address_ru' => 'string',
		'lat'        => 'float',
		'lng'        => 'float',
		'district'   => 'int',
		'violations' => 'int'
	];

	protected function construct () {
		$this->cache = new Prefix('precincts');
	}
	protected function cdb () {
		return Config::instance()->module('Precincts')->db('precincts');
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
			$address,
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
		$clang = Language::instance()->clang;
		return $this->cache->get("$id/$clang", function () use ($id, $clang) {
			$data = $this->read_simple($id);
			if (!$data) {
				return false;
			}
			$data['id']      = (int)$data['id'];
			$data['lat']     = (float)$data['lat'];
			$data['lng']     = (float)$data['lng'];
			$data['address'] = $data["address_$clang"] ?: $data['address_uk'];
			unset(
				$data['address_uk'],
				$data['address_en'],
				$data['address_ru']
			);
			$data['district']   = (int)$data['district'];
			$data['violations'] = (int)$data['violations'];
			return $data;
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
	function get_districts () {
		return $this->cache->get('all/group_by_district', function () {
			$districts = $this->db()->qfa(
				"SELECT
					`district`,
					COUNT(`id`) AS `count`,
					SUM(`violations`) AS `violations`
				FROM `$this->table`
				WHERE `district` > 0
				GROUP BY `district`"
			);
			$locations = $this->db()->qfa(
				"SELECT
					`number`,
					`lat`,
					`lng`
				FROM `$this->table`
				WHERE `district` = 0"
			);
			$lats      = array_column($locations, 'lat', 'number');
			$lngs      = array_column($locations, 'lng', 'number');
			unset($locations);
			foreach ($districts as $i => &$d) {
				if (!isset($lats[$d['district']])) {
					unset($districts[$i]);
					continue;
				}
				$d['district']   = (int)$d['district'];
				$d['count']      = (int)$d['count'];
				$d['lat']        = (float)$lats[$d['district']];
				$d['lng']        = (float)$lngs[$d['district']];
				$d['violations'] = (int)$d['violations'];
			}
			unset($i, $d);
			return array_values($districts);
		});
	}
	/**
	 * Precincts search
	 *
	 * @param string       $text
	 * @param bool|float[] $coordinates
	 * @param int          $limit
	 *
	 * @return array|bool
	 */
	function search ($text, $coordinates = false, $limit = 20) {
		$order = 'ORDER BY `district` = 0 DESC, `id` ASC';
		if ($coordinates && isset($coordinates[0], $coordinates[1])) {
			$coordinates = _float($coordinates);
			$order       = "ORDER BY `district` = 0 DESC, SQRT(POW(`lat` - $coordinates[0], 2) + POW(`lng` - $coordinates[0], 2)) ASC";
		}
		$where  = [];
		$params = [];
		if (is_numeric($text)) {
			/**
			 * Search for precinct number
			 */
			if (strlen($text) > 3 || (int)$text > 225) {
				$where[]  = "`number` LIKE '%s%%'";
				$params[] = $text;
			} else {
				$where[]  = "`district` = '%s'";
				$params[] = $text;
			}
		} else {
			$where[]  = "(
				MATCH (`address_uk`) AGAINST ('%s' IN BOOLEAN MODE) > 0 OR
				MATCH (`address_en`) AGAINST ('%s' IN BOOLEAN MODE) > 0 OR
				MATCH (`address_ru`) AGAINST ('%s' IN BOOLEAN MODE) > 0
			)";
			$s        =
				'+'.implode(
					_trim(explode(
						' ',
						trim($text)
					)),
					'* +'
				).'*';
			$params[] = $s;
			$params[] = $s;
			$params[] = $s;
		}
		if ($where) {
			$where = 'WHERE '.implode(' AND ', $where);
		} else {
			$where = '';
		}
		return $this->db()->qfas([
			"SELECT `id`
			FROM `$this->table`
			$where
			$order
			LIMIT $limit",
			$params
		]);
	}
	/**
	 * Update number of violations for specified precinct
	 *
	 * @param int $precinct
	 */
	function update_violations ($precinct) {
		$precinct = (int)$precinct;
		$this->db_prime()->q(
			"UPDATE `$this->table`
			SET `violations` = (
				SELECT COUNT(`id`)
				FROM `{$this->table}_violations`
				WHERE
					`precinct`	= '%s' AND
					`status`	!= '%s'
			)
			WHERE `id` = '%s'
			LIMIT 1",
			$precinct,
			Violations::STATUS_DECLINED,
			$precinct
		);
		unset(
			$this->cache->$precinct,
			$this->cache->{'all/group_by_district'}
		);
	}
}
