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
	!isset($Index->route_ids[0]) ||
	(
		(
			!isset($_POST['text']) || empty($_POST['text'])
		) &&
		(
			!isset($_POST['images']) || empty($_POST['images'])
		) &&
		(
			!isset($_POST['video']) || empty($_POST['video'])
		)
	)
) {
	error_code(400);
	return;
}
$Precincts = Precincts::instance();
if (!$Precincts->get($Index->route_ids[0])) {
	error_code(404);
	return;
}
$Violations = Violations::instance();
$id         = $Violations->add(
	$Index->route_ids[0],
	$User->id,
	isset($_POST['text']) ? $_POST['text'] : '',
	isset($_POST['images']) ? (array)$_POST['images'] : [],
	isset($_POST['video']) ? $_POST['video'] : '',
	isset($_POST['location']) ? $_POST['location'] : [],
	isset($_POST['device_model']) ? $_POST['device_model'] : ''
);
if (!$id) {
	error_code(500);
}
// Hack: Android library can't parse single number and requires object, so we return object using undocumented `android` parameter
Page::instance()->json(isset($_GET['android']) ? ['id' => $id] : $id);
