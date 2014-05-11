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
	cs\Cache\Prefix,
	cs\Index,
	cs\Page;

$Index     = Index::instance();
$Page      = Page::instance();
$Precincts = Precincts::instance();
if (isset($Index->route_ids[0])) {
	$Page->json($Precincts->get($Index->route_ids[0]));
} else {
	$Cache     = new Prefix('precincts');
	$precincts = $Cache->get('all/ids_api', function () use ($Precincts) {
		$precincts = $Precincts->get($Precincts->get_all());
		foreach ($precincts as &$precinct) {
			$precinct = [
				'id'         => $precinct['id'],
				'number'     => $precinct['number'],
				'lat'        => $precinct['lat'],
				'lng'        => $precinct['lng'],
				'violations' => $precinct['violations']
			];
		}
		unset($precinct);
		return $precincts;
	});
	if (isset($_GET['number'])) {
		if (isset($_GET['page'])) {
			$page = max((int)$_GET['page'], 1);
		} else {
			$page = 1;
		}
		$number    = max((int)$_GET['number'], 1);
		$offset    = $number * ($page - 1);
		$precincts = array_slice($precincts, $offset, $number);
	}
	$Page->json($precincts);
}
