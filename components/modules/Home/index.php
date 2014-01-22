<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs;
use			h;
$Page			= Page::instance();
$User			= User::instance();
$Page->Header	=
	h::{'button.cs-home-chat'}().
	h::{'button.cs-home-add'}('Додати').
	(
		$User->user() ? h::{'button.cs-home-sign-out'}('Вийти') : h::{'button.cs-home-sign-in'}('Увійти')
	).
	h::{'button.cs-home-settings'}();
$Page->content(
	h::{'div.cs-home-left-side'}().
	h::{'div.cs-home-right-side'}()
);
