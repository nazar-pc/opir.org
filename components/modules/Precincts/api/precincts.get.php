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

$Index     = Index::instance();
$Page      = Page::instance();
$Precincts = Precincts::instance();
if (isset($Index->route_ids[0])) {
	$Page->json($Precincts->get($Index->route_ids[0]));
} else {
	$precincts = $Precincts->get($Precincts->get_all());
	foreach ($precincts as &$precinct) {
		$precinct = [
			'id'         => $precinct['id'],
			'nuber'      => $precinct['number'],
			'lat'        => $precinct['lat'],
			'lng'        => $precinct['lng'],
			'violations' => $precinct['violations']
		];
	}
	unset($precinct);
	$Page->json($precincts);
}
