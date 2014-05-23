<?php
/**
 * @package        Moderation
 * @category       modules
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
 */
namespace cs;

use h;

$module_properties = Config::instance()->module('Moderation');
$User              = User::instance();
if (!$User->admin() && !in_array($module_properties->moderators_group ? : User::ADMIN_GROUP_ID, $User->get_groups())) {
	error_code(403);
	return;
}
$L                 = Language::instance();
$Index             = Index::instance();
$Index->title_auto = false;
$Page              = Page::instance();
$Page->title(isset($Index->route_path[0]) && $Index->route_path[0] == 'streams' ? $L->streams_need_checking : $L->violations_need_checking);
$Page->Header .=
	h::{'a.cs-moderation-need-checking'}(
		isset($Index->route_path[0]) && $Index->route_path[0] == 'streams' ? [
			'in'   => $L->violations_need_checking,
			'href' => 'Moderation/violations'
		] : [
			'in'   => $L->streams_need_checking,
			'href' => 'Moderation/streams'
		]
	);
$Page->content(
	h::{'div.cs-moderation'}()
);
