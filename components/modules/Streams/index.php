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
$Page				= Page::instance();
$Page->Description	= 'opir.org - Тут ви можете з орієнтуватися на самому майдані, дізнатися де проходять суди над евромайданівцями, бути попередженим про появлення тітушок';
$User				= User::instance();
$Page->og('image', 'https://opir.org/components/modules/Home/includes/img/share.png');
$Page->og('image:secure_url', 'https://opir.org/components/modules/Home/includes/img/share.png');
$Page->link([
	'rel'	=> 'image_src',
	'href'	=> 'https://opir.org/components/modules/Home/includes/img/share.png'
]);
$Page->css(
	'#map {left:0;right:0;}',
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
	h::{'a.cs-home-cameras[href=Streams]'}().
	(
		$User->user() ?
			h::{'button.cs-home-sign-out'}()
		:
			h::{'button.cs-home-sign-in'}()
	).
	h::{'button.cs-home-donate'}('Допомогти сайту');
$Page->content(
	h::{'aside.cs-stream-add-panel'}()/*.
	h::{'aside.cs-home-settings-panel'}(
		h::{'input.cs-home-address-search'}([
			'placeholder'	=> 'Пошук адреси на карті'
		]).
		h::h2('Фільтр')
	).
	h::{'div.cs-home-settings.uk-icon-chevron-right'}()*/
);
