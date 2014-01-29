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
if (!isset($_POST['lat'], $_POST['lng'])) {
	error_code(400);
	return;
}
$User	= User::instance();
if (!in_array(STREAMER_GROUP, $User->get_groups())) {
	error_code(403);
	return;
}
$Events	= Events::instance();
$event	= $User->get_data('stream_event');
if (!$event) {
	error_code(404);
	return;
}
$event	= $Events->get($event);
if (!$event) {
	error_code(404);
	return;
}
if (!Events::instance()->set($event['id'], 120, $_POST['lat'], $_POST['lng'], 0, $event['text'], 'urgent', 2, 60, '')) {
	error_code(500);
}
