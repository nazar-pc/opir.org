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
$districts = Precincts::instance()->group_by_district();
if (isset($_GET['fields'])) {
	$fields   = array_intersect(explode(',', $_GET['fields']), ['district', 'count', 'lat', 'lng', 'violations']);
	$fields[] = 'district';
	$fields   = array_flip(array_unique($fields)); //For usage in array_intersect_key()
	foreach ($districts as &$district) {
		$district = array_intersect_key($district, $fields);
	}
	if (!isset($fields['violations'])) {
		header('Cache-Control: max-age=86400, public');
		header('Expires: access plus 1 day');
	} else {
		header('Cache-Control: max-age=600, public');
		header('Expires: access plus 10 minutes');
	}
} else {
	header('Cache-Control: max-age=600, public');
	header('Expires: access plus 10 minutes');
}
$Page->json($districts);
