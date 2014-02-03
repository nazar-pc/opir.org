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
			cs\Page,
			cs\User;
$Index	= Index::instance();
if (!isset($Index->route_ids[0], $_POST['driver'])) {
	error_code(400);;
	return;
}
$User	= User::instance();
if (!in_array(AUTOMAIDAN_COORD_GROUP, $User->get_groups())) {
	error_code(403);
	return;
}
$Events	= Events::instance();
$event	= $Events->get($Index->route_ids[0]);
if (!$event) {
	error_code(404);
	return;
}
if ($event['user']	 == $_POST['driver']) {
	error_code(400);
	Page::instance()->error('Цей той самий водій, який додав подію на карту, оберіть іншого для підтвердження.');
}
if (!Events::instance()->check_assign($Index->route_ids[0], $_POST['driver'])) {
	error_code(500);
}
