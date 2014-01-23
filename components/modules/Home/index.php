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
			h::{'button.cs-home-chat'}().
			h::{'button.cs-home-add'}('Додати').
			h::{'button.cs-home-sign-out'}('Вийти')
		:
			h::{'button.cs-home-sign-in'}('Увійти')
	).
	h::{'button.cs-home-settings'}();
$categories		= Events_categories::instance()->get_all();
$Page->js(
	'cs.home = {categories:'._json_encode($categories).'};',
	'code'
);
array_unshift(
	$categories,
	[
		'id'	=> 0,
		'name'	=> 'Всі події'
	]
);
$Page->content(
	h::{'aside.cs-home-add-panel'}().
	h::{'aside.cs-home-settings-panel'}(
		h::h2('Фільтр подій').
		h::{"div.uk-button-dropdown[data-uk-dropdown={mode:'click'}]"}(
			h::{'button.uk-button'}(
				h::icon('caret-down').
				h::span($categories[0]['name'])
			).
			h::{'div.uk-dropdown ul.cs-home-filter-category.uk-nav.uk-nav-dropdown li'}(array_map(
				function ($category) {
					return [
						h::a($category['name']),
						[
							'data-id'	=> $category['id']
						]
					];
				},
				$categories
			))
		).
		h::{"div.uk-button-dropdown[data-uk-dropdown={mode:'click'}]"}(
			h::{'button.uk-button'}(
				h::icon('caret-down').
				h::span('Будь-яка терміновість')
			).
			h::{'div.uk-dropdown ul.cs-home-filter-urgency.uk-nav.uk-nav-dropdown li'}(array_map(
				function ($category) {
					return [
						h::a($category['name']),
						[
							'data-id'	=> $category['id']
						]
					];
				},
				[
					[
						'id'	=> 'any',
						'name'	=> 'Будь-яка терміновість'
					],
					[
						'id'	=> 'unknown',
						'name'	=> 'Невідомо'
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
			))
		).
		h::hr().
		h::h2('Позначення на карті').
		h::ul(
			h::li(array_map(
				function ($category) {
					return
						h::img([
							'src'	=> "components/modules/Home/includes/img/$category[id].png"
						]).
						h::span($category['name']);
				},
				array_merge(
					array_slice($categories, 1),
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
				)
			))
		)
	)
);
