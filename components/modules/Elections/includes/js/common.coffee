###*
 * @package        Elections
 * @category       modules
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
###

window.cs.elections               = window.cs.elections || {}
window.cs.elections.get_precincts = (check) ->
	precincts			= localStorage.getItem('precincts')
	if check
		return !!precincts
	precincts = if precincts then JSON.parse(precincts) else {}
	if precincts[0]
		precincts_new_format = {}
		for precinct in precincts
			precincts_new_format[precinct.id] = precinct
		localStorage.setItem('precincts', JSON.stringify(precincts_new_format))
		precincts = precincts_new_format
	precincts
window.cs.elections.get_districts = (check) ->
	districts			= localStorage.getItem('districts')
	if check
		return !!districts
	districts = if districts then JSON.parse(districts) else {}
	if districts[0]
		districts_new_format = {}
		for district in districts
			districts_new_format[district.district] = district
		localStorage.setItem('districts', JSON.stringify(districts_new_format))
		districts = districts_new_format
	districts
window.cs.elections.loading		= (mode) ->
		if mode == 'show'
			$("""
				<div class="cs-elections-loading">
					<i class="uk-icon-spinner uk-icon-spin"></i>
				</div>
			""")
				.hide()
				.appendTo('body')
				.slideDown()
		else
			setTimeout (->
				$('.cs-elections-loading').slideUp().remove()
			), 200
