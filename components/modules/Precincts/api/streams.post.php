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
if (!isset($Index->route_ids[0], $_POST['stream_url'])) {
	error_code(400);
	return;
}
$Streams = Streams::instance();
$id      = $Streams->add(
	$Index->route_ids[0],
	User::instance()->id,
	$_POST['stream_url']
);
if (!$id) {
	error_code(500);
}
Page::instance()->json($id);
