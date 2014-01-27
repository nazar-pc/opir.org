$ ->
	panel				= $('.cs-home-add-panel')
	category			= 0
	visible				= 2
	urgency				= 'urgent'
	time				= 15					#15 minutes by default
	time_interval		= 60					# minutes
	timeout				= time * time_interval
	coords				= [0, 0]
	event_coords		= null
	put_events_coords	= false
	map_cursor			= null
	edit_data			= 0
	reset_options		= ->
		# Reset
		visible				= 2
		urgency				= 'urgent'
		time				= 15
		time_interval		= 60
		timeout				= time * time_interval
		coords				= [0, 0]
		event_coords && map.geoObjects.remove(event_coords)
		event_coords		= null
		map_cursor && map_cursor.remove()
		map_cursor			= null
		put_events_coords	= false
	$(document).on(
		'click'
		'.cs-home-add, .cs-home-add-close'
		->
			reset_options()
			panel
				.html('')
				.toggle('fast', ->
					if panel.css('display') != 'none'
						content	= ''
						for id, category of cs.home.categories
							content	+= """
								<li data-id="#{id}">
									<img src="/components/modules/Home/includes/img/#{id}.png" alt="">
									<span>#{category}</span>
								</li>
							"""
						panel.html("<ul>#{content}</ul>")
				)
	)
	do ->
		map_init = setInterval (->
			if !window.map || !map.events
				return
			clearInterval(map_init)
			map.events.add('click', (e) ->
				if !put_events_coords
					return
				coords					= e.get('coords')
				event_coords && map.geoObjects.remove(event_coords)
				event_coords			= new ymaps.Placemark coords, {}, {
					draggable			: true
					iconLayout			: 'default#image'
					iconImageHref		: '/components/modules/Home/includes/img/new-event.png'
					iconImageSize		: [91, 86]
					iconImageOffset		: [-36, -86]
				}
				map.geoObjects.add(event_coords)
				event_coords.events.add(
					'geometrychange',
					(e) ->
						coords	= e.get('originalEvent').originalEvent.newCoordinates
				)
			)
			return
		), 100
		return
	addition_editing_panel	= ->
		$this	= $(@)
		edit		= $this.hasClass('cs-home-edit')
		if edit
			submit		= """<button class="cs-home-edit-process">Зберегти</button>"""
			name		= cs.home.categories[edit_data.category]
		else
			category	= $this.data('id')
			submit		= """<button class="cs-home-add-process">Додати</button>"""
			name		= $this.find('span').text()
		content		= """
			<h2>#{name}</h2>
			<textarea placeholder="Коментар"></textarea>
			<div data-uk-dropdown="{mode:'click'}" class="uk-button-dropdown">
				<button type="button" class="uk-button">
					<span class="uk-icon-caret-down"></span> <span>Моїй групі активістів</span>
				</button>
				<div class="uk-dropdown">
					<ul class="cs-home-add-visible uk-nav uk-nav-dropdown">
						<li class="uk-nav-header">Кому відображати</li>
						<li data-id="2">
							<a>Моїй групі активістів</a>
						</li>""" +
