<?php
/**
 * @package		Package
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs;
$rc								= Config::instance()->route;
if (!isset($rc[0])) {
	return;
}
switch ($rc[0]) {
	case 'profile':
	case path(Language::instance()->profile):
		error_code(404);
}
