<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs;
use			h;
$Group	= Group::instance();
$Index	= Index::instance();
$User	= User::instance();
if (isset($_POST['group'], $_POST['count'])) {
	$group	= $Group->get($_POST['group']);
	$count	= (int)$_POST['count'];
	while ($count--) {
		for (
			$i = 100;
			$User->get_id(hash('sha224', $login	= $group['title'].'_'.$i));
			++$i
		) {}
		$new_user = $User->registration("$login@".DOMAIN, false, false);
		$User->set_groups([
			2,
			$group['id']
		], $new_user['id']);
		$Index->content(
			 h::p("Логін: $login Пароль: $new_user[password]")
		);
	}
}
$groups	= array_filter(
	$Group->get_all(),
	function ($group) {
		return $group['id'] > 3;
	}
);
Index::instance()->buttons	= false;
$Index->content(
	h::{'p.cs-center'}(
		h::span('Група').
		h::{'select[name=group]'}(
			[
				'in'	=> array_values(array_map(
					function ($group) {
						return $group['description'];
					},
					$groups
				)),
				'value'	=> array_values(array_map(
					function ($group) {
						return $group['id'];
					},
					$groups
				))
			]
		)
	).
	h::{'p.cs-center'}(
		h::span('Кількість').
		h::{'input[name=count][type=number][min=1][value=1]'}()
	).
	h::{'p.cs-center button[type=submit]'}('Додати користувачів')
);
