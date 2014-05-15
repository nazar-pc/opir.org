<?php
/**
 * @package        Districts
 * @category       modules
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
 */
namespace cs\modules\Precincts;

use cs\Page;

$Page      = Page::instance();
$Precincts = Precincts::instance();
$Page->json(
	$Precincts->group_by_district()
);
header('Cache-Control: max-age=600, public');
header('Expires: access plus 10 minutes');
