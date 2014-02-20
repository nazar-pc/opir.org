<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use			cs\Index,
			cs\User;
$Index	= Index::instance();
if (!isset($Index->route_ids[0])) {
	error_code(400);;
	return;
}
$Events	= Events::instance();
$event	= $Events->get($Index->route_ids[0]);
if (!$event) {
	error_code(403);
	return;
}
if (!$Events->check_confirm($event['id'])) {
	error_code(500);
}
