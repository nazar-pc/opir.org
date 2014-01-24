$ ->
	panel				= $('.cs-home-add-panel')
	category			= 0
	visible				= 0
	urgency				= 'urgent'
	timeout_interval	= 60		# minutes
	timeout				= 15 * timeout_interval	#15 minutes by default
	coords				= [0, 0]
	event_coords		= null
	put_events_coords	= false
	$(document).on(
		'click'
		'.cs-home-add, .cs-home-add-close'
		->
			# Reset
			category			= 0
			visible				= 0
			urgency				= 'urgent'
			timeout_interval	= 60
			timeout				= 15 * timeout_interval
			coords				= [0, 0]
			event_coords && map.geoObjects.remove(event_coords)
			event_coords		= null
			put_events_coords	= false
			panel
				.html('')
				.toggle('fast', ->
					if panel.css('display') != 'none'
						content	= ''
						for category, category of cs.home.categories
							content	+= """
								<li data-id="#{category.id}">
									<img src="/components/modules/Home/includes/img/#{category.id}.png" alt="">
									<span>#{category.name}</span>
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
				put_events_coords		= false
				coords					= e.get('coords')
				event_coords			= new ymaps.Placemark coords, {}, {
					draggable			: true
					iconLayout			: 'default#image'
					iconImageHref		: '/components/modules/Home/includes/img/new-event.png'
					iconImageSize		: [91, 86]
					iconImageOffset		: [-35, -86]
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
	panel
		.on(
			'click'
			'> ul > li'
			->
				category	= $(@).data('id')
				name		= $(@).find('span').text()
				content		= """
					<h2>#{name}</h2>
					<textarea placeholder="Коментар"></textarea>
					<div data-uk-dropdown="{mode:'click'}" class="uk-button-dropdown">
						<button type="button" class="uk-button">
							<span class="uk-icon-caret-down"></span> <span>Відображати всім</span>
						</button>
						<div class="uk-dropdown">
							<ul class="cs-home-add-visible uk-nav uk-nav-dropdown">
								<li data-id="0">
									<a>Відображати всім</a>
								</li>
								<li data-id="1">
									<a>Активістам</a>
								</li>
								<li data-id="2">
									<a>Моїй групі активістів</a>
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
								<li data-id="unknown">
									<a>Терміновість не вказано</a>
								</li>
								<li data-id="can-wait">
									<a>Може почекати</a>
								</li>
								<li data-id="urgent">
									<a>Терміново</a>
								</li>
							</ul>
						</div>
					</div>
					<h3>Актуальність</h3>
					<div>
						<input class="cs-home-add-time" type="number" min="1" value="15"/>
						<div data-uk-dropdown="{mode:'click'}" class="uk-button-dropdown">
							<button type="button" class="uk-button">
								<span class="uk-icon-caret-down"></span> <span>Хвилин</span>
							</button>
							<div class="uk-dropdown">
								<ul class="cs-home-add-time-interval uk-nav uk-nav-dropdown">
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
						<button class="cs-home-add-process">Додати</button>
					</div>
				"""
				panel.html(content)
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
				$this.parentsUntil('[data-uk-dropdown]').prev().find('span:last').html($this.find('a').text())
		)
		.on(
			'click'
			'.cs-home-add-time-interval [data-id]'
			->
				$this				= $(@)
				timeout_interval	= $this.data('id')
				timeout				= $('.cs-home-add-time').val() * timeout_interval
				$this.parentsUntil('[data-uk-dropdown]').prev().find('span:last').html($this.find('a').text())
		)
		.on(
			'change'
			'.cs-home-add-time'
			->
				$this	= $(@)
				timeout	= timeout_interval * $this.val()
				$this.parentsUntil('[data-uk-dropdown]').prev().find('span:last').html($this.find('a').text())
		)
		.on(
			'click'
			'.cs-home-add-location'
			->
				if event_coords
					return
				put_events_coords	= true
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
							category	: category
							timeout		: timeout
							lat			: coords[0]
							lng			: coords[1]
							visible		: visible
							text		: comment
							urgency		: urgency
						success	: ->
							panel.hide('fast')
							map.geoObjects.remove(event_coords)
							event_coords		= null
							put_events_coords	= false
							map.update_events()
							alert 'Успішно додано, дякуємо вам!'
					)
				else
					alert 'Вкажіть точку на карті'
		)
