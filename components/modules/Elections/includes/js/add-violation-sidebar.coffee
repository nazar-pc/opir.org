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
	precinct_sidebar			= $('.cs-elections-precinct-sidebar')
	add_violation_button		= $('.cs-elections-add-violation')
	add_violation_sidebar		= $('.cs-elections-add-violation-sidebar')
	L							= cs.Language
	add_violation_button.click ->
		if !cs.is_user
			sessionStorage.setItem('action', 'add-violation')
			cs.elections.sign_in()
			return
		# If precinct sidebar already opened
		if precinct_sidebar.data('open')
			$('.cs-elections-precinct-sidebar-add-violation').click()
			return
		is_open = add_violation_sidebar.data('open')
		add_violation_sidebar
			.html("""
				<i class="cs-elections-add-violation-sidebar-close uk-icon-times"></i>
				<h2>#{L.add_violation}</h2>
				<input class="cs-elections-add-violation-sidebar-search" type="search" placeholder="#{L.number_or_address}">
				<div class="cs-elections-add-violation-sidebar-search-results"></div>
			""")
			.animate(
				width	: 320
				'fast'
			)
			.data('open', 1)
		if !is_open
			$('.cs-elections-violation-read-more-sidebar-close').click()
			map_container.animate(
				left	: '+=320'
				'fast'
			)
		precints_search_timeout		= 0
		last_search_value			= ''
		precincts_search_results	= $('.cs-elections-add-violation-sidebar-search-results')
		$('.cs-elections-add-violation-sidebar-search').keydown ->
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
		precincts_search_results.on(
			'click'
			'article'
			->
				$this = $(@)
				title = $this.children('h3').html()
				add_violation($this.data('id'), title)
		)
	if sessionStorage.getItem('action') == 'add-violation' && cs.is_user
		sessionStorage.removeItem('action')
		add_violation_button.click()
	precinct_sidebar.on(
		'click'
		'.cs-elections-precinct-sidebar-add-violation'
		->
			$this = $(@)
			add_violation($this.data('id'), precinct_sidebar.children('h2:first').html())
	)
	add_violation = (precinct, title) ->
		if !cs.is_user
			sessionStorage.setItem('action', 'add-violation-for-precinct')
			sessionStorage.setItem('action-details', JSON.stringify([precinct, title]))
			cs.elections.sign_in()
			return
		is_open = add_violation_sidebar.data('open')
		add_violation_sidebar
			.html("""
				<i class="cs-elections-add-violation-sidebar-close uk-icon-times"></i>
				<h2>#{L.add_violation}</h2>
				<h2>#{title}</h2>
				<textarea placeholder="#{L.violation_text}"></textarea>
				<button class="cs-elections-add-violation-add-image">
					<i class="uk-icon-picture-o"></i>
					#{L.photo}
				</button>
				<span>#{L.or}</span>
				<button class="cs-elections-add-violation-add-video">
					<i class="uk-icon-video-camera"></i>
					#{L.video}
				</button>
				<button class="cs-elections-add-violation-add">#{L.add}</button>
			""")
			.animate(
				width	: 320
				'fast'
			)
			.data('open', 1)
		if !is_open
			$('.cs-elections-violation-read-more-sidebar-close').click()
			map_container.animate(
				left	: '+=320'
				'fast'
			)
		add_image_button = $('.cs-elections-add-violation-add-image')
		cs.file_upload(
			add_image_button
			(files) ->
				if files.length
					textarea = add_violation_sidebar.children('textarea')
					for file in files
						textarea.after(
							"""<img src="#{file}" alt="">"""
						)
				cs.elections.loading('hide')
			->
				cs.elections.loading('hide')
			->
				cs.elections.loading('show')
			true
		)
		$('.cs-elections-add-violation-add-video').click ->
			modal = $.cs.simple_modal("""<div class="cs-elections-add-violation-add-video-modal">
				<h2>#{L.video}</h2>
				<input placeholder="#{L.youtube_or_ustream_video_link}">
				<button>#{L.add}</button>
			</div>""")
			modal.find('button').click ->
				video_url = modal.find('input').val()
				if match = /ustream.tv\/(channel|embed)\/([0-9]+)/i.exec(video_url)
					video_url = "https://www.ustream.tv/embed/#{match[2]}"
				else if match = /ustream.tv\/(recorded|embed\/recorded)\/([0-9]+)/i.exec(video_url)
					video_url = "https://www.ustream.tv/embed/recorded/#{match[2]}"
				else if match = /(youtube.com\/embed\/|youtube.com\/watch\?v=)([0-9a-z\-]+)/i.exec(video_url)
					video_url = "https://www.youtube.com/embed/#{match[2]}"
				else
					alert L.bad_link
					return
				add_violation_sidebar.find('iframe').remove()
				add_image_button.before(
					"""<iframe src="#{video_url}" frameborder="0" scrolling="no"></iframe>"""
				)
				modal.hide().remove()
		$('.cs-elections-add-violation-add').click ->
			images	= add_violation_sidebar.children('img')
				.map ->
					$(@).attr('src')
				.get() || []
			images.reverse()
			video	= add_violation_sidebar.children('iframe').attr('src') || ''
			$.ajax(
				url		: "api/Precincts/#{precinct}/violations"
				data	:
					text	: add_violation_sidebar.children('textarea').val()
					images	: images
					video	: video
				type	: 'post'
				success	: ->
					alert L.thank_you_for_your_message
					cs.elections.open_precinct(precinct)
					$('.cs-elections-add-violation-sidebar-close').click()
			)
	if sessionStorage.getItem('action') == 'add-violation-for-precinct' && cs.is_user
		sessionStorage.removeItem('action')
		details = JSON.parse(sessionStorage.getItem('action-details'))
		sessionStorage.removeItem('action-details')
		cs.elections.open_precinct(details[0])
		add_violation(details[0], details[1])
	add_violation_sidebar
		.on(
			'click'
			'.cs-elections-add-violation-sidebar-close'
			->
				if !add_violation_sidebar.data('open')
					return
				add_violation_sidebar.animate(
					width	: 0
					'fast'
				)
				.data('open', 0)
				map_container.animate(
					left	: '-=320'
					'fast'
				)
		)
