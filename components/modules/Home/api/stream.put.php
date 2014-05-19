<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use			cs\User;
if (!isset($_POST['stream_code'], $_POST['lat'], $_POST['lng'])) {
	error_code(400);
	return;
}
$User	= User::instance();
if (!in_array(STREAMER_GROUP, $User->get_groups())) {
	error_code(403);
	return;
}
$stream_code	= trim($_POST['stream_code']);
if (preg_match('/ustream.tv\/(channel|embed)\/([0-9]+)/', $stream_code, $m)) {
	$stream_code	= "https://www.ustream.tv/embed/$m[2]";
} elseif (preg_match('/(youtube.com\/embed\/|youtube.com\/watch\?v=)([0-9a-z\-]+)/i', $stream_code, $m)) {
	$stream_code	= "https://www.youtube.com/embed/$m[2]";
} elseif ($stream_code != '') {
	error_code(400);
	return;
}
$User->set_data('stream_url', $stream_code);
$Events	= Events::instance();
$event	= $User->get_data('stream_event');
if ($event) {
	$Events->del($event);
}
if ($stream_code) {
	$event = Events::instance()->add(STREAM_CATEGORY, 120, $_POST['lat'], $_POST['lng'], 0, "stream:$stream_code", 'urgent', 2, 30, '');
	if (!$event) {
		error_code(500);
	}
	$User->set_data('stream_event', $event);
}
