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
	precints_search_timeout = 0
	last_search_value			= ''
	last_violations_button		= $('.cs-elections-last-violations')
	precincts_search_results	= $('.cs-elections-precincts-search-results')
	L = cs.Language
	$('.cs-elections-precincts-search').keydown ->
		if !last_violations_button.is('.cs-elections-last-violations')
			return
		$this = $(@)
		clearTimeout(precints_search_timeout)
		precints_search_timeout = setTimeout (->
			value = $this.val()
			if value.length < 3 && `value != parseInt(value)`
				precincts_search_results.html('')
				return
			if value == last_search_value
				return
			$.ajax(
				url    : 'api/Precincts/search'
				data   :
					text       : value
					coordinates: JSON.parse(localStorage.getItem('coordinates'))
				type   : 'get'
				success: (precincts) ->
					if precincts.length
						last_search_value = value
						content = ''
						for precinct, precinct of precincts
							title =
								if precinct.number == '0'
									L.cec
								else if precinct.number.length > 3
									L.district_precint_number(precinct.number)
								else
									L.district_number(precinct.number)
							content += """<article data-id="#{precinct.id}">
								<h3>#{title}</h3>
								<p>#{precinct.address}</p>
							</article>"""
						precincts_search_results.html(content)
					else
						precincts_search_results.html("""<article>#{L.no_precincts_found}</article>""")
				error  : ->
					precincts_search_results.html("""<article>#{L.no_precincts_found}</article>""")
			)
		), 300
	show_timeout			= 0
	precincts_search_results
		.on(
			'mouseenter'
			'[data-id]'
			->
				clearTimeout(show_timeout)
				$this			= $(@)
				show_timeout	= setTimeout (->
					id = parseInt($this.data('id'))
					precinct = cs.elections.get_precincts()[id]
					map.panTo([precinct.lat, precinct.lng]).then ->
						map.zoomRange.get([precinct.lat, precinct.lng]).then (zoomRange) ->
							map.setZoom(
								zoomRange[1],
								duration	: 500
							)
				), 200
		)
		.on(
			'mouseleave'
			'[data-id]'
			->
				clearTimeout(show_timeout)
		)
