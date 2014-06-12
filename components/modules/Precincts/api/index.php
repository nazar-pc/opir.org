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
	cs\Config,
	cs\Index;

$Config = Config::instance();
$Index  = Index::instance();
if (
	!isset($Config->route[0]) ||
	(
		!isset($Config->route[1]) && is_numeric($Config->route[0])
	)
) {
	array_unshift($Index->route_path, 'precincts');
} elseif ($Index->route_path[0] == 'violations' && !isset($Index->route_path[1])) {
	array_push($Index->route_path, 'all');
}

