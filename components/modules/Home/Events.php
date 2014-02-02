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
		return $this->create_simple([
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
		]);
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
		if (in_array(AUTOMAIDAN_COORD_GROUP, $groups)) {
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
						`confirmed`	= 0 OR
						`category` IN (1, 3, 6, 7, 8, 17, 21, 22)
					) AND
					`id`		= '%s'",	// Magic numbers - if of categories, where driver can add events
				$id
			]);
			if ($return['user'] != $user_id) {
				unset($return['user']);
			}
			$return['confirmed']	= (int)(bool)$return['confirmed'];
			$return['text']			= str_replace('&apos;', "'", $return['text']);
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
					`user`	= $user_id
				) AND
				`id` = '%s'",
			$id
		]);
		if ($return['user'] != $user_id) {
			unset($return['user']);
		}
		$return['confirmed']	= (int)(bool)$return['confirmed'];
		$return['text']			= str_replace('&apos;', "'", $return['text']);
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
		if ($visible == 2) {
			$visible	= array_filter(
				$User->get_groups(),
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
			$data['added'],
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
	 * Confirm event
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	function confirm ($id) {
		$data	= $this->db()->qf([
			"SELECT `user`, `confirm`
			FROM `$this->table`
			WHERE `id` = '%s'",
			$id
		]);
		$User	= User::instance();
		if ($data['user'] == $User->id || $data['confirmed']) {
			return false;
		}
		return $this->db_prime()->q(
			"UPDATE `$this->table`
			SET `confirmed` = '%s'
			WHERE `id` = '%s'
			LIMIT 1",
			$User->id,
			$id
		);
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
					`lng`		!= 0",
				TIME
			]);
		}
		$groups		= $User->get_groups();
		if (in_array(AUTOMAIDAN_COORD_GROUP, $groups)) {
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
					`lng`		!= 0",	// Magic numbers - if of categories, where driver can add events
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
				`category` NOT IN (1, 3, 6, 7, 8, 17, 21, 22) AND
				`lat`	!= 0 AND
				`lng`	!= 0",
			TIME
		]);
	}
}
