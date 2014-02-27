$ ->
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
