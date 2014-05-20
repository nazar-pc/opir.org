$ ->
	map_container	= $('#map')
	panel			= $('.cs-home-events-stream-panel')
	$('.cs-home-events-stream').click ->
		shown	= panel.css('width') != '0px'
		map_container.animate(
			left	: (if shown then 0 else 310) + 'px'
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
				left	: '0'
				'fast'
			).removeClass('uk-icon-chevron-left').addClass('uk-icon-chevron-right')
		else
			panel.animate(
				width	: '310'
				'fast'
			)
			$(@).animate(
				left	: '310'
				'fast'
			).removeClass('uk-icon-chevron-right').addClass('uk-icon-chevron-left')
