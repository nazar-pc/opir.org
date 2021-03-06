<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use			cs\User;
$User	= User::instance();
$Events	= Events::instance();
if (!$User->user()) {
	error_code(403);
	return;
}
if (!isset($_POST['category'], $_POST['timeout'], $_POST['lat'], $_POST['lng'], $_POST['visible'], $_POST['text'], $_POST['time'], $_POST['time_interval'], $_POST['img'])) {
	error_code(400);
	return;
}
if (in_array(AUTOMAIDAN_GROUP, $User->get_groups()) && !in_array($_POST['category'], [1, 3, 6, 7, 8, 17, 21, 22])) {	// Magic numbers - id of categories, where confirmation is needed
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
if (!$Events->add($_POST['category'], $_POST['timeout'], $_POST['lat'], $_POST['lng'], $_POST['visible'], $_POST['text'], $_POST['time'], $_POST['time_interval'], $_POST['img'], $tags)) {
	error_code(500);
}
