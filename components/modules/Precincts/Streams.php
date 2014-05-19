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
	cs\CRUD,
	cs\Singleton;

/**
 * @method static \cs\modules\Precincts\Streams instance($check = false)
 */
class Streams {
	use
		CRUD,
		Singleton;

	const STATUS_ADDED    = -1;
	const STATUS_APPROVED = 1;
	const STATUS_DECLINED = 0;

	/**
	 * @var Prefix
	 */
	protected $cache;
	protected $table      = '[prefix]precincts_streams';
	protected $data_model = [
		'id'         => 'int',
		'precinct'   => 'int',
		'user'       => 'int',
		'added'      => 'int',
		'stream_url' => null, //Set in constructor
		'status'     => 'int'
	];

	protected function construct () {
		$this->cache                    = new Prefix('precincts/streams');
		$this->data_model['stream_url'] = function ($stream_url) {
			if (preg_match('/ustream.tv\/(channel|embed)\/([0-9]+)/', $stream_url, $m)) {
				$stream_url	= "https://www.ustream.tv/embed/$m[2]";
			} elseif (preg_match('/(youtube.com\/embed\/|youtube.com\/watch\?v=)([0-9a-z\-]+)/i', $stream_url, $m)) {
				$stream_url = "https://www.youtube.com/embed/$m[2]";
			} else {
				$stream_url = '';
			}
			return $stream_url;
		};
	}
	protected function cdb () {
		return Config::instance()->module('Precincts')->db('precincts');
	}
	/**
	 * Add new stream
	 *
	 * @param $precinct
	 * @param $user
	 * @param $stream_url
	 *
	 * @return bool|int
	 */
	function add ($precinct, $user, $stream_url) {
		$precinct = (int)$precinct;
		$id       = $this->create_simple([
			$precinct,
			$user,
			TIME,
			$stream_url,
			self::STATUS_ADDED
		]);
		if ($id) {
			unset($this->cache->all_for_precincts);
			return $id;
		}
		return false;
	}
	/**
	 * Get stream
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
	 * @param int $precinct
	 *
	 * @return bool|int[]
	 */
	function get_all_for_precinct ($precinct) {
		return $this->cache->get("all_for_precincts/$precinct", function () use ($precinct) {
			return $this->db()->qfas([
				"SELECT `id`
				FROM `$this->table`
				WHERE
					`precinct`	= '%s' AND
					`status`	!= '%s'",
				$precinct,
				self::STATUS_DECLINED
			]);
		});
	}
	/**
	 * Approve added stream
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	function approve ($id) {
		$id             = (int)$id;
		$data           = $this->get($id);
		$data['status'] = self::STATUS_APPROVED;
		if ($this->update_simple($data)) {
			unset(
				$this->cache->$id,
				$this->cache->{"all_for_precincts/$data[precinct]"}
			);
			return true;
		}
		return false;
	}
	/**
	 * Decline added stream
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	function decline ($id) {
		$data           = $this->get($id);
		$data['status'] = self::STATUS_DECLINED;
		if ($this->update_simple($data)) {
			unset(
				$this->cache->$id,
				$this->cache->{"all_for_precincts/$data[precinct]"}
			);
			return true;
		}
		return false;
	}
}
