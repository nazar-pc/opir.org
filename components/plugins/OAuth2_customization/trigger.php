<?php
/**
 * @package        OAuth2 customization
 * @category       plugins
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
 */

namespace cs;

use h;

Trigger::instance()
	->register(
		'OAuth2/custom_sign_in_page',
		function () {
			if (!in_array('OAuth2_customization', Config::instance()->components['plugins'])) {
				return true;
			}
			define('MOBILE_AUTH', true);
			//TODO: remove next line and add normal auth page for mobile apps
			$L           = Language::instance();
			$Page        = Page::instance();
			$Page->Title = [$Page->Title[0], $L->sign_in];
			$Page->content(
				h::{'section.cs-oauth2-customization'}(
					h::{'h2.uk-text-center.uk-margin-top'}($L->sign_in).
					h::a(
						h::icon('facebook').
						$L->sign_in_with('Facebook'),
						[
							'href' => 'HybridAuth/Facebook'
						]
					).
					h::a(
						h::icon('vk').
						$L->sign_in_with('VK'),
						[
							'href' => 'HybridAuth/Vkontakte'
						]
					).
					h::a(
						h::icon('twitter').
						$L->sign_in_with('Twitter'),
						[
							'href' => 'HybridAuth/Twitter'
						]
					)
				)
			);
			return false;
		}
	)
	->register(
		'System/Config/routing_replace',
		function () {
			if (!in_array('OAuth2_customization', Config::instance()->components['plugins'])) {
				return;
			}
			spl_autoload_register(
				function ($class) {
					if (ltrim($class, '\\') == 'cs\modules\OAuth2\OAuth2') {
						include __DIR__.'/OAuth2.php';
					}
				},
				true,
				true
			);
		}
	);
