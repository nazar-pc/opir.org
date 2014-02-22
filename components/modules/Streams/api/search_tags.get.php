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
if (!isset($_GET['text']) || !$_GET['text']) {
	error_code(400);
	$Page->error('non-empty "text" parameter required');
	return;
}
Page::instance()->json(
	Tags::instance()->search($_GET['text']) ?: []
);
