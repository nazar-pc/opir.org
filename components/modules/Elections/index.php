<?php
/**
 * @package        Elections
 * @category       modules
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
 */
namespace cs\modules\Home;

use
	h,
	cs\Index,
	cs\Language,
	cs\Page,
	cs\User,
	cs\modules\Info\Info,
	cs\modules\Help\Help;

Index::instance()->title_auto = false;
$L                            = Language::instance();
$Page                         = Page::instance();
$Page->Description            = 'opir.org - ми контролюємо вибори';
$Page->og('image', 'https://opir.org/components/modules/Elections/includes/img/share.png');
$Page->og('image:secure_url', 'https://opir.org/components/modules/Elections/includes/img/share.png');
$Page->link([
	'rel'  => 'image_src',
	'href' => 'https://opir.org/components/modules/Elections/includes/img/share.png'
]);
$Page->Header =
	h::{'div.cs-elections-logo'}(
		h::{'a[href=/] img'}([
			'src' => "components/modules/Elections/includes/img/logo-$L->clang.png"
		])
	).
	h::{'nav.cs-elections-switch-language'}(
		h::span(
			"$L->clang ".h::icon('caret-down'),
			[
				'class'	=> $L->clang
			]
		).
		h::{'div a[href=/$i[lang]][in=$i[language]]'}([
			'class'		=> '$i[lang]',
			'insert'	=> [
				[
					'lang'		=> 'uk',
					'language'	=> 'Українська'
				],
				[
					'lang'		=> 'ru',
					'language'	=> 'Русский'
				],
				[
					'lang'		=> 'en',
					'language'	=> 'English'
				]
			]
		])
	).
	h::{'button.cs-elections-info'}().
	h::{'button.cs-elections-help-initiative'}($L->help_initiative);
$Page->content(
	h::{'section.cs-elections-info-modal[style=display:none] article'}(
		Info::get()
	).
	h::{'section.cs-elections-help-initiative-modal[style=display:none] article'}(
		Help::get()
	).
	h::{'aside.cs-elections-main-sidebar'}(
		h::{'div.cs-elections-socials'}(
			'<div class="yashare-auto-init" data-yashareL10n="uk" data-yashareQuickServices="facebook" data-yashareLink="https://www.facebook.com/opir.org" data-yashareTheme="counter" data-yashareImage="https://opir.org/components/modules/Elections/includes/img/share.png"></div>'.
			'<div class="yashare-auto-init" data-yashareL10n="uk" data-yashareQuickServices="vkontakte,twitter" data-yashareTheme="counter" data-yashareImage="https://opir.org/components/modules/Elections/includes/img/share.png"></div>'
		).
		h::h2($L->precincts_search).
		h::{'input.cs-elections-precincts-search[type=search]'}([
			'placeholder'	=> $L->number_or_address
		]).
		h::{'section.cs-elections-precincts-search-results[level=0]'}().
		h::h2($L->mobile_apps).
		h::{'div.cs-elections-mobile-apps'}(
			h::a(
				$L->download_in('App Store'),
				[
					'onclick'	=> "$.cs.simple_modal('$L->soon')"
				]
			).
			h::a(
				$L->download_in('Google Play'),
				[
					'onclick'	=> "$.cs.simple_modal('$L->soon')"
				]
			).
			h::a(
				$L->download_in('Market Place'),
				[
					'onclick'	=> "$.cs.simple_modal('$L->soon')"
				]
			)
		).
		h::h2($L->contacts).
		h::{'div.cs-elections-contacts'}(
			h::a(
				h::icon('phone').'+380 93 01 222 11').
			h::{'a[href=mailto:info@opir.org]'}(
				h::icon('envelope').'info@opir.org'
			)
		).
		h::h2($L->map_legend).
		h::{'div.cs-elections-map-legend'}(
			h::div($L->precincts_with_violations).
			h::div($L->precincts_without_violations)
		)
	)
);
