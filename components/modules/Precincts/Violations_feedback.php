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
	cs\Config,
	cs\Storage,
	cs\CRUD,
	cs\Singleton;

/**
 * @method static \cs\modules\Precincts\Violations_feedback instance($check = false)
 */
class Violations_feedback {
	use
		CRUD,
		Singleton;

	protected $table      = '[prefix]precincts_violations_feedback';
	protected $data_model = [
		'id'   => 'int',
		'user' => 'int',
		'int'  => 'set:-1,1'
	];

	protected function cdb () {
		return Config::instance()->module('Precincts')->db('precincts');
	}
	/**
	 * Add new feedback
	 *
	 * @param int $id Violation id
	 * @param int $user
	 * @param int $value
	 *
	 * @return bool|int
	 */
	function add ($id, $user, $value) {
		if ($this->create_simple([
			$id,
			$user,
			$value
		])
		) {
			$feedback_sum = $this->db()->qfs(
				"SELECT SUM(`value`)
				FROM `$this->table`
				WHERE `id` = $id"
			);
			if ($feedback_sum < 0 && $feedback_sum % 5 == 0) {
				Violations::instance()->to_moderation($id);
			}
		}
		return false;
	}
}
