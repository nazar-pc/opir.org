###*
 * @package        Elections
 * @category       modules
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
###

$ ->
	if cs.module != 'Moderation' || cs.route_path[0] != 'streams'
		return
	streams_container	= $('.cs-moderation')
	L						= cs.Language
	streams_container.html("""
		<h2>#{L.streams_need_checking}</h2>
		<section></section>
	""")
	$.ajax(
		url		: 'api/Moderation/streams/' + cs.route_path[1]
		type	: 'get'
		success	: (streams) ->
			if !streams.length
				streams_container.append("""<p class="uk-text-center">#{L.empty}</p>""")
				return
			ids	= []
			do ->
				for violation, violation of streams
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
					for violation in streams
						precinct = precincts[violation.precinct]
						if !precinct
							continue
						time = new Date(violation.added * 1000)
						time =
							(if time.getHours() < 10 then '0' + time.getHours() else time.getHours()) + ':' + (if time.getMinutes() < 10 then '0' + time.getMinutes() else time.getMinutes())
						video = """<iframe src="#{violation.stream_url}" frameborder="0" scrolling="no"></iframe>"""
						content += """<article data-id="#{violation.id}">
							<h3>
								#{time}
								<span>""" + L.precint_number(precinct.number) + """</span> (#{L.district} #{districts[precinct.id]})
							</h3>
							<p>#{addresses[precinct.id]}</p>
							#{video}
							<p class="uk-text-center">
								<button class="cs-moderation-approve" data-id="#{violation.id}">#{L.approve}</button>
								<button class="cs-moderation-decline" data-id="#{violation.id}">#{L.decline}</button>
							</p>
						</article>"""
					if content
						streams_container.children('section')
							.append(content)
							.masonry(
								columnWidth		: 300
								gutter			: 20
								itemSelector	: 'article'
							)
						setTimeout(check_new_streams, 1000)
					else
						streams_container.append("""<p class="uk-text-center">#{L.empty}</p>""")
				error		: ->
					console.error('Precincts addresses loading error')
			)
		error	: ->
			streams_container.append("""<p class="uk-text-center">#{L.empty}</p>""")
	)
	streams_container
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
					url		: "api/Moderation/streams/#{id}"
					data	:
						status	: status
					type	: 'put'
					success	: ->
						$this.parentsUntil('section').css('visibility', 'hidden')
				)
		)
	if cs.route_path[1] == 'new'
		check_new_streams	= ->
			$.ajax(
				url		: 'api/Moderation/streams/' + cs.route_path[1]
				type	: 'get'
				success	: (streams) ->
					current_available = streams.map (violation) ->
						violation.id
					shown_streams = streams_container.children('section').children('article')
					shown_streams.each ->
						$this = $(@)
						if current_available.indexOf(parseInt($this.data('id'))) == -1
							$this.css('visibility', 'hidden')
					if shown_streams.length
						setTimeout(check_new_streams, 1000)
				error	: ->
			)
