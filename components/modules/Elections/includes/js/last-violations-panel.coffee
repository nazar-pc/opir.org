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
	last_violations_content	= last_violations_panel.children('section')
		.masonry(
			columnWidth		: 280
			gutter			: 20
			itemSelector	: 'article'
		)
	last_violations_search	= $('.cs-elections-last-violations-panel-search')
	L						= cs.Language
	data_loading			= false
	last_violations_button.click ->
		if !last_violations_button.is('.cs-elections-last-violations')
			last_violations_button.removeClass('cs-elections-switch-to-map').addClass('cs-elections-last-violations')
			last_violations_panel
				.slideUp(
					'fast'
					->
						last_violations_content
							.html('')
							.masonry('destroy')
							.masonry(
								columnWidth		: 280
								gutter			: 20
								itemSelector	: 'article'
							)
				)
			return
		last_violations_button.removeClass('cs-elections-last-violations').addClass('cs-elections-switch-to-map')
		data_loading = false
		last_violations_panel
			.slideDown(
				'fast'
				find_violations
			)
	find_violations = () ->
		if data_loading
			return
		data_loading = true
		last_id      = last_violations_content.children('article:last').data('id') || 0
		last_violations_content.children('p').remove()
		cs.elections.loading('show')
		search = last_violations_search.val()
		$.ajax(
			url		: "api/Violations?number=20&last_id=#{last_id}&search=" + (if search.length < 3 then '' else search)
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
							content += """<article data-id="#{violation.id}">
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
							do ->
								last_violations_content.append(content)
							appended = []
							for violation in violations
								appended.push(last_violations_content.children("article[data-id=#{violation.id}]")[0])
								$(".cs-elections-social-links[data-violation=#{violation.id}]").data('violation', violation)
							last_violations_content.masonry('reloadItems').masonry('layout')
							data_loading = false
						else if !last_id
							last_violations_content
								.masonry('destroy')
								.html("""<p class="uk-text-center">#{L.empty}</p>""")
							data_loading = false
						cs.elections.loading('hide')
						if last_violations_panel[0].scrollHeight == last_violations_panel.outerHeight()
							find_violations()
					error		: ->
						if !last_id
							console.error('Precincts addresses loading error')
							data_loading = false
						cs.elections.loading('hide')
				)
			error	: ->
				if !last_id
					last_violations_content
						.masonry('destroy')
						.html("""<p class="uk-text-center">#{L.empty}</p>""")
					data_loading = false
				cs.elections.loading('hide')
		)
	# Hack to open last violations from the beginning
	do ->
		interval = setInterval (->
			if cs.elections.get_precincts(true)
				clearInterval(interval)
				last_violations_button.click()
		), 100
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
			last_violations_content
				.masonry('destroy')
				.html('')
				.masonry(
					columnWidth		: 280
					gutter			: 20
					itemSelector	: 'article'
				)
			data_loading = false
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
		.on(
			'click'
			'article[data-id] h3 span'
			->
				cs.elections.open_precinct($(@).data('id'))
		)
		.scroll ->
			if !data_loading && last_violations_panel[0].scrollHeight - last_violations_panel.outerHeight() - last_violations_panel.scrollTop() < 200
				find_violations()
