<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use			cs\Index;
$Events	= Events::instance();
$event	= $Events->get(Index::instance()->route_ids[0]);
if (!$event) {
	error_code(404);
	return;
}
if (!isset($event['user'])) {
	error_code(403);
	return;
}
if (!$Events->del($event['id'])) {
	error_code(500);
}
