$ ->
	if cs.module != 'Streams'
		return
	ymaps.ready ->
		window.map				= new ymaps.Map 'map', {
			center				: [50.45, 30.523611]
			zoom				: 13
			controls			: ['typeSelector', 'zoomControl', 'fullscreenControl']
		}
		map.setBounds(
			[[44.02462975216294, 21.777120521484335], [52.82663432351663, 40.32204239648433]]
			preciseZoom	: true
		)
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
					if stream.tags.indexOf(String(tag)) > -1
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
		streams_list	= $('.cs-stream-list')
		map.add_streams_on_map	= (streams) ->
			streams			= filter_streams(streams || streams_cache)
			placemarks		= []
			list_content	= ''
			for stream, stream of streams
				stream_url	= stream.stream_url
				if /youtube/.test(stream_url)
					stream_url	+= '?wmode=transparent'
				placemarks.push(
					new ymaps.Placemark(
						[stream.lat, stream.lng]
						{
							stream_id				: stream.id
							stream_url				: stream_url
							balloonContentBody		: """
								<p><iframe width="400" height="240" src="#{stream_url}" frameborder="0" scrolling="no"></iframe></p>
								<div class="cs-streams-social-links" data-id="#{stream.id}">
									<a class="fb uk-icon-facebook"></a>
									<a class="vk uk-icon-vk"></a>
									<a class="tw uk-icon-twitter"></a>
								</div>
								<button onclick="cs.streams.commenting(#{stream.id})" class="uk-icon-comment" data-uk-tooltip title="Коментувати"></button>
							"""
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
				list_content	+= """<iframe src="#{stream.stream_url}" frameborder="0" scrolling="no"></iframe>"""
			clusterer.removeAll()
			clusterer.add(placemarks)
			streams_list.html(list_content)
			open_modal_commenting()
		$.ajax(
			url			: 'api/Streams/streams'
			type		: 'get'
			success		: (streams) ->
				streams_cache	= streams
				map.add_streams_on_map(streams)
				return
			error		: ->
		)
		cs.streams				= {}
		cs.streams.commenting	= (id) ->
			history.pushState(null, null, "Streams/#{id}")
			open_modal_commenting()
		modal_opened_once	= false
		window.addEventListener(
			'popstate'
			->
				if !modal_opened_once
					return false
				return open_modal_commenting()
		)
		open_modal_commenting	= ->
			modal_opened_once	= true
			if /\/Streams\/[0-9]+/.test(location.pathname)
				id							= parseInt(location.pathname.substr(9))
				window.disqus_shortname		= 'opirorg'
				window.disqus_identifier	= 'Streams/' + id
				for i in placemarks
					if parseInt(i.properties.get('stream_id')) == id
						placemark	= i
						break
				if !placemark
					$.cs.simple_modal(
						'<h3 class="cs-center">Стрім не знайдено</h3>'
						false
						400
					)
					return
				map.balloon.close()
				state		= clusterer.getObjectState(placemark)
				if state.isClustered
					state.cluster.state.set('activeObject', placemark)
					state.cluster.events.fire('click')
				else
					placemark.balloon.open()
				stream_url	= placemark.properties.get('stream_url')
				modal		= $.cs.simple_modal(
					"""
						<p><iframe width="700" height="420" src="#{stream_url}" frameborder="0" scrolling="no" style="display : block; margin : 0 auto;"></iframe></p>
						<div class="cs-streams-social-links" data-id="#{id}">
							<a class="fb uk-icon-facebook"></a>
							<a class="vk uk-icon-vk"></a>
							<a class="tw uk-icon-twitter"></a>
						</div>
						<div id="disqus_thread"></div>
					"""
					true
					800
				)
				init_disqus()
				modal.on(
					'uk.modal.hide'
					->
						history.pushState(null, null, 'Streams')
				)
				return false
			else
				return true
		return
