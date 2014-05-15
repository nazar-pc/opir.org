<?php
/**
 * @package		Elections
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs;
Page::instance()->post_Body	.=
	"<script src=\"//api-maps.yandex.ru/2.1/?load=package.full&lang=".Language::instance()->clang."_UA\"></script>\n".
	"<script type=\"text/javascript\" src=\"//yandex.st/share/share.js\"></script>\n";
