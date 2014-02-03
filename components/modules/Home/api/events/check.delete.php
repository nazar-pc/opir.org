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
$User	= User::instance();
if (!in_array(AUTOMAIDAN_GROUP, $User->get_groups())) {
	error_code(403);
	return;
}
if (!Events::instance()->check_refuse($Index->route_ids[0])) {
	error_code(500);
}
