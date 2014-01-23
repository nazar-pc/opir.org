$ ->
	$('.cs-home-settings').click ->
		$('.cs-home-settings-panel').toggle('fast')
	$('.cs-home-filter-category [data-id]').click ->
		$this	= $(@)
		$('.cs-home-filter-category').data('id', $this.data('id'))
		$this.parentsUntil('[data-uk-dropdown]').prev().find('span:last').html($this.find('a').text())
		map.update_events(true)
	$('.cs-home-filter-urgency [data-id]').click ->
		$this	= $(@)
		$('.cs-home-filter-urgency').data('id', $this.data('id'))
		$this.parentsUntil('[data-uk-dropdown]').prev().find('span:last').html($this.find('a').text())
		map.update_events(true)
