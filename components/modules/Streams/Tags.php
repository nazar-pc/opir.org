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
 * @method static \cs\modules\Streams\Tags instance($check = false)
 */
class Tags {
	use	CRUD,
		Singleton;

	/**
	 * @var Prefix
	 */
	protected $cache;
	protected $table		= '[prefix]streams_tags';
	protected $data_model	= [
		'id'		=> 'int',
		'title'		=> 'text'
	];

	protected function construct () {
		$this->cache	= new Prefix('streams_tags');
	}
	protected function cdb () {
		return '0';
	}
	/**
	 * Add new tag
	 *
	 * @param $title
	 *
	 * @return bool|int
	 */
	function add ($title) {
		return $this->create_simple([
			$title
		]);
	}
	/**
	 * Get tag
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
		return $this->cache->get($id, function () use ($id) {
			return $this->db()->qf([
				"SELECT
					`id`,
					`title`
				FROM `$this->table`
				WHERE `id` = '%s'
				LIMIT 1",
				$id
			]) ?: false;
		});
	}
	/**
	 * Search for tags
	 *
	 * @param $tag
	 *
	 * @return array|bool
	 */
	function search ($tag) {
		return $this->db()->qfas([
			"SELECT `id`
			FROM `$this->table`
			WHERE
				`title`	LIKE '%s'",
			"$tag%"
		]);
	}
}
