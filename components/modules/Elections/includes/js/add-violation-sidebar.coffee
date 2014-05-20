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
	search_results			= $('.cs-elections-precincts-search-results')
	precinct_sidebar		= $('.cs-elections-precinct-sidebar')
	add_violation_sidebar	= $('.cs-elections-add-violation-sidebar')
	show_timeout			= 0
	L						= cs.Language
	precinct_sidebar
		.on(
			'click'
			'.cs-elections-precinct-sidebar-add-violation'
			->
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
