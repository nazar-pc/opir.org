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
$Page->og('image', 'https://opir.org/components/modules/Home/includes/img/share.png');
$Page->og('image:secure_url', 'https://opir.org/components/modules/Home/includes/img/share.png');
$Page->link([
	'rel'	=> 'image_src',
	'href'	=> 'https://opir.org/components/modules/Home/includes/img/share.png'
]);
$Page->Header	=
	h::{'div.cs-home-logo'}(
		h::{'a[href=/] img'}([
			'src'	=> "components/modules/Home/includes/img/logo.png"
		]).
		h::{'div.hot-line'}(
			'+38 050 258 17 05 (мед. питання)<br>+38 050 258 17 43 (самооборона)<br>+38 093 01 222 11 (інше)'
		).
		'<div class="yashare-auto-init" data-yashareL10n="uk" data-yashareQuickServices="facebook" data-yashareLink="https://www.facebook.com/opir.org" data-yashareTheme="counter" data-yashareImage="https://opir.org/components/modules/Home/includes/img/share.png"></div>'.
		'<div class="yashare-auto-init" data-yashareL10n="uk" data-yashareQuickServices="vkontakte,twitter" data-yashareTheme="counter" data-yashareImage="https://opir.org/components/modules/Home/includes/img/share.png"></div>'
	).
	(
		$User->user() ?
			h::{'button.cs-home-add'}('Додати').
			h::{'button.cs-home-sign-out'}()
		:
			h::{'button.cs-home-sign-in'}()
	).
	h::{'button.cs-home-donate'}('Допомогти сайту');
$categories	= Events_categories::instance()->get_all();
$groups		= Events_categories_groups::instance()->get_all();
$groups		= array_combine(array_column($groups, 'id'), $groups);
$groups		= array_map(
	function ($g) {
		$g['categories']	= [];
		return $g;
	},
	$groups
);
$categories_			= [];
foreach ($categories as $c) {
	$categories_[$c['id']]	= $c;
	$groups[$c['group']]['categories'][]	= $c['id'];
}
$categories	= $categories_;
unset($categories_, $c);
$Page->js(
	'cs.home = '._json_encode([
		'categories'		=> $categories,
		'reporter'			=> in_array(STREAMER_GROUP, $User->get_groups() ?: []) ? _json_encode($User->get_data('stream_url') ?: 1) : 0,
		'automaidan'		=> (int)in_array(AUTOMAIDAN_GROUP, $User->get_groups() ?: []),
		'automaidan_coord'	=> (int)in_array(AUTOMAIDAN_COORD_GROUP, $User->get_groups() ?: [])
	]).';',
	'code'
);
$Page->content(
	h::{'aside.cs-home-add-panel'}().
	h::{'aside.cs-home-events-stream-panel'}().
	(
		!in_array(AUTOMAIDAN_COORD_GROUP, $User->get_groups() ?: []) ? h::{'aside.cs-home-settings-panel'}(
			h::{'input.cs-home-address-search'}([
				'placeholder'	=> 'Пошук адреси на карті'
			]).
			h::h2('Фільтр').
			h::{'ul.cs-home-filter-category li'}(array_map(
				function ($g) use ($categories) {
					$return = [[
						h::h2($g['name']),
						[
							'data-group'	=> $g['id']
						]
					]];
					foreach ($g['categories'] as $c) {
						$c			= $categories[$c];
						$return[]	= [
							h::img([
								'src'	=> "components/modules/Home/includes/img/$c[id].png"
							]).
							h::span($c['name']),
							[
								'data-id'		=> $c['id'],
								'data-group'	=> $g['id']
							]
						];
					}
					return $return;
				},
				array_values($groups)
			))
		) : h::{'aside.cs-home-settings-coordinator'}(
			h::h2('Фільтр').
			h::{'div.all'}()
		)
	).
	h::{'div.cs-home-events-stream.uk-icon-chevron-left'}().
	h::{'div.cs-home-settings.uk-icon-chevron-right'}()
);
