<?php
/**
 * @package        OAuth2 customization
 * @category       plugins
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
 */

namespace cs;

Trigger::instance()
	->register(
		'OAuth2/custom_login_page',
		function () {
			//define('MOBILE_AUTH', true);
			//TODO: remove next line and add normal auth page for mobile apps
			interface_off();
			Page::instance()->content(
				'<!doctype html>
				<title>Mobile sign in</title>
				<a href="/HybridAuth/Facebook">Sign in with Facebook</a>'
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
						include	__DIR__.'/OAuth2.php';
					}
				},
				true,
				true
			);
		}
	);
