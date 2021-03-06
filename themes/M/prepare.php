<?php
/**
 * @package		ClevereStyle CMS
 * @subpackage	CleverStyle theme
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013-2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs;
$Page	= Page::instance();
if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/msie|trident/i',$_SERVER['HTTP_USER_AGENT'])) {
	$Page->Head	.= '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">';
}
if (defined('MOBILE_AUTH') && MOBILE_AUTH) {
	$Page->Head .= '<meta name="viewport" content="width=400">';
	if (preg_match('/iPad/', $_SERVER['HTTP_USER_AGENT'])) {
		$Page->Head	.= '<style>body {background: #232930 !important} .cs-oauth2-customization > h2 {color:#fff !important}</style>';
	}
} else {
	$Page->Head .= '<meta name="viewport" content="width=1000">';
}
