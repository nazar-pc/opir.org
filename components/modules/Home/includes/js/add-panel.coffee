$ ->
	panel				= $('.cs-home-add-panel')
	category			= 0
	visible				= 0
	time				= 1						#1 day by default
	time_interval		= 86400					# day
	time_limit			= 1						#time limit enabled
	timeout				= time * time_interval * time_limit
	coords				= [0, 0]
	event_coords		= null
	put_events_coords	= false
	map_cursor			= null
	edit_data			= 0
	address_timeout		= 0
	uploader			= null
	reset_options		= ->
		# Reset
		visible				= 2
		time				= 1
		time_interval		= 86400
		time_limit			= 1
		timeout				= time * time_interval * time_limit
		coords				= [0, 0]
		event_coords && map.geoObjects.remove(event_coords)
		event_coords		= null
		map_cursor && map_cursor.remove()
		map_cursor			= null
		put_events_coords	= false
		uploader && uploader.destroy()
		uploader			= null
	$(document).on(
		'click'
		'.cs-home-add, .cs-home-add-close'
		->
			reset_options()
			panel
				.html('')
				.toggle('fast', ->
					if panel.css('display') != 'none'
						if $('.cs-home-settings-panel, .cs-home-settings-coordinator').css('width') != '0px'
							$('.cs-home-settings').click()
						content	= $('.cs-home-filter-category').html()
						panel.html("<ul>#{content}</ul>")
						if cs.home.automaidan
							panel.find('li').each ->
								if $.inArray($(@).data('id'), [1, 3, 6, 7, 8, 17, 21, 22]) == -1 # Magic numbers - id of categories, where driver can add events
									$(@).hide()
				)
	)
	add_event_coords	= (point) ->
		coords			= point
		event_coords && map.geoObjects.remove(event_coords)
		event_coords	= new ymaps.Placemark coords, {}, {
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
	do ->
		map_init = setInterval (->
			if !window.map || !map.events
				return
			clearInterval(map_init)
			map.events.add('click', (e) ->
				if !put_events_coords
					return
				add_event_coords(e.get('coords'))
			)
			return
		), 100
		return
	addition_editing_panel	= ->
		$this	= $(@)
		edit		= $this.hasClass('cs-home-edit')
		if edit
			submit		= """<button class="cs-home-edit-process">Зберегти</button>"""
			name		= cs.home.categories[edit_data.category].name
		else
			category	= $this.data('id')
			submit		= """<button class="cs-home-add-process">Додати</button>"""
			name		= $this.find('span').text()
		content		= """
			<h2>#{name}</h2>
			<textarea placeholder="Коментар"></textarea>
			<button class="cs-home-add-image-button uk-icon-picture-o"> Додати фото</button>"""+
#			<div data-uk-dropdown="{mode:'click'}" class="uk-button-dropdown">
#				<button type="button" class="uk-button">
#					<span class="uk-icon-caret-down"></span> <span>Моїй групі активістів</span>
#				</button>
#				<div class="uk-dropdown">
#					<ul class="cs-home-add-visible uk-nav uk-nav-dropdown">
#						<li class="uk-nav-header">Кому відображати</li>
#						<li data-id="2">
#							<a>Моїй групі активістів</a>
#						</li>""" +
##						<li data-id="1">
##							<a>Активістам</a>
##						</li>
#						"""<li data-id="0">
#							<a>Всім</a>
#						</li>
#					</ul>
#				</div>
#			</div>
			"""<h3>Актуально протягом</h3>
			<div data-uk-dropdown="{mode:'click'}" class="uk-button-dropdown">
				<button type="button" class="uk-button">
					<span class="uk-icon-caret-down"></span> <span>Вказаного часу</span>
				</button>
				<div class="uk-dropdown">
					<ul class="cs-home-add-time-limit uk-nav uk-nav-dropdown">
						<li class="uk-nav-header">Актуально протягом</li>
						<li data-id="1">
							<a>Вказаного часу</a>
						</li>
						<li data-id="0">
							<a>Без обмежень</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="cs-home-actuality-control">
				<input class="cs-home-add-time" type="number" min="1" value="1"/>
				<div data-uk-dropdown="{mode:'click'}" class="uk-button-dropdown">
					<button type="button" class="uk-button">
						<span class="uk-icon-caret-down"></span> <span>Днів</span>
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
			<input type="text" class="cs-home-add-location-address" placeholder="Адреса або точка на карті">
			<button class="cs-home-add-location uk-icon-location-arrow"></button>
			<div>
				<button class="cs-home-add-close uk-icon-times"></button>
				#{submit}
			</div>
		"""
		panel.html(content)
		put_events_coords	= true
		map_cursor			= map.cursors.push('pointer')
		do ->
			uploader_button	= $('.cs-home-add-image-button')
			uploader		= cs.file_upload(
				uploader_button
				(files) ->
					if files.length
						uploader_button.next('img').remove()
						uploader_button.after(
							"""<img src="#{files[0]}" alt="" class="cs-home-add-image">"""
						)
			)
		if edit
			$(".cs-home-add-visible [data-id=#{edit_data.visible}]").click()
			$('.cs-home-add-time-limit [data-id=' + (if edit_data.timeout > 0 then 1 else 0) + ']').click()
			$(".cs-home-add-time").val(edit_data.time).change()
			$(".cs-home-add-time-interval [data-id=#{edit_data.time_interval}]").click()
			panel.find('textarea').val(edit_data.text)
			add_event_coords([edit_data.lat, edit_data.lng])
			if edit_data.img
				$('.cs-home-add-image-button').after(
					"""<img src="#{edit_data.img}" alt="" class="cs-home-add-image">"""
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
			'.cs-home-add-time-limit [data-id]'
			->
				$this		= $(@)
				time_limit	= $this.data('id')
				timeout		= time * time_interval * time_limit
				$('.cs-home-actuality-control')[if time_limit then 'show' else 'hide']()
				$this.parentsUntil('[data-uk-dropdown]').prev().find('span:last').html($this.find('a').text())
		)
		.on(
			'click'
			'.cs-home-add-time-interval [data-id]'
			->
				$this			= $(@)
				time_interval	= $this.data('id')
				timeout			= $('.cs-home-add-time').val() * time_interval * time_limit
				$this.parentsUntil('[data-uk-dropdown]').prev().find('span:last').html($this.find('a').text())
		)
		.on(
			'change'
			'.cs-home-add-time'
			->
				$this	= $(@)
				timeout	= time_interval * $this.val() * time_limit
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
				if category && coords[0] && coords[1]
					comment	= panel.find('textarea').val()
					img		= panel.find('.cs-home-add-image')
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
							img				: if img.length then img.attr('src') else ''
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
				if coords[0] && coords[1]
					comment	= panel.find('textarea').val()
					img		= panel.find('.cs-home-add-image')
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
							img				: if img.length then img.attr('src') else ''
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
		.on(
			'keyup change'
			'.cs-home-add-location-address'
			->
				$this	= $(@)
				if $this.val().length < 4
					return
				clearTimeout(address_timeout)
				address_timeout	= setTimeout (->
					ymaps.geocode($this.val()).then(
						(res) ->
							coords	= res.geoObjects.get(0).geometry.getCoordinates()
							map.panTo(
								coords
								fly				: true
								checkZoomRange	: true
							)
							add_event_coords(coords)
					)
				), 300
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