#						<li data-id="1">
#							<a>Активістам</a>
#						</li>
						"""<li data-id="0">
							<a>Всім</a>
						</li>
					</ul>
				</div>
			</div>
			<div data-uk-dropdown="{mode:'click'}" class="uk-button-dropdown">
				<button type="button" class="uk-button">
					<span class="uk-icon-caret-down"></span> <span>Терміново</span>
				</button>
				<div class="uk-dropdown">
					<ul class="cs-home-add-urgency uk-nav uk-nav-dropdown">
						<li class="uk-nav-header">Терміновість</li>
						<li data-id="urgent">
							<a>Терміново</a>
						</li>
						<li data-id="can-wait">
							<a>Може почекати</a>
						</li>
						<li data-id="unknown">
							<a>Не вказано</a>
						</li>
					</ul>
				</div>
			</div>
			<h3 class="cs-home-actuality-control">Актуально протягом</h3>
			<div class="cs-home-actuality-control">
				<input class="cs-home-add-time" type="number" min="1" value="15"/>
				<div data-uk-dropdown="{mode:'click'}" class="uk-button-dropdown">
					<button type="button" class="uk-button">
						<span class="uk-icon-caret-down"></span> <span>Хвилин</span>
					</button>
					<div class="uk-dropdown">
						<ul class="cs-home-add-time-interval uk-nav uk-nav-dropdown">
							<li class="uk-nav-header">Одиниці часу</li>
							<li data-id="60">
								<a>Хвилин</a>
							</li>
							<li data-id="3600">
								<a>Годин</a>
							</li>
							<li data-id="86400">
								<a>Днів</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="cs-home-add-location">
				<span>Вказати на карті</span>
			</div>
			<div>
				<button class="cs-home-add-close"></button>
				#{submit}
			</div>
		"""
		panel.html(content)
		put_events_coords	= true
		map_cursor			= map.cursors.push('pointer');
		if edit
			$(".cs-home-add-visible [data-id=#{edit_data.visible}]").click()
			$(".cs-home-add-urgency [data-id=#{edit_data.urgency}]").click()
			$(".cs-home-add-time").val(edit_data.time).change()
			$(".cs-home-add-time-interval [data-id=#{edit_data.time_interval}]").click()
			panel.find('textarea').val(edit_data.text)
			coords				= [edit_data.lat, edit_data.lng]
			event_coords && map.geoObjects.remove(event_coords)
			event_coords			= new ymaps.Placemark coords, {}, {
				draggable			: true
				iconLayout			: 'default#image'
				iconImageHref		: '/components/modules/Home/includes/img/new-event.png'
				iconImageSize		: [91, 86]
				iconImageOffset		: [-36, -86]
				zIndex				: 1000
			}
			map.geoObjects.add(event_coords)
			event_coords.events.add(
				'geometrychange',
				(e) ->
					coords	= e.get('originalEvent').originalEvent.newCoordinates
			)
	panel
		.on(
			'click'
			'> ul > li'
			addition_editing_panel
		)
		.on(
			'click'
			'.cs-home-add-visible [data-id]'
			->
				$this	= $(@)
				visible	= $this.data('id')
				$this.parentsUntil('[data-uk-dropdown]').prev().find('span:last').html($this.find('a').text())
		)
		.on(
			'click'
			'.cs-home-add-urgency [data-id]'
			->
				$this	= $(@)
				urgency	= $this.data('id')
				if urgency == 'unknown'
					$('.cs-home-actuality-control').hide('fast')
				else
					$('.cs-home-actuality-control').show('fast')
				$this.parentsUntil('[data-uk-dropdown]').prev().find('span:last').html($this.find('a').text())
		)
		.on(
			'click'
			'.cs-home-add-time-interval [data-id]'
			->
				$this				= $(@)
				time_interval	= $this.data('id')
				timeout				= $('.cs-home-add-time').val() * time_interval
				$this.parentsUntil('[data-uk-dropdown]').prev().find('span:last').html($this.find('a').text())
		)
		.on(
			'change'
			'.cs-home-add-time'
			->
				$this	= $(@)
				timeout	= time_interval * $this.val()
				$this.parentsUntil('[data-uk-dropdown]').prev().find('span:last').html($this.find('a').text())
		)
		.on(
			'click'
			'.cs-home-add-location'
			->
				alert 'Клікніть місце з подією на карті'
		)
		.on(
			'click'
			'.cs-home-add-process'
			->
				comment	= panel.find('textarea').val()
				if category && timeout && coords[0] && coords[1] && urgency
					$.ajax(
						url		: 'api/Home/events'
						type	: 'post'
						data	:
							category		: category
							time			: time
							time_interval	: time_interval
							timeout			: timeout
							lat				: coords[0]
							lng				: coords[1]
							visible			: visible
							text			: comment
							urgency			: urgency
						success	: ->
							panel.hide('fast')
							map.geoObjects.remove(event_coords)
							event_coords		= null
							put_events_coords	= false
							map_cursor.remove()
							map.update_events()
							alert 'Успішно додано, дякуємо вам!'
					)
				else
					alert 'Вкажіть точку на карті'
		)
		.on(
			'click'
			'.cs-home-edit-process'
			->
				comment	= panel.find('textarea').val()
				if timeout && coords[0] && coords[1] && urgency
					$.ajax(
						url		: "api/Home/events/#{edit_data.id}"
						type	: 'put'
						data	:
							time			: time
							time_interval	: time_interval
							timeout			: timeout
							lat				: coords[0]
							lng				: coords[1]
							visible			: visible
							text			: comment
							urgency			: urgency
						success	: ->
							panel.hide('fast')
							map.geoObjects.remove(event_coords)
							event_coords		= null
							put_events_coords	= false
							map_cursor.remove()
							map.update_events()
							alert 'Успішно відредаговано!'
					)
				else
					alert 'Вкажіть точку на карті'
		)
	$('#map')
		.on(
			'click'
			'.cs-home-edit'
			->
				item	= @
				$.ajax(
					url		: 'api/Home/events/' + $(@).data('id')
					type	: 'get'
					success	: (data) ->
						window.map.balloon.close()
						edit_data	= data
						reset_options()
						panel.show('fast')
						addition_editing_panel.call(item)
				)
		)
