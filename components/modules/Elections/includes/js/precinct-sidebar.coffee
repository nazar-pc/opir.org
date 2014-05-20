$ ->
	if cs.module != 'Elections'
		return
	map_container			= $('#map')
	search_results			= $('.cs-elections-precincts-search-results')
	precinct_sidebar		= $('.cs-elections-precinct-sidebar')
	add_violation_sidebar	= $('.cs-elections-add-violation-sidebar')
	show_timeout			= 0
	L						= cs.Language
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
				is_open = precinct_sidebar.data('open')
				precinct_sidebar
					.html("""
						<i class="cs-elections-precinct-sidebar-close uk-icon-times"></i>
						<h2>""" + L.precint_number(precinct.number) + """</h2>
						<p>""" + $this.children('p').html() + """</p>
						<h2>#{L.video_stream}</h2>
						<div class="cs-elections-precinct-sidebar-streams">
							<i class="uk-icon-spinner uk-icon-spin"></i>
						</div>
						<h2>
							<!--<button class="cs-elections-precinct-sidebar-add-violation uk-icon-plus" data-id="#{precinct.id}"></button>-->
							#{L.violations}
						</h2>
						<section class="cs-elections-precinct-sidebar-violations">
							<i class="uk-icon-spinner uk-icon-spin"></i>
						</section>
					""")
					.animate(
						width	: 320
						'fast'
					)
					.data('open', 1)
				if !is_open
					add_violation_sidebar.animate(
						left	: '+=320'
						'fast'
					)
					map_container.animate(
						left	: '+=320'
						'fast'
					)
				streams_container = $('.cs-elections-precinct-sidebar-streams')
				$.ajax(
					url		: "api/Precincts/#{id}/streams"
					type	: 'get'
					data	: null
					success	: (streams) ->
						content = ''
						for stream in streams
							content += """<iframe src="#{stream.stream_url}" frameborder="0" scrolling="no"></iframe>"""
						if content
							streams_container.html(content)
						else
							streams_container.html("""<p class="uk-text-center">#{L.empty}</p>""")
					error	: ->
						streams_container.html("""<p class="uk-text-center">#{L.empty}</p>""")
				)
				violations_container = $('.cs-elections-precinct-sidebar-violations')
				$.ajax(
					url		: "api/Precincts/#{id}/violations"
					type	: 'get'
					data	: null
					success	: (violations) ->
						content = ''
						for violation in violations
							text =
								if violation.text
									"<p>" + violation.text.substr(0, 200) + "</p>"
								else
									''
							images =
								if violation.images.length
									"""<img src="#{violation.images[0]}">"""
								else
									''
							video =
								if violation.video
									"""<iframe src="#{violation.video}" frameborder="0" scrolling="no"></iframe>"""
								else
									''
							content += """<article>
								#{text}
								#{images}
								#{video}
							</article>"""
						if content
							violations_container.html(content)
						else
							violations_container.html("""<p class="uk-text-center">#{L.empty}</p>""")
					error	: ->
						violations_container.html("""<p class="uk-text-center">#{L.empty}</p>""")
				)
		)
	precinct_sidebar
		.on(
			'click'
			'.cs-elections-precinct-sidebar-close'
			->
				precinct_sidebar
					.animate(
						width	: 0
						'fast'
					)
					.data('open', 0)
				add_violation_sidebar.animate(
					left	: '-=320'
					'fast'
				)
				map_container.animate(
					left	: '-=320'
					'fast'
				)
		)
