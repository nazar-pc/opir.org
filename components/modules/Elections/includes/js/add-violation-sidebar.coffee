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
	map_container			= $('#map')
	precinct_sidebar		= $('.cs-elections-precinct-sidebar')
	add_violation_sidebar	= $('.cs-elections-add-violation-sidebar')
	L						= cs.Language
	precinct_sidebar
		.on(
			'click'
			'.cs-elections-precinct-sidebar-add-violation'
			->
				if !cs.is_user
					cs.elections.sign_in()
					return
				is_open = add_violation_sidebar.data('open')
				add_violation_sidebar
					.html("""
						<i class="cs-elections-add-violation-sidebar-close uk-icon-times"></i>
						<h2>#{L.add_violation}</h2>
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
					null
					null
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
				id = $(@).data('id')
				$('.cs-elections-add-violation-add').click ->
					images	= add_violation_sidebar.children('img')
						.map ->
							$(@).attr('src')
						.get() || []
					video	= add_violation_sidebar.children('iframe').attr('src') || ''
					$.ajax(
						url		: "api/Precincts/#{id}/violations"
						data	:
							text	: add_violation_sidebar.children('textarea').val()
							images	: images
							video	: video
						type	: 'post'
						success	: ->
							alert L.thank_you_for_your_message
							location.reload()
					)
		)
	add_violation_sidebar
		.on(
			'click'
			'.cs-elections-add-violation-sidebar-close'
			->
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