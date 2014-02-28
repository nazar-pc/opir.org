$ ->
	if cs.module != 'Streams'
		return
	ymaps.ready ->
		window.map				= new ymaps.Map 'map', {
			center				: [50.45, 30.523611]
			zoom				: 13
			controls			: ['typeSelector', 'zoomControl', 'fullscreenControl']
		}
		clusterer				= new ymaps.Clusterer()
		clusterer.createCluster	= (center, geoObjects) ->
			cluster	= ymaps.Clusterer.prototype.createCluster.call(this, center, geoObjects)
			cluster.options.set(
				icons	: [
					{
						href	: '/components/modules/Home/includes/img/cluster-46.png'
						size	: [46, 46]
						offset	: [-23, -23]
					}
					{
						href	: '/components/modules/Home/includes/img/cluster-58.png'
						size	: [58, 58]
						offset	: [-27, -27]
					}
				]
			)
			cluster
		map.geoObjects.add(clusterer)
		filter_streams		= (streams) ->
			tags	= $('.cs-stream-added-tags [data-id]')
			if !tags.length
				return streams
			tags	= tags
				.map ->
					$(@).data('id')
				.get()
			streams.filter (stream) ->
				for tag in tags
					if stream.tags.indexOf(tag) == -1
						return true
				return false
		placemarks			= []
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
		streams_cache	= []
		map.add_streams_on_map	= (streams) ->
			streams						= filter_streams(streams || streams_cache)
			placemarks					= []
			for stream, stream of streams
				placemarks.push(
					new ymaps.Placemark(
						[stream.lat, stream.lng]
						{
							balloonContentBody		: """<p><iframe width="400" height="240" src="#{stream.stream_url}" frameborder="0" scrolling="no"></iframe></p>"""
						}
						{
							hasHint				: false
							iconLayout			: 'default#image'
							iconImageHref		: '/components/modules/Home/includes/img/events.png'
							iconImageSize		: [59, 56]
							iconImageOffset		: [-24, -56]
							iconImageClipRect	: [[0, 56 * (28 - 1)], [59, 56 * 28]]
							iconImageShape		: icons_shape
						}
					)
				)
			clusterer.removeAll()
			clusterer.add(placemarks)
		$.ajax(
			url			: 'api/Streams/streams'
			type		: 'get'
			success		: (streams) ->
				streams_cache	= streams
				map.add_streams_on_map(streams)
				return
			error		: ->
		)
		return
