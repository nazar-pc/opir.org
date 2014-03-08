<?php
/**
 * @package		Streams
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Streams;
if (!isset($_POST['stream_code'], $_POST['lat'], $_POST['lng'], $_POST['address_details']) || !$_POST['lat'] || !$_POST['lng']) {
	error_code(400);
	return;
}
$stream_code	= trim($_POST['stream_code']);
if (preg_match('/ustream.tv\/embed\/[0-9]+/', $stream_code, $m)) {
	$stream_code	= "https://www.$m[0]";
} elseif (preg_match('/ustream.tv\/channel\/[0-9]+/', $stream_code, $m)) {
	$stream_code	= "https://www.$m[0]";
} elseif (preg_match('/(youtube.com\/embed\/|youtube.com\/watch\?v=)([0-9a-z\-]+)/i', $stream_code, $m)) {
	$stream_code	= "https://www.youtube.com/embed/$m[2]";
} elseif ($stream_code != '') {
	error_code(400);
	return;
}
$tags				= [];
if ($_POST['address_details']) {
	$tags	= _trim(explode(',', $_POST['address_details']));
	$last	= count($tags) - 1;
	if (preg_match('/^[0-9].*/s', $tags[$last])) {
		unset($tags[$last]);
	}
}
if (!Streams::instance()->add($stream_code, $_POST['lat'], $_POST['lng'], $tags)) {
	error_code(500);
}
