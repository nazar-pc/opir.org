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
	header('Cache-Control: max-age=60, public');
	header('Expires: access plus 1 minute');
} else {
	$Cache     = new Prefix('precincts');
	$precincts = $Cache->get('all/ids_api', function () use ($Precincts) {
		return $Precincts->get($Precincts->get_all());
	});
	if (isset($_GET['id'])) {
		$id = array_unique(_int(explode(',', $_GET['id'])));
		if ($id) {
			$precincts = array_filter(
				$precincts,
				function ($precinct) use ($id) {
					return in_array($precinct['id'], $id);
				}
			);
		}
	}
	if (isset($_GET['number'])) {
		if (isset($_GET['page'])) {
			$page = max((int)$_GET['page'], 1);
		} else {
			$page = 1;
		}
		$number    = max((int)$_GET['number'], 1);
		$offset    = $number * ($page - 1);
		$precincts = array_slice($precincts, $offset, $number);
		$precinct  = array_values($precincts);
	}
	if (isset($_GET['fields'])) {
		$fields = array_intersect(explode(',', $_GET['fields']), ['id', 'number', 'address', 'lat', 'lng', 'district', 'violations']);
	} else {
		$fields = ['id', 'number', 'lat', 'lng', 'violations'];
	}
	if (empty($fields)) {
		error_code(400);
		return;
	}
	$fields[] = 'id';
	$fields   = array_flip(array_unique($fields)); //For usage in array_intersect_key()
	if (isset($_GET['flat'])) {
		$result = [];
		foreach (array_keys($fields) as $key) {
			$result[$key] = [];
		}
		unset($key);
		foreach ($precincts as &$precinct) {
			$precinct = array_intersect_key($precinct, $fields);
			foreach ($precinct as $k => $v) {
				$result[$k][] = $v;
			}
			unset($k, $v);
		}
		unset($precinct);
		$precincts = $result;
		unset($result);
	} else {
		foreach ($precincts as &$precinct) {
			$precinct = array_intersect_key($precinct, $fields);
		}
	}
	unset($precinct);
	/**
	 * Cache expiration depending on violations field availability
	 */
	if (!isset($fields['violations'])) {
		header('Cache-Control: max-age=86400, public');
		header('Expires: access plus 1 day');
	} else {
		header('Cache-Control: max-age=600, public');
		header('Expires: access plus 10 minutes');
	}
	$Page->json($precincts);
}
