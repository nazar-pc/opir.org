<?php
/**
 * @package		Elections
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use
	h,
	cs\Index,
	cs\Language,
	cs\Page,
	cs\User;

$L					= Language::instance();
$Page				= Page::instance();
$Page->Description	= 'opir.org - ми контролюємо вибори';
$User				= User::instance();
$Page->og('image', 'https://opir.org/components/modules/Home/includes/img/share.png');
$Page->og('image:secure_url', 'https://opir.org/components/modules/Home/includes/img/share.png');
$Page->link([
	'rel'	=> 'image_src',
	'href'	=> 'https://opir.org/components/modules/Home/includes/img/share.png'
]);
$Page->Header	=
	h::{'div.cs-elections-logo'}(
		h::{'a[href=/] img'}([
			'src'	=> "components/modules/Elections/includes/img/logo.png"
		])/*.
		'<div class="yashare-auto-init" data-yashareL10n="uk" data-yashareQuickServices="facebook" data-yashareLink="https://www.facebook.com/opir.org" data-yashareTheme="counter" data-yashareImage="https://opir.org/components/modules/Home/includes/img/share.png"></div>'.
		'<div class="yashare-auto-init" data-yashareL10n="uk" data-yashareQuickServices="vkontakte,twitter" data-yashareTheme="counter" data-yashareImage="https://opir.org/components/modules/Home/includes/img/share.png"></div>'*/
	).
	h::{'button.cs-elections-info'}().
	h::{'button.cs-elections-help-initiative'}($L->help_initiative);
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
$Index	= Index::instance();
if (isset($Index->route_ids[0])) {
	$event				= Events::instance()->get($Index->route_ids[0]);
	if ($event) {
		$Page->Description	= 'Додано: '.date('H:i d.m.Y', $event['added']);
		if ($event['timeout'] > 0) {
			$Page->Description	.= ' Актуально до: '.date('H:i d.m.Y', $event['timeout']);
		}
		if ($event['img']) {
			$Page->replace('https://opir.org/components/modules/Home/includes/img/share.png', $event['img']);
		}
		if (strpos($event['text'], 'stream:') === false) {
			$Page->Description	.= ' '.$event['text'];
		}
		$Page->og(
			'title',
			array_column(
				Events_categories::instance()->get_all(),
				'name',
				'id'
			)[$event['category']]
		);
	}
	unset($event);
}
$Page->content(
	h::{'aside.cs-elections-add-panel'}().
	h::{'aside.cs-elections-events-stream-panel'}().
	(
		!in_array(AUTOMAIDAN_COORD_GROUP, $User->get_groups() ?: []) ? h::{'aside.cs-elections-settings-panel'}(
			h::{'a.cs-app-store[target=_blank]'}(
				'App Store',
				[
					'href'	=> 'https://itunes.apple.com/in/app/opir/id828565038'
				]
			).
			h::{'div.cs-hot-line'}(
				h::h3('Гаряча лінія:').
				'+38 050 258 17 05<br><small>(медичні питання)</small><br>+38 050 258 17 43<br><small>(координація самооборони)</small><br>+38 093 01 222 11<br><small>(інше)</small>'
			).
			h::{'input.cs-elections-address-search'}([
				'placeholder'	=> 'Пошук адреси на карті'
			]).
			h::h2('Фільтр').
			h::{'div.cs-elections-added-tags[level=0]'}().
			h::{'input.cs-elections-filter-tags'}([
				'placeholder'	=> 'Фільтр по адресі'
			]).
			h::{'div.cs-elections-found-tags[level=0]'}().
			h::{'ul.cs-elections-filter-category li'}(array_map(
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
		) : h::{'aside.cs-elections-settings-coordinator'}(
			h::h2('Фільтр').
			h::{'div.all'}()
		)
	).
	h::{'div.cs-elections-events-stream.uk-icon-chevron-left'}().
	h::{'div.cs-elections-settings.uk-icon-chevron-right'}()
);
