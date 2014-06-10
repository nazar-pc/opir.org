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
	last_violations_panel	= $('.cs-elections-last-violations-panel')
	L						= cs.Language
	last_violations_panel
		.on(
			'click'
			'article[data-id]'
			(e) ->
				if $(e.target).is('h3, span, img, iframe, div')
					return
				article	= $(@)
				id		= article.data('id')
				title	= L.violation_number(id)
				modal	= $("""
					<section data-modal-frameless class="cs-elections-last-violations-modal">
						<article data-id="">
							<header>
								<a class="uk-modal-close uk-close"></a>
								<nav>
									<a class="uk-icon-chevron-left prev"></a>
									#{title}
									<a class="uk-icon-chevron-right next"></a></nav>
							</header>
							#{article[0].innerHTML}
						</article>
					</section>
				""")
					.appendTo('body')
					.cs().modal('show')
					.on 'uk.modal.hide', ->
						$(@).remove()
					.on(
						'click'
						'article[data-id] h3 span'
						->
							modal.cs().modal('hide')
							cs.elections.open_precinct(id)
							precinct = cs.elections.get_precincts()[id]
							map.panTo([precinct.lat, precinct.lng]).then ->
								map.zoomRange.get([precinct.lat, precinct.lng]).then (zoomRange) ->
									map.setZoom(
										zoomRange[1],
										duration	: 500
									)
					)
					.on(
						'click'
						'.prev, .next'
						->
							opened_article = last_violations_panel.find("article[data-id=#{id}]")
							if $(@).is('.prev')
								opened_article.prev().click()
							else
								opened_article.next().click()
					)
		)
