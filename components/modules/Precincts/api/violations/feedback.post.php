<?php
/**
 * @package        Precincts
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
$User  = User::instance();
if (!$User->user()) {
	error_code(403);
	return;
}
if (!isset($Index->route_ids[0]) || !in_array($_POST['value'], [-1, 1])) {
	error_code(400);
	return;
}
if (Violations_feedback::instance()->add($Index->route_ids[0], $User->id, $_POST['value'])) {
	Page::instance()->json('ok');
}
