<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use			cs\Index,
			cs\Page;
$Index	= Index::instance();
$Page	= Page::instance();
$Events	= Events::instance();
if (isset($Index->route_ids[0])) {
	$event	= $Events->get($Index->route_ids[0]);
	if (!$event) {
		error_code(404);
		return;
	}
	if (!isset($event['user'])) {
		error_code(403);
		return;
	}
	$Page->json($event);
} else {
	$Page->json(
		$Events->get_all() ?: []
	);
}
