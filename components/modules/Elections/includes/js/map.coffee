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
					cs.setcookie('coordinates', JSON.stringify(user_location))
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
					if previous_zoom > 14
						setTimeout(add_precincts_on_map, 0)
					return
				previous_zoom	= e.get('newZoom')
				if previous_zoom < 14
					map.geoObjects.remove(precincts_clusterer)
					map.geoObjects.add(districts_clusterer)
				else
					map.geoObjects.remove(districts_clusterer)
					map.geoObjects.add(precincts_clusterer)

			)
		districts_icons_shape	= new ymaps.shape.Polygon(new ymaps.geometry.pixel.Polygon([
			[
				[0-81, 32-82],
				[11-81, 11-82],
				[31-81, 0-82],
				[47-81, 0-82],
				[68-81, 11-82],
				[79-81, 32-82],
				[78-81, 49-82],
				[67-81, 67-82],
				[52-81, 77-82],
				[31-81, 78-82],
				[11-81, 67-82],
				[0-81, 48-82],
				[0-81, 32-82]
			]
		]))
		precincts_icons_shape	= new ymaps.shape.Polygon(new ymaps.geometry.pixel.Polygon([
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
		precincts			= []
		filter_precincts	= (precincts) ->
			bounds	= map.getBounds()
			precincts.filter (precinct) ->
				parseFloat(precinct.lat) > bounds[0][0] && parseFloat(precinct.lat) < bounds[1][0] &&
				parseFloat(precinct.lng) > bounds[0][1] && parseFloat(precinct.lng) < bounds[1][1]
		add_precincts_on_map	= ->
			placemarks	= []
			for precinct, precinct of filter_precincts(precincts)
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
							iconImageShape		: precincts_icons_shape
						}
					)
				)
			precincts_clusterer.removeAll()
			precincts_clusterer.add(placemarks)
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
								iconContent	: '<div class="cs-elections-map-district-placemark-content'+(if parseInt(district.violations) then ' violations' else '')+'">'+cs.Language.district_map_content(district.district)+'</div>'
							}
							{
								iconLayout			: 'default#imageWithContent'
								iconImageHref		: '/components/modules/Elections/includes/img/map-districts.png'
								iconImageSize		: [81, 82]
								iconImageOffset		: [-40, -41]
								iconImageClipRect	: [
									[81 * district.violations, 0],
									[81 * (district.violations + 1), 0]
								]
								iconImageShape		: districts_icons_shape
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
			success		: (precincts_loaded) ->
				precincts	= precincts_loaded
				add_precincts_on_map()
			error		: ->
				console.error('Precincts loading error')
		)
		return
