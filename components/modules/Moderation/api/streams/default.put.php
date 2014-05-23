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
	cs\Index;

$Index = Index::instance();
if (!isset($_POST['status'], $Index->route_ids[0])) {
	error_code(400);
	return;
}
$Streams = Streams::instance();
$action  = $_POST['status'] ? 'approve' : 'decline';
if (!$Streams->$action($Index->route_ids[0])) {
	error_code(500);
}
