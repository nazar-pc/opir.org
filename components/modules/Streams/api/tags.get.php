<?php
/**
 * @package		Streams
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Streams;
use			cs\Page;
$Page	= Page::instance();
if (!isset($_GET['title']) || !$_GET['title']) {
	error_code(400);
	$Page->error('non-empty "title" parameter required');
	return;
}
$Tags	= Tags::instance();
Page::instance()->json(
	$Tags->get($Tags->search($_GET['title']) ?: [])
);
