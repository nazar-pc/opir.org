<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use			cs\Cache\Prefix,
			cs\User,
			cs\CRUD,
			cs\Singleton,
			cs\plugins\SimpleImage\SimpleImage;
/**
 * @method static \cs\modules\Home\Events instance($check = false)
 */
class Events {
	use	CRUD,
		Singleton;

	/**
	 * @var Prefix
	 */
	protected $cache;
	protected $table		= '[prefix]events';
	protected $data_model	= [
		'id'			=> 'int',
		'user'			=> 'id',
		'category'		=> 'id',
		'added'			=> 'id',
		'timeout'		=> 'id',
		'lat'			=> 'float',
		'lng'			=> 'float',
		'visible'		=> 'id',
		'text'			=> 'text',
		'time'			=> 'int',
		'time_interval'	=> 'int',
		'img'			=> 'text',
		'confirmed'		=> 'int'
	];

	protected function construct () {
		if (!file_exists(STORAGE.'/events')) {
			mkdir(STORAGE.'/events');
		}
		$this->cache	= new Prefix('events');
	}
	protected function cdb () {
		return '0';
	}
	/**
	 * Add new event
	 *
	 * @param $category
	 * @param $timeout
	 * @param $lat
	 * @param $lng
	 * @param $visible
	 * @param $text
	 * @param $time
	 * @param $time_interval
	 * @param $img
	 *
	 * @return bool|int
	 */
	function add ($category, $timeout, $lat, $lng, $visible, $text, $time, $time_interval, $img) {
		$User	= User::instance();
		if ($visible == 2) {
			$visible	= array_filter(
				$User->get_groups(),
				function ($group) {
					return $group > 3;
				}
			) ?: [0];
			$visible	= $visible[0];
		}
		$img	= source_by_url($img);
		if ($img) {
			(new SimpleImage($img))->thumbnail(260, 240)->save($img = STORAGE.'/events/'.md5(MICROTIME.'_'.$User->id).'.png', 100);
			$img	= url_by_source($img);
		}
		if ($this->create_simple([
			$User->id,
			(int)$category,
			TIME,
			$timeout ? TIME + max(0, (int)$timeout) : 0,
			$lat,
			$lng,
			$visible,
			$text,
			$time,
			$time_interval,
			$img,
			$visible == AUTOMAIDAN_GROUP ? 0 : 1
		])) {
			unset($this->cache->all);
			return true;
		}
		return false;
	}
	/**
	 * Get event
	 *
	 * @param int|int[]	$id
	 *
	 * @return array|array[]|bool
	 */
	function get ($id) {
		$User		= User::instance();
		$admin		= $User->admin();
		$user_id	= $User->id;
		$groups		= $User->get_groups();
		$Cache		= $this->cache;
		if (is_array($id)) {
			foreach ($id as &$i) {
				$i	= (int)$i;
				$i	= $Cache->get("$i/$user_id", function () use ($i, $User, $admin, $user_id, $groups) {
					return $this->get_internal($i, $User, $admin, $user_id, $groups);
				});
			}
			return $id;
		}
		$id			= (int)$id;
		return $this->cache->get("$id/$user_id", function () use ($id, $User, $admin, $user_id, $groups) {
			return $this->get_internal($id, $User, $admin, $user_id, $groups);
		});
	}
	/**
	 * Get event
	 *
	 * @param     $id
	 * @param     $User
	 * @param     $admin
	 * @param     $user_id
	 * @param     $groups
	 *
	 * @return array|bool
	 */
	protected function get_internal ($id, $User, $admin, $user_id, $groups) {
		if (!$id) {
			return false;
		}
		if ($admin) {
			$return	= $this->db()->qf([
				"SELECT *
				FROM `$this->table`
				WHERE
					`id` = '%s'",
				$id
			]);
			$return['text']	= str_replace('&apos;', "'", $return['text']);
			return $return;
		}
		if (in_array(AUTOMAIDAN_COORD_GROUP, $groups ?: [])) {
			$return	= $this->db()->qf([
				"SELECT
					`id`,
					`user`,
					`category`,
					`added`,
					`timeout`,
					`lat`,
					`lng`,
					`text`,
					`time`,
					`time_interval`,
					`img`,
					`confirmed`,
					`assigned_to`
				FROM `$this->table`
				WHERE
					(
						`confirmed`	= 0 OR
						`category` IN (1, 3, 6, 7, 8, 17, 21, 22)
					) AND
					`id`		= '%s'",	// Magic numbers - if of categories, where driver can add events
				$id
			]);
			if ($return) {
				$return['user_login']		= $User->get('login', $return['user']);
				$return['confirmed_login']	= $return['confirmed'] && $return['confirmed'] != 1 ? $User->get('login', $return['confirmed']) : '';
				$return['assigned_login']	= $return['assigned_to'] ? $User->get('login', $return['assigned_to']) : '';
				$return['confirmed']		= (int)(bool)$return['confirmed'];
				$return['text']				= str_replace('&apos;', "'", $return['text']);
			}
			return $return;
		}
		$groups[]	= 0;
		if ($User->user()) {
			$groups[]	= 1;
		}
		$groups		= implode(',', $groups);
		$return	= $this->db()->qf([
			"SELECT
				`id`,
				`user`,
				`category`,
				`added`,
				`timeout`,
				`lat`,
				`lng`,
				`text`,
				`time`,
				`time_interval`,
				`img`,
				`confirmed`
			FROM `$this->table`
			WHERE
				(
					(
						`visible` IN($groups) AND
						`confirmed`	> 0
					) OR
					`user`			= $user_id OR
					`assigned_to`	= $user_id
				) AND
				`id` = '%s'",
			$id
		]);
		if ($return) {
			if ($return['user'] != $user_id) {
				unset($return['user']);
			}
			$return['confirmed']	= (int)(bool)$return['confirmed'];
			$return['text']			= str_replace('&apos;', "'", $return['text']);
		}
		return $return;
	}
	/**
	 * Set event
	 *
	 * @param $id
	 * @param $timeout
	 * @param $lat
	 * @param $lng
	 * @param $visible
	 * @param $text
	 * @param $time
	 * @param $time_interval
	 * @param $img
	 *
	 * @return bool|int
	 */
	function set ($id, $timeout, $lat, $lng, $visible, $text, $time, $time_interval, $img) {
		$data	= $this->get($id);
		$User	= User::instance();
		$id		= (int)$id;
		$groups	= $User->get_groups();
		if (in_array(AUTOMAIDAN_GROUP, $groups)) {
			return false;
		}
		if ($visible == 2) {
			$visible	= array_filter(
				$groups,
				function ($group) {
					return $group > 3;
				}
			)[0];
		}
		if ($img != $data['img'] && $img) {
			(new SimpleImage($img))->thumbnail(260, 240)->save($img = STORAGE.'/events/'.md5(MICROTIME.'_'.$User->id).'.png', 100);
			$img	= url_by_source($img);
			unlink(source_by_url($data['img']));
		}
		if ($this->update_simple([
			$data['id'],
			$data['user'],
			$data['category'],
			TIME,
			$timeout ? TIME + max(0, (int)$timeout) : 0,
			$lat,
			$lng,
			$visible,
			$text,
			$time,
			$time_interval,
			$img,
			$data['confirmed']
		])) {
			unset($this->cache->$id);
			return true;
		}
		return false;
	}
	/**
	 * Get event, to which driver is assigned
	 *
	 * @return array|bool
	 */
	function check_is_assigned () {
		$User	= User::instance();
		return $this->get(
			$this->db_prime()->qfs([
				"SELECT `id`
				FROM `$this->table`
				WHERE `assigned_to` = '%s'
				LIMIT 1",
				$User->id
			])
		);
	}
	/**
	 * Assign driver for event checking
	 *
	 * @param $id
	 * @param $driver
	 *
	 * @return bool
	 */
	function check_assign ($id, $driver) {
		$id			= (int)$id;
		$driver		= (int)$driver;
		$data	= $this->db()->qf([
			"SELECT `user`, `confirmed`, `assigned_to`
			FROM `$this->table`
			WHERE `id` = '%s'",
			$id
		]);
		if ($data['user'] == $driver || $data['confirmed'] || $data['assigned_to']) {
			return false;
		}
		if ($this->db_prime()->q(
			"UPDATE `$this->table`
			SET `assigned_to` = '%s'
			WHERE `id` = '%s'
			LIMIT 1",
			$driver,
			$id
		)) {
			$Drivers	= Drivers::instance();
			$driver		= $Drivers->get($driver);
			$Drivers->set($driver['lat'], $driver['lng'], 1, $driver['id']);
			unset($this->cache->$id);
			return true;
		}
		return false;
	}
	/**
	 * Confirm event
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	function check_confirm ($id) {
		$id		= (int)$id;
		$data	= $this->db()->qf([
			"SELECT `user`, `confirmed`
			FROM `$this->table`
			WHERE `id` = '%s'",
			$id
		]);
		$User	= User::instance();
		if ($data['user'] == $User->id || $data['confirmed']) {
			return false;
		}
		if ($this->db_prime()->q(
			"UPDATE `$this->table`
			SET
				`confirmed`		= '%s',
				`assigned_to`	= 0
			WHERE `id` = '%s'
			LIMIT 1",
			$User->id,
			$id
		)) {
			$Drivers	= Drivers::instance();
			$driver		= $Drivers->get($User->id);
			$Drivers->set($driver['lat'], $driver['lng'], 0, $driver['id']);
			unset($this->cache->$id);
			return true;
		}
		return false;
	}
	/**
	 * Refuse event checking
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	function check_refuse ($id) {
		$id		= (int)$id;
		$data	= $this->db()->qf([
			"SELECT `user`, `confirmed`, `assigned_to`
			FROM `$this->table`
			WHERE `id` = '%s'",
			$id
		]);
		$User	= User::instance();
		if ($data['user'] == $User->id || $data['confirmed'] || $data['assigned_to'] != $User->id) {
			return false;
		}
		if ($this->db_prime()->q(
			"UPDATE `$this->table`
			SET `assigned_to` = 0
			WHERE `id` = '%s'
			LIMIT 1",
			$id
		)) {
			$Drivers	= Drivers::instance();
			$driver		= $Drivers->get($User->id);
			$Drivers->set($driver['lat'], $driver['lng'], 0, $driver['id']);
			unset($this->cache->$id);
			return true;
		}
		return false;
	}
	/**
	 * Delete event
	 *
	 * @param int	$id
	 *
	 * @return array|bool
	 */
	function del ($id) {
		$id		= (int)$id;
		$data	= $this->get($id);
		if ($data['img']) {
			unlink(source_by_url($data['img']));
		}
		if ($this->db()->q(
			"DELETE FROM `$this->table`
			WHERE `id` = '%s'
			LIMIT 1",
			$id
		)) {
			unset($this->cache->$id);
			return true;
		}
		return false;
	}
	/**
	 * Get all events
	 *
	 * @return array|bool
	 */
	function get_all () {
		$user_id	= User::instance()->id;
		$data		= $this->cache->{"all/$user_id"};
		if (!is_array($data) || $data['timeout'] < TIME) {
			$data	= $this->get_data_internal();
			$this->cache->{"all/$user_id"}	= [
				'timeout'	=> TIME + 5,
				'data'		=> $data
			];
			return $this->get($data);
		}
		return $this->get($data['data']);
	}

