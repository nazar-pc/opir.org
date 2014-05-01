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
	cs\Language,
	cs\Page,
	cs\User,
	cs\modules\Info\Info,
	cs\modules\Help\Help;

$L					= Language::instance();
$Page				= Page::instance();
$Page->Description	= 'opir.org - ми контролюємо вибори';
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
$Page->content(
	h::{'section.cs-elections-info-modal[style=display:none] article'}(
		Info::get()
	).
	h::{'section.cs-elections-help-initiative-modal[style=display:none] article'}(
		Help::get()
	)
);
