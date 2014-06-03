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
	map_container				= $('#map')
	precincts_search_results	= $('.cs-elections-precincts-search-results')
	precinct_sidebar			= $('.cs-elections-precinct-sidebar')
	add_violation_sidebar		= $('.cs-elections-add-violation-sidebar')
	L							= cs.Language
	precincts_search_results
		.on(
			'click'
			'[data-id]'
			->
				cs.elections.open_precinct(
					parseInt($(@).data('id'))
				)
		)
	window.cs.elections = window.cs.elections || {}
	window.cs.elections.open_precinct = (id) ->
		$.ajax(
			url			: "api/Precincts/#{id}"
			type		: 'get'
			data		: null
			success		: (precinct) ->
				is_open = precinct_sidebar.data('open')
				precinct_sidebar
					.html("""
						<i class="cs-elections-precinct-sidebar-close uk-icon-times"></i>
						<h2>""" + L.precint_number(precinct.number) + """</h2>
						<p>#{L.district} #{precinct.district}</p>
						<p class="cs-elections-precinct-sidebar-address">
							<i class="uk-icon-location-arrow"></i>
							<span>#{precinct.address}</span>
						</p>
						<h2>
							<button class="cs-elections-precinct-sidebar-add-stream uk-icon-plus" data-id="#{precinct.id}"></button>
							#{L.video_stream}
						</h2>
						<div class="cs-elections-precinct-sidebar-streams">
							<i class="uk-icon-spinner uk-icon-spin"></i>
						</div>
						<h2>
							<button class="cs-elections-precinct-sidebar-add-violation uk-icon-plus" data-id="#{precinct.id}"></button>
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
				$('.cs-elections-switch-to-map').click()
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
				violations_container	= $('.cs-elections-precinct-sidebar-violations')
				$.ajax(
					url		: "api/Precincts/#{id}/violations"
					type	: 'get'
					data	: null
					success	: (violations) ->
						content		= ''
						for violation in violations
							text =
								if violation.text
									"<p>" + violation.text.substr(0, 200) + "</p>"
								else
									''
							images =
								if violation.images.length
									"""<img src="#{violation.images[0]}" alt="">"""
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
								<div class="cs-elections-social-links" data-violation="#{violation.id}">
									<a class="fb uk-icon-facebook"></a>
									<a class="vk uk-icon-vk"></a>
									<a class="tw uk-icon-twitter"></a>
								</div>
								<div class="cs-elections-precinct-sidebar-read-more" data-id="#{violation.id}">#{L.read_more} »</div>
							</article>"""
						if content
							violations_container.html(content)
							for violation in violations
								$(".cs-elections-social-links[data-violation=#{violation.id}]").data('violation', violation)
								$(".cs-elections-precinct-sidebar-read-more[data-id=#{violation.id}]").data('violation', violation)
						else
							violations_container.html("""<p class="uk-text-center">#{L.empty}</p>""")
					error	: ->
						violations_container.html("""<p class="uk-text-center">#{L.empty}</p>""")
				)
				violations_container
					.on(
						'click'
						'img'
						->
							$("""<div>
								<div style="text-align: center; width: 90%;">
									#{@.outerHTML}
								</div>
							</div>""")
								.appendTo('body')
								.cs().modal('show')
								.click ->
									$(@).hide()
								.on 'uk.modal.hide', ->
									$(this).remove()
					)
		)
	precinct_sidebar
		.on(
			'click'
			'.cs-elections-precinct-sidebar-close'
			->
				if !precinct_sidebar.data('open')
					return
				$('.cs-elections-violation-read-more-sidebar-close').click()
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
		.on(
			'click'
			'.cs-elections-precinct-sidebar-add-stream'
			->
				add_stream($(@).data('id'))
		)
	add_stream = (precinct) ->
		if !cs.is_user
			sessionStorage.setItem('action', 'add-stream-for-precinct')
			sessionStorage.setItem('action-details', precinct)
			cs.elections.sign_in()
			return
		modal		= $.cs.simple_modal("""<div class="cs-elections-precinct-sidebar-add-stream-modal">
			<h2>#{L.stream}</h2>
			<input placeholder="#{L.youtube_or_ustream_stream_link}">
			<button>#{L.add}</button>
		</div>""")
		modal.find('button').click ->
			stream_url = modal.find('input').val()
			if match = /ustream.tv\/(channel|embed)\/([0-9]+)/i.exec(stream_url)
				stream_url = "https://www.ustream.tv/embed/#{match[2]}"
			else if match = /(youtube.com\/embed\/|youtube.com\/watch\?v=)([0-9a-z\-]+)/i.exec(stream_url)
				stream_url = "https://www.youtube.com/embed/#{match[2]}"
			else
				alert L.bad_link
				return
			$.ajax(
				url		: "api/Precincts/#{precinct}/streams"
				data	:
					stream_url	: stream_url
				type	: 'post'
				success	: ->
					alert L.thank_you_for_your_stream
					cs.elections.open_precinct(precinct)
			)
			modal.hide().remove()
	if sessionStorage.getItem('action') == 'add-stream-for-precinct' && cs.is_user
		sessionStorage.removeItem('action')
		precinct = sessionStorage.getItem('action-details')
		sessionStorage.removeItem('action-details')
		cs.elections.open_precinct(precinct)
		add_stream(precinct)
	if cs.route[0] == 'violation'
		cs.elections.open_precinct(cs.route[1])
		interval_loading = setInterval (->
			read_more = $(".cs-elections-precinct-sidebar-read-more[data-id=#{cs.route[2]}]")
			if read_more.length
				clearInterval(interval_loading)
				read_more.click()
		), 300
