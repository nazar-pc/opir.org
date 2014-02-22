$ ->
	map_container	= $('#map')
	panel			= $('.cs-home-settings-panel, .cs-home-settings-coordinator')
	if !cs.home?.automaidan && !cs.home?.automaidan_coord && !cs.is_admin
		panel.find('[data-id]').each ->
#			if $.inArray($(@).data('id'), [1, 3, 6, 7, 8, 17, 21, 22]) != -1
#				$(@).remove()
		panel.find('[data-group]:not([data-id])').each ->
			prev	= $(@).prev()
			if prev.is('[data-group]:not([data-id])')
				prev.remove()
	$('.cs-home-settings').click ->
		shown	= panel.css('width') != '0px'
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
	filter_category	= $('.cs-home-filter-category')
	filter_category
		.find('[data-id]')
			.click ->
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
			.end()
		.find('[data-group]').not('[data-id]').click ->
			group	= $(@).data('group')
			$(".cs-home-filter-category [data-id][data-group=#{group}]").toggleClass('active')
			map.update_events(true)
