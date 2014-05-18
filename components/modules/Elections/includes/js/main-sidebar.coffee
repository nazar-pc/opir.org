$ ->
	if cs.module != 'Elections'
		return
	precints_search_timeout = 0
	last_search_value = ''
	precincts_search_results = $('.cs-elections-precincts-search-results')
	L = cs.Language
	$('.cs-elections-precincts-search').keydown ->
		$this = $(@)
		clearTimeout(precints_search_timeout)
		precints_search_timeout = setTimeout (->
			value = $this.val()
			if value.length < 3
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
					last_search_value = value
					content = ''
					for precinct, precinct of precincts
						content += """<article data-id="#{precinct.id}">
							<h3>""" + L.precint_number(precinct.number) + """</h3>
							<p>#{precinct.address}</p>
						</article>"""
					precincts_search_results.html(content)
				error  : ->
					precincts_search_results.html(L.no_precincts_found)
			)
		), 300
