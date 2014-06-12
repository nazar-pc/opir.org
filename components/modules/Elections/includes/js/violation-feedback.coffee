###*
 * @package        Elections
 * @category       modules
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
###

$ ->
	if cs.module != 'Elections'
		return
	L = cs.Language
	$(document).on(
		'click'
		'.cs-elections-violation-read-more-sidebar button'
		->
			id		= $(@).parent().data('id')
			value	= if $(@).is('.not-true') then -1 else 1
			$.ajax(
				url			: "api/Precincts/violations/#{id}/feedback"
				type		: 'post'
				data		:
					value	: value
				success		: (data) ->
					if data != 'ok'
						alert L.looks_like_feedback_already_given
					else
						alert L.thank_you_for_feedback
			)
	)
