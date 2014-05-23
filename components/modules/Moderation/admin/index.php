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
$Index             = Index::instance();
if (isset($_POST['moderators_group'])) {
	$module_properties->moderators_group = (int)$_POST['moderators_group'];
	$Index->save();
}
$groups              = Group::instance()->get_all();
$Index->apply_button = false;

$Index->content(
	h::{'p.uk-text-center'}(
		Language::instance()->moderators_group,
		h::{'select[name=moderators_group]'}([
			'in'       => array_column($groups, 'title'),
			'value'    => array_column($groups, 'id'),
			'selected' => $module_properties->moderators_group ? : User::ADMIN_GROUP_ID
		])
	)
);
