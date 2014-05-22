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
	violation_read_more_sidebar	= $('.cs-elections-violation-read-more-sidebar')
	precinct_sidebar
		.on(
			'click'
			'.cs-elections-precinct-sidebar-read-more'
			->
				violation = $(@).data('violation')
				text =
					if violation.text
						"<p>#{violation.text}</p>"
					else
						''
				images =
					if violation.images.length
						violation.images
							.map (image) ->
								"""<img src="#{image}" alt="">"""
							.join('')
					else
						''
				video =
					if violation.video
						"""<iframe src="#{violation.video}" frameborder="0" scrolling="no"></iframe>"""
					else
						''
				is_open = violation_read_more_sidebar.data('open')
				violation_read_more_sidebar
					.html("""
						<i class="cs-elections-violation-read-more-sidebar-close uk-icon-times"></i>
						#{text}
						#{images}
						#{video}
					""")
					.animate(
						width	: 320
						'fast'
					)
					.data('open', 1)
				if !is_open
					$('.cs-elections-add-violation-sidebar-close').click()
					map_container.animate(
						left	: '+=320'
						'fast'
					)
		)
	violation_read_more_sidebar
		.on(
			'click'
			'.cs-elections-violation-read-more-sidebar-close'
			->
				if !violation_read_more_sidebar.data('open')
					return
				violation_read_more_sidebar
					.animate(
						width	: 0
						'fast'
					)
					.data('open', 0)
				map_container.animate(
					left	: '-=320'
					'fast'
				)
		)
		.on(
			'click'
			'img'
			->
				$("""<div>
					<div style="width: 90%;">
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
