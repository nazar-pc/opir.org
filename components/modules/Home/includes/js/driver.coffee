$ ->
	if !cs.home.automaidan
		return
	ymaps.ready ->
		init = setInterval (->
			if !window.map
				return
			clearInterval(init)
			my_location	= null
			if navigator.geolocation
				location_updating	= ->
					navigator.geolocation.getCurrentPosition(
						(position) ->
							my_location && map.geoObjects.remove(my_location)
							my_location	= new ymaps.Placemark [position.coords.latitude, position.coords.longitude], {}, {
								iconLayout			: 'default#image'
								iconImageHref		: '/components/modules/Home/includes/img/driver.png'
								iconImageSize		: [40, 38]
								iconImageOffset		: [-16, -38]
								iconImageClipRect	: [[0, 0], [40, 0]]
							}
							map.geoObjects.add(my_location)
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
							alert 'Не вдалось отримати доступ до вашого місцеположення'
						{
							enableHighAccuracy	: true
							timeout				: 30 * 1000	#Wait for 30 seconds max
						}
					)
				location_updating()
			else
				alert 'Потрібен доступ до вашого місцеположення, це потрібно диспетчеру'
			return
		), 100
