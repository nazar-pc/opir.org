###*
 * @package        Elections
 * @category       modules
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
###

$ ->
	if cs.module != 'Moderation' || cs.route_path[0] != 'violations'
		return
	violations_container	= $('.cs-moderation')
	L						= cs.Language
	violations_container.html("""
		<h2>#{L.violations_need_checking}</h2>
		<section></section>
	""")
	$.ajax(
		url		: 'api/Moderation/violations/' + cs.route_path[1]
		type	: 'get'
		success	: (violations) ->
			if !violations.length
				violations_container.append("""<p class="uk-text-center">#{L.empty}</p>""")
				return
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
					precincts	= JSON.parse(localStorage.getItem('precincts'))
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
							<p class="uk-text-center">
								<button class="cs-moderation-approve" data-id="#{violation.id}">#{L.approve}</button>
								<button class="cs-moderation-decline" data-id="#{violation.id}">#{L.decline}</button>
							</p>
						</article>"""
					if content
						violations_container.children('section')
							.append(content)
							.masonry(
								columnWidth		: 300
								gutter			: 20
								itemSelector	: 'article'
							)
						setTimeout(check_new_violations, 1000)
					else
						violations_container.append("""<p class="uk-text-center">#{L.empty}</p>""")
				error		: ->
					console.error('Precincts addresses loading error')
			)
		error	: ->
			violations_container.append("""<p class="uk-text-center">#{L.empty}</p>""")
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
		.on(
			'click'
			'.cs-moderation-approve, .cs-moderation-decline'
			->
				$this	= $(@)
				status	= if $this.hasClass('cs-moderation-approve') then 1 else 0
				id		= $this.data('id')
				$.ajax(
					url		: "api/Moderation/violations/#{id}"
					data	:
						status	: status
					type	: 'put'
					success	: (result) ->
						$this.parentsUntil('section').css('visibility', 'hidden')
						if status == 0 && result.rating < 0 && result.rating % 5 == 0
							if confirm L.bad_user_rating(result.username, result.rating)
								$.ajax(
									url		: "api/Moderation/violations/block_user/#{result.user}"
									type	: 'post'
									success	: ->
										alert L.done
								)
				)
		)
	if cs.route_path[1] == 'new'
		check_new_violations	= ->
			$.ajax(
				url		: 'api/Moderation/violations/' + cs.route_path[1]
				type	: 'get'
				success	: (violations) ->
					current_available = violations.map (violation) ->
						violation.id
					shown_violations = violations_container.children('section').children('article')
					shown_violations.each ->
						$this = $(@)
						if current_available.indexOf(parseInt($this.data('id'))) == -1
							$this.css('visibility', 'hidden')
					if shown_violations.length
						setTimeout(check_new_violations, 1000)
				error	: ->
			)
