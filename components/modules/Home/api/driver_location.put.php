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
if (!isset($_POST['lat'], $_POST['lng'])) {
	error_code(400);
	return;
}
$User	= User::instance();
if (!in_array(AUTOMAIDAN_GROUP, $User->get_groups())) {
	error_code(403);
	return;
}
$driver	= Drivers::instance()->get($User->id) ?: ['busy' => 0];
if (!Drivers::instance()->set($_POST['lat'], $_POST['lng'], $driver['busy'])) {
	error_code(500);
}
