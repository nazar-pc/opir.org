<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use			cs\Page,
			cs\User;
$User	= User::instance();
if (!in_array(AUTOMAIDAN_COORD_GROUP, $User->get_groups())) {
	error_code(403);
	return;
}
$Page		= Page::instance();
$Drivers	= Drivers::instance();
$Page->json(
	$Drivers->get_all() ?: []
);