	protected function get_data_internal () {
		$User		= User::instance();
		$admin		= $User->admin();
		$user_id	= $User->id;
		if ($admin) {
			return $this->db()->qfas([
				"SELECT `id`
				FROM `$this->table`
				WHERE
					(
						`timeout`	> '%s' OR
						`timeout`	= 0
					) AND
					`lat`		!= 0 AND
					`lng`		!= 0
				ORDER BY `added` DESC",
				TIME
			]);
		}
		$groups		= $User->get_groups();
		if (in_array(AUTOMAIDAN_COORD_GROUP, $groups ?: [])) {
			return $this->db()->qfas([
				"SELECT `id`
				FROM `$this->table`
				WHERE
					(
						`timeout`	> '%s' OR
						`timeout`	= 0
					) AND
					(
						`confirmed`	= 0 OR
						`category` IN (1, 3, 6, 7, 8, 17, 21, 22)
					) AND
					`lat`		!= 0 AND
					`lng`		!= 0
				ORDER BY `added` DESC",	// Magic numbers - if of categories, where driver can add events
				TIME
			]);
		}
		$groups[]	= 0;
		if ($User->user()) {
			$groups[]	= 1;
		}
		$groups		= implode(',', $groups);
		return $this->db()->qfas([
			"SELECT `id`
			FROM `$this->table`
			WHERE
				(
					(
						`visible` IN($groups) AND
						`confirmed`	> 0
					) OR
					`user`	= $user_id
				) AND
				(
					`timeout`	> '%s' OR
					`timeout`	= 0
				) AND
				`lat`	!= 0 AND
				`lng`	!= 0
			ORDER BY `added` DESC",
			TIME
		]);
	}
}
