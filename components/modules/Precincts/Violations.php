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
	cs\Storage,
	cs\Trigger,
	cs\CRUD,
	cs\Singleton;

/**
 * @method static \cs\modules\Precincts\Violations instance($check = false)
 */
class Violations {
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
	protected $table      = '[prefix]precincts_violations';
	protected $data_model = [
		'id'       => 'int',
		'precinct' => 'int',
		'user'     => 'int',
		'date'     => 'int',
		'text'     => 'text',
		'images'   => null, //Set in constructor, array of strings
		'video'    => 'string', //TODO: check for allowed services, probably youtube only
		'status'   => 'int'
	];

	protected function construct () {
		$this->cache                = new Prefix('precincts/violations');
		$this->data_model['images'] = function ($images) {
			return array_filter($images, function ($image) {
				return preg_match("#^(http[s]?://)#", $image);
			});
		};
		$this->data_model['video']  = function ($video) {
			return preg_match("#^(http[s]?://)#", $video) ? $video : '';
		};
	}
	protected function cdb () {
		return Config::instance()->module('Precincts')->db('precincts');
	}
	/**
	 * Add new violation
	 *
	 * @param $precinct
	 * @param $user
	 * @param $text
	 * @param $images
	 * @param $video
	 *
	 * @return bool|int
	 */
	function add ($precinct, $user, $text, $images, $video) { //TODO: add tags to files
		$precinct = (int)$precinct;
		$id       = $this->create_simple([
			$precinct,
			$user,
			TIME,
			$text,
			$images,
			$video,
			self::STATUS_ADDED
		]);
		if ($id) {
			$images	= $this->data_model['images']($images);
			foreach ($images as $image) {
				Trigger::instance()->run(
					'System/upload_files/add_tag',
					[
						'tag' => "Precincts/violations/$id",
						'url' => $image
					]
				);
			}
			unset($images, $image);
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
	 * Approve added violation
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
	 * Decline added violation
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
