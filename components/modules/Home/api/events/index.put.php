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
$Index	= Index::instance();
$Events	= Events::instance();
if (isset($Index->route_ids[0], $_POST['timeout'], $_POST['lat'], $_POST['lng'], $_POST['visible'], $_POST['text'], $_POST['time'], $_POST['time_interval'], $_POST['img'])) {
	$event	= $Events->get($Index->route_ids[0]);
	if (!$event) {
		error_code(404);
		return;
	}
	if (!isset($event['user'])) {
		error_code(403);
		return;
	}
	$tags				= [];
	if ($_POST['address_details']) {
		$tags	= _trim(explode(',', $_POST['address_details']));
		$last	= count($tags) - 1;
		if (preg_match('/^[0-9].*/s', $tags[$last])) {
			unset($tags[$last]);
		}
	}
	if (!$Events->set($event['id'], $_POST['timeout'], $_POST['lat'], $_POST['lng'], $_POST['visible'], $_POST['text'], $_POST['time'], $_POST['time_interval'], $_POST['img'], $tags)) {
		error_code(500);
	}
} else {
	error_code(400);
}
