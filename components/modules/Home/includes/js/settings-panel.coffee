$ ->
	map_container	= $('#map')
	panel			= $('.cs-home-settings-panel')
	$('.cs-home-settings').click ->
		map_container.animate(
			right	: (if panel.css('display') != 'none' then 0 else 310) + 'px'
			'fast'
			->
				map.container.fitToViewport()
		)
		$('.cs-home-settings-panel').toggle('fast')
	$('.cs-home-filter-category [data-id], .cs-home-filter-urgency [data-id]').click ->
		$this	= $(@)
		if $this.hasClass('active')
			$this.removeClass('active')
		else
			$(@)
				.parent()
					.find('.active')
						.removeClass('active')
						.end()
					.end()
				.addClass('active')
		map.update_events(true)
