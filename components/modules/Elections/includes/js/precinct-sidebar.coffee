$ ->
	if cs.module != 'Elections'
		return
	map_container		= $('#map')
	search_results		= $('.cs-elections-precincts-search-results')
	precinct_sidebar	= $('.cs-elections-precinct-sidebar')
	show_timeout		= 0
	L					= cs.Language
	search_results
		.on(
			'mouseenter'
			'[data-id]'
			->
				clearTimeout(show_timeout)
				$this			= $(@)
				show_timeout	= setTimeout (->
					id = parseInt($this.data('id'))
					for precinct, precinct of JSON.parse(localStorage.getItem('precincts'))
						if precinct.id == id
							break
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
		.on(
			'click'
			'[data-id]'
			->
				$this	= $(@)
				id		= parseInt($this.data('id'))
				for precinct, precinct of JSON.parse(localStorage.getItem('precincts'))
					if precinct.id == id
						break
				precinct_sidebar
					.html("""
						<i class="cs-elections-precinct-sidebar-close uk-icon-times"></i>
						<h2>""" + L.precint_number(precinct.number) + """</h2>
						<p>""" + $this.children('p').html() + """</p>
					""")
					.animate(
						width	: 320
						'fast'
					)
				map_container.animate(
					left	: 320
					'fast'
				)
		)
	precinct_sidebar
		.on(
			'click'
			'.cs-elections-precinct-sidebar-close'
			->
				precinct_sidebar.animate(
					width	: 0
					'fast'
				)
				map_container.animate(
					left	: 0
					'fast'
				)
		)
