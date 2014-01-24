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
 * @method static \cs\modules\Home\Events instance($check = false)
 */
class Events {
	use	CRUD,
		Singleton;

	protected $table		= '[prefix]events';
	protected $data_model	= [
		'id'		=> 'int',
		'user'		=> 'id',
		'category'	=> 'id',
		'added'		=> 'id',
		'timeout'	=> 'id',
		'lat'		=> 'float',
		'lng'		=> 'float',
		'visible'	=> 'id',
		'text'		=> 'text',
		'urgency'	=> null
	];

	protected function constructor () {
		$this->data_model['urgency']	= function ($in) {
			switch ($in) {
				default:
					$in	= 'unknown';
				case 'can-wait':
				case 'urgent':
			}
			return $in;
		};
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
	 * @param $urgency
	 *
	 * @return bool|int
	 */
	function add ($category, $timeout, $lat, $lng, $visible, $text, $urgency) {
		$User	= User::instance();
		if ($visible == 2) {
			$visible	= array_filter(
				$User->get_groups(),
				function ($group) {
					return $group > 3;
				}
			)[0];
		}
		return $this->create_simple([
			$User->id,
			(int)$category,
			TIME,
			TIME + max(0, (int)$timeout),
			$lat,
			$lng,
			$visible,
			$text,
			$urgency
		]);
	}
	/**
	 * Get event
	 *
	 * @param int	$id
	 *
	 * @return array|bool
	 */
	function get ($id) {
		$User		= User::instance();
		$admin		= $User->admin();
		$user_id	= $User->id;
		if ($admin) {
			return $this->db()->qf(
				"SELECT *
				FROM `$this->table`
				WHERE
					`id` = '%s'",
				$id
			);
		}
		$groups		= $User->get_groups();
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
				`added`,
				`timeout`,
				`lat`,
				`lng`,
				`text`,
				`urgency`
			FROM `$this->table`
			WHERE
				(
					`visible` IN($groups) OR
					`user`	= $user_id
				) AND
				`id` = '%s'",
			$id
		]);
		if (!$admin && $return['user'] != $user_id) {
			unset($return['id']);
		}
		unset($return['user']);
		return $return;
	}
	/**
	 * Delete event
	 *
	 * @param int	$id
	 *
	 * @return array|bool
	 */
	function del ($id) {
		return $this->db()->q(
			"DELETE FROM `$this->table`
			WHERE `id` = '%s'
			LIMIT 1",
			$id
		);
	}
	/**
	 * Get all events
	 *
	 * @return array|bool
	 */
	function get_all () {
		$User		= User::instance();
		$admin		= $User->admin();
		$user_id	= $User->id;
		if ($admin) {
			return $this->db()->qfa([
				"SELECT *
				FROM `$this->table`
				WHERE
					`timeout`	> '%s'",
				TIME
			]);
		}
		$groups		= $User->get_groups();
		$groups[]	= 0;
		if ($User->user()) {
			$groups[]	= 1;
		}
		$groups		= implode(',', $groups);
		$return 	= $this->db()->qfa([
			"SELECT
				`id`,
				`user`,
				`category`,
				`added`,
				`timeout`,
				`added`,
				`timeout`,
				`lat`,
				`lng`,
				`text`,
				`urgency`
			FROM `$this->table`
			WHERE
				(
					`visible` IN($groups) OR
					`user`	= $user_id
				) AND
				`timeout`	> '%s'",
			TIME
		]);
		foreach ($return as &$r) {
			if (!$admin && $r['user'] != $user_id) {
				unset($r['id']);
			}
			unset($r['user']);
		}
		return $return;
	}
}
