<?php
/**
 * @package        Moderation
 * @category       modules
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
 */
namespace cs\modules\Precincts;

use
	cs\Page;

$Streams = Streams::instance();
Page::instance()->json(
	$Streams->get(
		$Streams->get_declined()
	)
);
