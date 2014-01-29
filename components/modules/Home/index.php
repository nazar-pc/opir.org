<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use			h,
			cs\Page,
			cs\User;
$Page			= Page::instance();
$User			= User::instance();
$Page->Header	=
	h::{'div.cs-home-logo a[href=/] img'}([
		'src'	=> "components/modules/Home/includes/img/logo.png"
	]).
	(
		$User->user() ?
			h::{'button.cs-home-chat'}([
				'data-title'	=> 'Ще не реалізовано'
			]).
			h::{'button.cs-home-add'}('Додати').
			h::{'button.cs-home-sign-out'}('Вийти')
		:
			h::{'button.cs-home-sign-in'}('Увійти')
	).
	h::{'button.cs-home-settings'}();
$categories		= Events_categories::instance()->get_all();
$Page->js(
	'cs.home = {categories:'._json_encode(array_column($categories, 'name', 'id')).',reporter:'.(in_array(STREAMER_GROUP, $User->get_groups()) ? _json_encode($User->get_data('stream_url') ?: 1) : 0).'};',
	'code'
);
$Page->content(
	h::{'aside.cs-home-add-panel'}().
	h::{'aside.cs-home-settings-panel'}(
		h::h2('Фільтр').
		h::{'ul.cs-home-filter-urgency li'}(array_map(
			function ($category) {
				return [
					h::img([
						'src'	=> "components/modules/Home/includes/img/$category[id].png"
					]).
					h::span($category['name']),
					[
						'data-id'	=> $category['id']
					]
				];
			},
			[
				[
					'id'	=> 'unknown',
					'name'	=> 'Терміновість не вказано'
				],
				[
					'id'	=> 'can-wait',
					'name'	=> 'Може почекати'
				],
				[
					'id'	=> 'urgent',
					'name'	=> 'Терміново'
				]
			]
		)).
		h::{'ul.cs-home-filter-category li'}(array_map(
			function ($category) {
				return [
					h::img([
						'src'	=> "components/modules/Home/includes/img/$category[id].png"
					]).
					h::span($category['name']),
					[
						'data-id'	=> $category['id']
					]
				];
			},
			$categories
		))
	)
);
