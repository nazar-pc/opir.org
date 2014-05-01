$ ->
	if cs.module != 'Elections'
		return
	user_location = null
	ymaps.ready ->
		user_location	= cs.getcookie('coordinates')
		if user_location
			user_location	= JSON.parse(user_location)
			begin()
		else
			ymaps.geolocation.get(
				autoReverseGeocode	: false
				provider			: 'yandex'
			).then (result) ->
				user_location	= result.geoObjects.get(0).geometry.getCoordinates()
				setTimeout(begin, 0)
				ymaps.geolocation.get(
					autoReverseGeocode	: false
				).then (result) ->
					user_location	= result.geoObjects.get(0).geometry.getCoordinates()
					map.panTo(user_location)
					cs.setcookie('location', JSON.stringify(user_location))
	begin = ->
		window.map	= new ymaps.Map(
			'map'
			{
				center				: user_location
				zoom				: 15
				controls			: ['typeSelector', 'zoomControl', 'fullscreenControl', 'rulerControl', 'trafficControl']
			}
			{
				avoidFractionalZoom	: false
			}
		)
		cluster_icons	= [
			{
				href	: '/components/modules/Elections/includes/img/cluster-46.png'
				size	: [46, 46]
				offset	: [-23, -23]
			}
			{
				href	: '/components/modules/Elections/includes/img/cluster-58.png'
				size	: [58, 58]
				offset	: [-27, -27]
			}
		]
		districts_clusterer		= new ymaps.Clusterer(
			clusterIcons	: cluster_icons
			hasBalloon		: false
			hasHint			: false
		)
		precincts_clusterer	= new ymaps.Clusterer(
			clusterIcons	: cluster_icons
			hasHint			: false
		)
		map.geoObjects.add(precincts_clusterer)
		do ->
			previous_zoom	= 15
			map.events.add('boundschange', (e)->
				# If previous and current zoom both smaller or greater than 14 - there is no need to change placemarks detalization
				if (previous_zoom < 14) == (e.get('newZoom') < 14)
					return
				previous_zoom	= e.get('newZoom')
				if previous_zoom < 14
					map.geoObjects.remove(precincts_clusterer)
					map.geoObjects.add(districts_clusterer)
				else
					map.geoObjects.remove(districts_clusterer)
					map.geoObjects.add(precincts_clusterer)

			)
		icons_shape			= new ymaps.shape.Polygon(new ymaps.geometry.pixel.Polygon([# TODO edit shape
			[
				[15-15, 37-36],
				[1-15, 22-36],
				[0-15, 16-36],
				[1-15, 10-36],
				[5-15, 5-36],
				[11-15, 1-36],
				[19-15, 1-36],
				[26-15, 5-36],
				[31-15, 14-36],
				[30-15, 22-36],
				[15-15, 37-36]
			]
		]))
		$.ajax(
			url			: 'api/Districts'
			type		: 'get'
			data		: null
			success		: (districts) ->
				placemarks	= []
				for district, district of districts
					placemarks.push(
						new ymaps.Placemark(
							[district.lat, district.lng]
							{
								hasBalloon	: false
								hasHint		: false
							}
							{
								iconLayout			: 'default#image'
								iconImageHref		: '/components/modules/Elections/includes/img/map-precincts.png' # TODO special district icon here
								iconImageSize		: [38, 37]
								iconImageOffset		: [-15, -36]
								iconImageClipRect	: [
									[38 * district.violations, 0],
									[38 * (district.violations + 1), 0]
								]
								iconImageShape		: icons_shape
							}
						)
					)
				districts_clusterer.add(placemarks)
			error		: ->
				console.error('Districts loading error')
		)
		$.ajax(
			url			: 'api/Precincts'
			type		: 'get'
			data		: null
			success		: (precincts) ->
				placemarks	= []
				for precinct, precinct of precincts
					placemarks.push(
						new ymaps.Placemark(
							[precinct.lat, precinct.lng]
							{
								hintContent				: "Дільниця №#{precinct.number}"
								balloonContentHeader	: "Дільниця №#{precinct.number}"
							}
							{
								iconLayout			: 'default#image'
								iconImageHref		: '/components/modules/Elections/includes/img/map-precincts.png'
								iconImageSize		: [38, 37]
								iconImageOffset		: [-15, -36]
								iconImageClipRect	: [
									[38 * precinct.violations, 0],
									[38 * (precinct.violations + 1), 0]
								]
								iconImageShape		: icons_shape
							}
						)
					)
				precincts_clusterer.add(placemarks)
			error		: ->
				console.error('Precincts loading error')
		)
		return
