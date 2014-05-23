###*
 * @package        Common
 * @category       modules
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
###

$ ->
	$('.cs-elections-info').click ->
		$('.cs-elections-info-modal')
			.cs().modal('show')
