<?php
/**
 * @package        Common
 * @category       modules
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
 */
namespace cs;

use h,
	cs\modules\Info\Info,
	cs\modules\Help\Help;

Trigger::instance()->register(
	'System/Index/construct',
	function () {
		if (!API && !ADMIN && Config::instance()->module('Common')->active()) {
			$L                 = Language::instance();
			$Page              = Page::instance();
			$Page->Description = 'opir.org - ми контролюємо вибори';
			$Page->og('image', 'https://opir.org/components/modules/Common/includes/img/share.png');
			$Page->og('image:secure_url', 'https://opir.org/components/modules/Common/includes/img/share.png');
			$Page->link([
				'rel'  => 'image_src',
				'href' => 'https://opir.org/components/modules/Common/includes/img/share.png'
			]);
			$Page->Header =
				h::{'div.cs-elections-logo'}(
					h::{'a[href=/] img'}([
						'src' => "components/modules/Common/includes/img/logo-$L->clang.png"
					])
				).
				h::{'nav.cs-elections-switch-language'}(
					h::span(
						"$L->clang ".h::icon('caret-down'),
						[
							'class' => $L->clang
						]
					).
					h::{'div a[href=/$i[lang]][in=$i[language]]'}([
						'class'  => '$i[lang]',
						'insert' => [
							[
								'lang'     => 'uk',
								'language' => 'Українська'
							],
							[
								'lang'     => 'ru',
								'language' => 'Русский'
							],
							[
								'lang'     => 'en',
								'language' => 'English'
							]
						]
					])
				).
				h::{'button.cs-elections-info'}().
				h::{'button.cs-elections-help-initiative'}($L->help_initiative);
			$Page->content(
				h::{'section.cs-elections-info-modal[style=display:none] article[style=width:800px;]'}(
					Info::get()
				).
				h::{'section.cs-elections-help-initiative-modal[style=display:none] article[style=width:800px;]'}(
					Help::get()
				)
			);
		}
	}
);
