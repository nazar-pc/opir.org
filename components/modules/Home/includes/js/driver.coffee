$ ->
	if !cs.home.automaidan
		return
	ymaps.ready ->
		add_zero		= (input) ->
			if input < 10 then '0' + input else input
		init = setInterval (->
			if !window.map
				return
			clearInterval(init)
			my_location		= null
			driving_point	= null
			driving_route	= null
			event_coords	= null
			if navigator.geolocation
				location_updating	= ->
					navigator.geolocation.getCurrentPosition(
						(position) ->
							my_location && map.geoObjects.remove(my_location)
							my_location	= new ymaps.Placemark(
								[position.coords.latitude, position.coords.longitude]
								{
									hintContent				: 'Тут знаходитесь ви'
								}
								{
									iconLayout			: 'default#image'
									iconImageHref		: '/components/modules/Home/includes/img/driver.png'
									iconImageSize		: [40, 38]
									iconImageOffset		: [-16, -38]
									iconImageClipRect	: [[0, 0], [40, 0]]
								}
							)
							map.geoObjects.add(my_location)
							if driving_route
								ymaps.route(
									[
										my_location.geometry.getCoordinates(), event_coords
									],
									{
										avoidTrafficJams	: true
									}
								).then (route) ->
									driving_route	= route
									route.getWayPoints().removeAll()
									map.geoObjects.add(route);
							$.ajax(
								url			: 'api/Home/driver_location'
								type		: 'put'
								data		:
									lat			: position.coords.latitude
									lng			: position.coords.longitude
								complete	: ->
									setTimeout(location_updating, 2 * 1000)
							)
						->
							setTimeout(location_updating, 2 * 1000)
							alert 'Не вдалось отримати доступ до вашого місцеположення'
						{
							enableHighAccuracy	: true
							timeout				: 30 * 1000	#Wait for 30 seconds max
						}
					)
				setTimeout(location_updating, 0)
			else
				setTimeout(location_updating, 2 * 1000)
				alert 'Дозвольте доступ до вашого місцеположення, це потрібно диспетчеру'
			check_assignment	= ->
				if driving_point || !my_location
					setTimeout(check_assignment, 100)
					return
				$.ajax(
					url			: "api/Home/events/check"
					type		: 'get'
					complete	: ->
						setTimeout(check_assignment, 3000)
					success		: (event) ->
						category_name	= cs.home.categories[event.category].name
						t				= new Date(event.timeout * 1000)
						time			=
							add_zero(t.getHours()) + ':' + add_zero(t.getMinutes()) + ' ' +
							add_zero(t.getDate()) + '.' + add_zero(t.getMonth() + 1) + '.' + t.getFullYear()
						time			= if event.timeout > 0 then "<time>Актуально до #{time}</time>" else ''
						text			= event.text.replace(/\n/g, '<br>')
						text			= if text then """<p>#{text}</p>""" else ''
						img				= if event.img then """<p><img height="240" width="260" src="#{event.img}" alt=""></p>""" else ''
						driving_point && map.geoObjects.remove(driving_point)
						driving_point	= new ymaps.Placemark(
							[event.lat, event.lng]
							{
								hintContent				: category_name
								balloonContentHeader	: category_name
								balloonContentBody		: """
									#{time}
									#{img}
									#{text}
								"""
								balloonContentFooter	: """
									<p><b>Координатор просить вас приїхати сюди і підтвердити подію коли будете на місці</b></p>
									<button class="cs-home-check-confirm" data-id="#{event.id}">Підтвердити подію</button> <button class="cs-home-check-refuse" data-id="#{event.id}">Відмовитись</button>
								"""
							}
							{
								iconLayout			: 'default#image'
								iconImageHref		: '/components/modules/Home/includes/img/events.png'
								iconImageSize		: [59, 56]
								iconImageOffset		: [-24, -56]
								iconImageClipRect	: [[59, 56 * (event.category - 1)], [59 * 2, 56 * event.category]]
							}
						)
						event_coords	= [event.lat, event.lng]
						ymaps.route(
							[
								my_location.geometry.getCoordinates(), event_coords
							],
							{
								avoidTrafficJams	: true
								mapStateAutoApply	: true
							}
						).then (route) ->
							driving_route	= route
							route.getWayPoints().removeAll()
							map.geoObjects.add(route);
						map.geoObjects.add(driving_point)
						driving_point.balloon.open()
					error		: ->
				)
			setTimeout(check_assignment, 0)
			$('#map')
				.on(
					'click'
					'.cs-home-check-confirm'
					->
						$.ajax(
							url			: 'api/Home/events/' + $(@).data('id') + '/check'
							type		: 'put'
							success		: ->
								driving_point && map.geoObjects.remove(driving_point)
								driving_point	= null
								driving_route && map.geoObjects.remove(driving_route)
								driving_route	= null
								alert 'Підтвердження отримано, дякуємо вам!'
						)
				)
				.on(
					'click'
					'.cs-home-check-refuse'
					->
						$.ajax(
							url			: 'api/Home/events/' + $(@).data('id') + '/check'
							type		: 'delete'
							success		: ->
								driving_point && map.geoObjects.remove(driving_point)
								driving_point	= null
								driving_route && map.geoObjects.remove(driving_route)
								driving_route	= null
								alert 'Запит на підтвердження відхилено'
						)
				)
			return
		), 100
