<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs;
Trigger::instance()->register(
	'System/Config/routing_replace',
	function ($data) {
		$rc	= explode('/', $data['rc']);
		if (isset($rc[0]) && !isset($rc[1]) && is_numeric($rc[0])) {
			$data['rc']	= "Home/$rc[0]";
		}
	}
);
