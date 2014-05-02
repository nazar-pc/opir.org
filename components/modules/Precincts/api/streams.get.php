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
	cs\Page;

$Index   = Index::instance();
$Page    = Page::instance();
$Streams = Streams::instance();
if (!isset($Index->route_ids[0])) {
	error_code(400);
	return;
}
$Page->json(
	$Streams->get(
		$Streams->get_all_for_precinct($Index->route_ids[0])
	)
);
