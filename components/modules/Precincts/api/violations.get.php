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

$Index      = Index::instance();
$Page       = Page::instance();
$Violations = Violations::instance();
if (!isset($Index->route_ids[0])) {
	error_code(400);
	return;
}
$Page->json(
	$Violations->get(
		$Violations->get_all_for_precinct($Index->route_ids[0])
	)
);
header('Cache-Control: max-age=60, public');
header('Expires: access plus 1 minute');
