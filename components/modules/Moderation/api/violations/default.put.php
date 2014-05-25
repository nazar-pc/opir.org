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
	cs\Index,
	cs\Page,
	cs\User;

$Index = Index::instance();
if (!isset($_POST['status'], $Index->route_ids[0])) {
	error_code(400);
	return;
}
$Violations = Violations::instance();
$action     = $_POST['status'] ? 'approve' : 'decline';
$violation  = $Violations->get($Index->route_ids[0]);
if (!$Violations->$action($violation['id'])) {
	error_code(500);
	return;
}
Page::instance()->json([
	'user'     => (int)$violation['user'],
	'rating'   => (int)$Violations->user_rating($violation['user']),
	'username' => User::instance()->username($violation['user'])
]);
