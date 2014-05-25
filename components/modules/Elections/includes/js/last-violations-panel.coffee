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
	last_violations_button	= $('.cs-elections-last-violations')
	last_violations_panel	= $('.cs-elections-last-violations-panel')
	last_violations_search	= $('.cs-elections-last-violations-panel-search')
	L						= cs.Language
	last_violations_button.click ->
		if !last_violations_button.is('.cs-elections-last-violations')
			last_violations_button.removeClass('cs-elections-switch-to-map').addClass('cs-elections-last-violations')
			last_violations_panel.children('section').remove()
			last_violations_panel
				.slideUp('fast')
				.append('<section/>')
			return
		last_violations_button.removeClass('cs-elections-last-violations').addClass('cs-elections-switch-to-map')
		last_violations_panel.children('section').remove()
		last_violations_panel
			.slideDown(
				'fast'
				find_violations
			)
			.append('<section/>')
	find_violations = ->
		cs.elections.loading('show')
		search = last_violations_search.val()
		$.ajax(
			url		: "api/Violations?number=20&search=" + (if search.length < 3 then '' else search)
			type	: 'get'
			data	: null
			success	: (violations) ->
				ids	= []
				do ->
					for violation, violation of violations
						ids.push(violation.precinct)
				ids	= ids.join(',')
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
									"<p>#{violation.text}</p>"
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
								<div class="cs-elections-social-links" data-violation="#{violation.id}">
									<a class="fb uk-icon-facebook"></a>
									<a class="vk uk-icon-vk"></a>
									<a class="tw uk-icon-twitter"></a>
								</div>
							</article>"""
						if content
							last_violations_panel.children('section')
								.append(content)
								.masonry(
									columnWidth		: 300
									gutter			: 20
									itemSelector	: 'article'
								)
							for violation in violations
								$(".cs-elections-social-links[data-violation=#{violation.id}]").data('violation', violation)
						else
							last_violations_panel.children('section').html("""<p class="uk-text-center">#{L.empty}</p>""")
						cs.elections.loading('hide')
					error		: ->
						console.error('Precincts addresses loading error')
						cs.elections.loading('hide')
				)
			error	: ->
				last_violations_panel.children('section').html("""<p class="uk-text-center">#{L.empty}</p>""")
				cs.elections.loading('hide')
		)
	# Hack to open last violations from the beginning
	last_violations_button.click()
	search_timeout		= 0
	last_search_value	= ''
	last_violations_search.keydown ->
		$this = $(@)
		clearTimeout(search_timeout)
		search_timeout = setTimeout (->
			value = $this.val()
			if value == last_search_value || (value.length < 3 && last_search_value.length < 3)
				return
			last_search_value = value
			last_violations_panel.children('section').remove()
			last_violations_panel.append('<section/>')
			find_violations()
		), 300
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
