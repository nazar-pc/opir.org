<?php
/**
 * @package		Streams
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Streams;
use			h,
			cs\Index,
			cs\Page,
			cs\User;
$Page				= Page::instance();
$Index				= Index::instance();
$Index->title_auto	= false;
$Page->Description	= 'opir.org - Тут ви можете з орієнтуватися на самому майдані, дізнатися де проходять суди над евромайданівцями, бути попередженим про появлення тітушок';
$User				= User::instance();
$Page->title('Камери');
$Page->og('image', 'https://opir.org/components/modules/Home/includes/img/share.png');
$Page->og('image:secure_url', 'https://opir.org/components/modules/Home/includes/img/share.png');
if (isset($Index->route_ids[0])) {
	$stream				= Streams::instance()->get($Index->route_ids[0]);
	if (preg_match('/youtube.com\/embed\/(.*)/', $stream['stream_url'], $image)) {
		$Page->replace(
			'https://opir.org/components/modules/Home/includes/img/share.png',
			"https://i1.ytimg.com/vi/$image[1]/hqdefault.jpg"
		);
	}
	unset($stream, $image);
}
$Page->link([
	'rel'	=> 'image_src',
	'href'	=> 'https://opir.org/components/modules/Home/includes/img/share.png'
]);
$Page->css(
	'body>header{height:62px}#map{top:62px;left:0;}',
	'code'
);
$Page->Header	=
	h::{'div.cs-home-logo'}(
		h::{'a[href=/] img'}([
			'src'	=> "components/modules/Home/includes/img/logo.png"
		]).
		'<div class="yashare-auto-init" data-yashareL10n="uk" data-yashareQuickServices="facebook" data-yashareLink="https://www.facebook.com/opir.org" data-yashareTheme="counter" data-yashareImage="https://opir.org/components/modules/Home/includes/img/share.png"></div>'.
		'<div class="yashare-auto-init" data-yashareL10n="uk" data-yashareQuickServices="vkontakte,twitter" data-yashareTheme="counter" data-yashareImage="https://opir.org/components/modules/Home/includes/img/share.png"></div>'
	).
	h::{'button.cs-stream-add'}('Додати камеру').
	h::{'a.cs-home-home[href=/]'}().
	(
		$User->user() ?
			h::{'button.cs-home-sign-out'}()
		:
			h::{'button.cs-home-sign-in'}()
	).
	h::{'button.cs-home-donate'}('Допомогти ініціативі');
$Page->content(
	h::{'aside.cs-stream-add-panel'}().
	h::{'aside.cs-stream-filter-panel'}(
		h::{"div.uk-button-dropdown[data-uk-dropdown={mode:'click'}]"}(
			h::{'button.uk-button'}(
				h::icon('caret-down').' '.h::span('Показати карту')
			).
			h::{'div.uk-dropdown ul.cs-stream-show.uk-nav.uk-nav-dropdown'}(
				h::{'li[data-mode=map] a'}('Показати карту').
				h::{'li[data-mode=list] a'}('Показати список')
			)
		).
		h::{'div.cs-stream-added-tags[level=0]'}().
		h::{'input.cs-stream-filter'}([
			'placeholder'	=> 'Фільтр по адресі'
		]).
		h::{'div.cs-stream-found-tags[level=0]'}()
	).
	h::{'div.cs-stream-filter-hide.uk-icon-chevron-right'}().
	h::{'div.cs-stream-list'}()
);
