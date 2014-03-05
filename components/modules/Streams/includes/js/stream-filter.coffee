$ ->
	streams_list	= $('.cs-stream-list')
	$('.cs-stream-show [data-mode]').click ->
		$this	= $(@)
		if $this.data('mode') == 'map'
			streams_list.hide()
		else
			streams_list.show()
		$this.parentsUntil('[data-uk-dropdown]').prev().find('span:last').html($this.find('a').text())
	stream_filter	= $('.cs-stream-filter')
	last			= ''
	added_tags		= $('.cs-stream-added-tags')
	found_tags		= $('.cs-stream-found-tags')
	stream_filter.keyup ->
		val	= stream_filter.val()
		if last == val
			return
		last	= val
		if val.length > 2
			$.ajax(
				url		: 'api/Streams/tags'
				data	:
					title	: val
				type	: 'get'
				success	: (tags) ->
					found_tags.html(
						(for tag, tag of tags
							"""<button data-id="#{tag.id}"><i class="uk-icon-plus"></i> #{tag.title}</button>"""
						).join()
					)
				error	: ->
					found_tags.html('')
			)
	added_tags.on(
		'click'
		'button'
		->
			$(@).remove()
			map.add_streams_on_map()
	)
	found_tags.on(
		'click'
		'button'
		->
			added_tags.append(
				$(@).detach()[0].outerHTML.replace(/uk-icon-plus/, 'uk-icon-times')
			)
			map.add_streams_on_map()
			last	= ''
			stream_filter.val('')
	)
	panel			= $('.cs-stream-filter-panel')
	map_container	= $('#map')
	$('.cs-stream-filter-hide').click ->
		shown	= panel.css('width') != '0px'
		streams_list.animate(
			right	: (if shown then 0 else 310) + 'px'
			'fast'
		)
		map_container.animate(
			right	: (if shown then 0 else 310) + 'px'
			'fast'
			->
				map.container.fitToViewport()
		)
		if shown
			panel.animate(
				width	: '0'
				'fast'
			)
			$(@).animate(
				right	: '0'
				'fast'
			).removeClass('uk-icon-chevron-right').addClass('uk-icon-chevron-left')
		else
			panel.animate(
				width	: '310'
				'fast'
			)
			$(@).animate(
				right	: '310'
				'fast'
			).removeClass('uk-icon-chevron-left').addClass('uk-icon-chevron-right')
