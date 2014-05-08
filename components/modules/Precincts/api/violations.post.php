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
if (
	!isset($Index->route_ids[0], $_POST['text'], $_POST['images'], $_POST['video']) ||
	(
		empty($_POST['text']) &&
		empty($_POST['images']) &&
		empty($_POST['video'])
	)
) {
	error_code(400);
	return;
}
$Violations = Violations::instance();
$id         = $Violations->add(
	$Index->route_ids[0],
	$User->id,
	$_POST['text'],
	(array)$_POST['images'],
	$_POST['video']
);
if (!$id) {
	error_code(500);
}
Page::instance()->json($id);
