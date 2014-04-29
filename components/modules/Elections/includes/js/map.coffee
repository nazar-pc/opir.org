$ ->
	if cs.module != 'Elections'
		return
	ymaps.ready ->
		placemarks	= []
		window.map				= new ymaps.Map(
			'map'
			{
				center				: [50.45, 30.523611]
				zoom				: 12
				controls			: ['typeSelector', 'zoomControl', 'fullscreenControl', 'rulerControl', 'trafficControl']
			}
			{
				avoidFractionalZoom	: false
			}
		)
		map.setBounds(
			[[44.02462975216294, 21.777120521484335], [52.82663432351663, 40.32204239648433]]
			preciseZoom	: true
		)
		clusterer				= new ymaps.Clusterer(
			gridSize	: 128
			hasBalloon	: false
			hasHint		: false
		)
		clusterer.createCluster	= (center, geoObjects) ->
			cluster	= ymaps.Clusterer.prototype.createCluster.call(this, center, geoObjects)
			cluster.options.set(
				icons	: [
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
			)
			cluster
		map.geoObjects.add(clusterer)
		icons_shape			= new ymaps.shape.Polygon(new ymaps.geometry.pixel.Polygon([
			[
				[23-24, 56-58],
				[44-24, 34-58],
				[47-24, 23-58],
				[45-24, 14-58],
				[40-24, 7-58],
				[29-24, 0-58],
				[17-24, 0-58],
				[7-24, 6-58],
				[0-24, 18-58],
				[0-24, 28-58],
				[4-24, 36-58],
				[23-24, 56-58]
			]
		]))
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
				clusterer.add(placemarks)
			error		: ->
				console.error('Precincts loading error')
		)
		return
