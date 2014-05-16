<?php
/**
 * @package        Violations
 * @category       modules
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
 */
namespace cs\modules\Precincts;

use cs\Page;

$Page       = Page::instance();
$Violations = Violations::instance();
$number     = isset($_POST['number']) ? min((int)$_POST['number'], 100) : 10;
$number     = max($number, 0);
$last_id    = isset($_POST['last_id']) ? max((int)$_POST['last_id'], 0) : 0;
$Page->json(
	$Violations->last_violations($number, $last_id) ?: []
);
header('Cache-Control: max-age=30, public');
header('Expires: access plus 30 seconds');