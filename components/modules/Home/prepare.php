<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs;
Page::instance()->post_Body	.=
	"<script src=\"//api-maps.yandex.ru/2.1-dev/?load=package.full&lang=uk-UA\"></script>\n".
	"<script type=\"text/javascript\" src=\"//yandex.st/share/share.js\"></script>\n";
