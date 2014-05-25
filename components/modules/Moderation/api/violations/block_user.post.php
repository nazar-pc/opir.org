<?php
/**
 * @package        Moderation
 * @category       modules
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
 */
namespace cs\modules\Precincts;

use
	cs\Config,
	cs\Index,
	cs\User;

$Index = Index::instance();
if (!isset($Index->route_ids[0])) {
	error_code(400);
	return;
}
$module_properties = Config::instance()->module('Moderation');
$User              = User::instance();
$Violations        = Violations::instance();
$user_id           = (int)$Index->route_ids[0];
if ($user_id == User::ROOT_ID || in_array($module_properties->moderators_group ? : User::ADMIN_GROUP_ID, $User->get_groups($user_id))) {
	return;
}
foreach ($Violations->get_new_of_user($user_id) as $violation) {
	$Violations->decline($violation);
}
unset($violation);
$User->set('status', User::STATUS_INACTIVE, $user_id);
