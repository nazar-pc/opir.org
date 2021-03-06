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
$User	= User::instance();
if (!$User->user()) {
	error_code(403);
	return;
}
if (!isset($Index->route_ids[0], $_POST['stream_url'])) {
	error_code(400);
	return;
}
$Precincts = Precincts::instance();
if (!$Precincts->get($Index->route_ids[0])) {
	error_code(404);
	return;
}
$Streams = Streams::instance();
$id      = $Streams->add(
	$Index->route_ids[0],
	$User->id,
	$_POST['stream_url']
);
if (!$id) {
	error_code(500);
}
Page::instance()->json($id);
