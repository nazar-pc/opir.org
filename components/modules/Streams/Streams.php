<?php
/**
 * @package		Streams
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Streams;
use			cs\Cache\Prefix,
			cs\User,
			cs\CRUD,
			cs\Singleton;
/**
 * @method static \cs\modules\Streams\Streams instance($check = false)
 */
class Streams {
	use	CRUD,
		Singleton;

	/**
	 * @var Prefix
	 */
	protected $cache;
	protected $table		= '[prefix]streams_streams';
	protected $data_model	= [
		'id'			=> 'int',
		'stream_url'	=> 'text',
		'lat'			=> 'float',
		'lng'			=> 'float',
		'added'			=> 'int',
		'approved'		=> 'int',
		'abuse'			=> 'int'
	];

	protected function construct () {
		$this->cache	= new Prefix('streams');
	}
	protected function cdb () {
		return '0';
	}
	/**
	 * Add new stream
	 *
	 * @param $stream_url
	 * @param $lat
	 * @param $lng
	 * @param $tags
	 *
	 * @return bool|int
	 */
	function add ($stream_url, $lat, $lng, $tags) {
		$id	= $this->create_simple([
			$stream_url,
			$lat,
			$lng,
			TIME,
			1,
			0
		]);
		if ($id) {
			if ($tags) {
				$this->db_prime()->insert(
					"INSERT IGNORE INTO `[prefix]streams_tags`
						(
							`title`
						) VALUES (
							'%s'
						)",
						array_map(function ($t) {
							return [$t];
						}, $tags),
					true
				);
				foreach ($tags as &$t) {
					$t	= $this->db_prime()->qfs([
						"SELECT `id`
						FROM `[prefix]streams_tags`
						WHERE `title` = '%s'
						LIMIT 1",
						$t
					]);
					$t	= [$t];
				}
				$this->db_prime()->insert(
					"INSERT IGNORE INTO `[prefix]streams_streams_tags`
						(
							`id`,
							`tag`
						) VALUES (
							$id,
							'%s'
						)",
					$tags,
					true
				);
			}
			return true;
		}
		unset($this->cache->all);
		return false;
	}
	/**
	 * Get stream
	 *
	 * @param int|int[]	$id
	 *
	 * @return array|array[]|bool
	 */
	function get ($id) {
		if (is_array($id)) {
			foreach ($id as &$i) {
				$i	= $this->get($i);
			}
			return $id;
		}
		$id	= (int)$id;
		if (User::instance()->admin()) {
			$streams	= $this->db()->qf([
				"SELECT *
				FROM `$this->table`
				WHERE `id` = '%s'
				LIMIT 1",
				$id
			]);
			foreach ($streams as &$stream) {
				$stream['tags']	= $this->db()->qfas([
					"SELECT `tag`
					FROM `[prefix]streams_streams_tags`
					WHERE `id` = '%s'",
					$stream['id']
				]);
			}
		}
		return $this->cache->get($id, function () use ($id) {
			$data			= $this->db()->qf([
				"SELECT
					`id`,
					`stream_url`,
					`lat`,
					`lng`
				FROM `$this->table`
				WHERE
					`id`		= '%s' AND
					`approved`	= 1 AND
					`abuse`		< 5
				LIMIT 1",
				$id
			]) ?: false;
			$data['tags']	= $this->db()->qfas([
				"SELECT `tag`
				FROM `[prefix]streams_streams_tags`
				WHERE `id` = '%s'",
				$id
			]);
			return $data;
		});
	}
	/**
	 * Approve added stream
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	function approve ($id) {
		$id			= (int)$id;
		if ($this->db_prime()->q(
			"UPDATE `$this->table`
			SET
				`approved`	= 1 AND
				`abuse`		= 0
			WHERE `id` = '%s'
			LIMIT 1",
			$id
		)) {
			unset(
				$this->cache->$id,
				$this->cache->all
			);
			return true;
		}
		return false;
	}
	/**
	 * Decline added stream
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	function decline ($id) {
		$id			= (int)$id;
		if ($this->db_prime()->q(
			"UPDATE `$this->table`
			SET `approved` = '-1'
			WHERE `id` = '%s'
			LIMIT 1",
			$id
		)) {
			unset(
				$this->cache->$id,
				$this->cache->all
			);
			return true;
		}
		return false;
	}
	/**
	 * Abuse stream
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	function abuse ($id) {
		$id			= (int)$id;
		if ($this->db_prime()->q(
			"UPDATE `$this->table`
			SET `abuse` = `abuse` + 1
			WHERE `id` = '%s'
			LIMIT 1",
			$id
		)) {
			unset(
				$this->cache->$id,
				$this->cache->all
			);
			return true;
		}
		return false;
	}
	/**
	 * Get all streams
	 *
	 * @return array|bool
	 */
	function get_all () {
		return $this->cache->get('all', function () {
			return $this->db()->qfas(
				"SELECT `id`
				FROM `$this->table`
				WHERE
					`approved`	= 1 AND
					`abuse`		< 5"
			);
		});
	}
	/**
	 * Get all streams
	 *
	 * @return array|bool
	 */
	function need_approval () {
		return $this->db()->qfas(
			"SELECT `id`
			FROM `$this->table`
			WHERE
				`approved`	= 0"
		);
	}
}
