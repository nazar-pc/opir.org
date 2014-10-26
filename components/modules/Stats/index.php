<?php
/**
 * @package        Moderation
 * @category       modules
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
 */
namespace cs;
use
	h,
	cs\modules\Precincts\Violations,
	cs\modules\Precincts\Streams;

$User = User::instance();
if (!$User->admin() && !in_array(Config::instance()->module('Moderation')->moderators_group ?: User::ADMIN_GROUP_ID, $User->get_groups())) {
	error_code(403);
	return;
}
$Page = Page::instance();
$db   = DB::instance();
$Page->content(
	h::{'p.cs-left'}(
		"Зареєстрованих користувачів (всі за весь час): ".$db->qfs(
			"SELECT COUNT(`id`) - 1
 			FROM `[prefix]users`"
		),
		"Доданих повідомлень (не модерованих + підтверджених, відхилені не враховуються): ".$db->qfs(
			"SELECT COUNT(`id`)
			FROM `[prefix]precincts_violations`
			WHERE
				`status` != '%s'",
			Violations::STATUS_DECLINED
		),
		"- з фото: ".$db->qfs(
			"SELECT COUNT(`id`)
			FROM `[prefix]precincts_violations`
			WHERE
				`status` != '%s' AND
				`images` != '[]'",
			Violations::STATUS_DECLINED
		),
		"- з відео: ".$db->qfs(
			"SELECT COUNT(`id`)
			FROM `[prefix]precincts_violations`
			WHERE
				`status` != '%s' AND
				`video` != ''",
			Violations::STATUS_DECLINED
		),
		"Стрімів: ".count(Streams::instance()->get_approved())
	)
);
