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
	loading		= (mode) ->
		if mode == 'show'
			$("""
				<div class="cs-elections-loading">
					<i class="uk-icon-spinner uk-icon-spin"></i>
				</div>
			""")
				.hide()
				.appendTo('body')
				.slideDown()
		else
			setTimeout (->
				$('.cs-elections-loading').slideUp().remove()
			), 200
	last_violations_button	= $('.cs-elections-last-violations')
	last_violations_panel	= $('.cs-elections-last-violations-panel')
	L						= cs.Language
	last_violations_button.click ->
		if last_violations_button.is('.cs-elections-last-violations')
			last_violations_button.removeClass('cs-elections-last-violations').addClass('cs-elections-switch-to-map')
		else
			last_violations_button.removeClass('cs-elections-switch-to-map').addClass('cs-elections-last-violations')
			last_violations_panel.slideUp('fast').html('')
			return
		loading('show')
		last_violations_panel
			.html("""
				<h2>#{L.last_violations}</h2>
				<section></section>
			""")
			.slideDown 'fast', ->
				$.ajax(
					url		: "api/Violations?number=20"
					type	: 'get'
					data	: null
					success	: (violations) ->
						ids	= []
						do ->
							for violation of violations
								ids.push(violation.precinct)
						ids	= precincts.join(',')
						$.ajax(
							url			: "api/Precincts?fields=address,district&id=#{ids}"
							type		: 'get'
							data		: null
							success		: (addresses_districts_loaded) ->
								addresses	= {}
								districts	= {}
								do ->
									for p in addresses_districts_loaded
										addresses[p.id]	= p.address
										districts[p.id]	= p.district
									return
								content		= ''
								precincts	= cs.elections.get_precincts()
								for violation in violations
									precinct = precincts[violation.precinct]
									time = new Date(violation.date * 1000)
									time =
										(if time.getHours() < 10 then '0' + time.getHours() else time.getHours()) + ':' + (if time.getMinutes() < 10 then '0' + time.getMinutes() else time.getMinutes())
									text =
										if violation.text
											"<p>" + violation.text.substr(0, 200) + "</p>"
										else
											''
									images =
										if violation.images.length
											violation.images
											.map (image) ->
												"""<figure class="uk-vertical-align"><img src="#{image}" alt="" class="uk-vertical-align-middle"></figure>"""
											.join('')
										else
											''
									video =
										if violation.video
											"""<iframe src="#{violation.video}" frameborder="0" scrolling="no"></iframe>"""
										else
											''
									content += """<article>
										<h3>
											#{time}
											<span>""" + L.precint_number(precinct.number) + """</span> (#{L.district} #{districts[precinct.id]})
										</h3>
										<p>#{addresses[precinct.id]}</p>
										#{text}
										#{images}
										#{video}
									</article>"""
								if content
									last_violations_panel.children('section')
										.append(content)
										.masonry(
											columnWidth		: 300
											gutter			: 20
											itemSelector	: 'article'
										)
								else
									last_violations_panel.append("""<p class="uk-text-center">#{L.empty}</p>""")
								loading('hide')
							error		: ->
								console.error('Precincts addresses loading error')
						)
					error	: ->
						last_violations_panel.append("""<p class="uk-text-center">#{L.empty}</p>""")
						loading('hide')
				)
	last_violations_panel
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
