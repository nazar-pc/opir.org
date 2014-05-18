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
	cs\Page;

$Page      = Page::instance();
$Precincts = Precincts::instance();
if (!isset($_GET['text']) || mb_strlen($_GET['text']) < 3) {
	error_code(400);
	return;
}
$Page->json(
	$Precincts->get(
		$Precincts->search($_GET['text'], isset($_GET['coordinates']) ? $_GET['coordinates'] : false)
	)
);
