$ ->
	ymaps.ready ->
		refresh_delay		= if cs.home.automaidan_coord then 5 else 60
		streaming_opened	= false
		stop_updating		= false
		add_zero		= (input) ->
			if input < 10 then '0' + input else input
		placemarks	= []
		window.map				= new ymaps.Map 'map', {
			center				: [50.45, 30.523611]
			zoom				: 13
			controls			: ['typeSelector', 'zoomControl']
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
		filter_events		= (events) ->
			categories	= $('.cs-home-filter-category .active')
			events.filter (event) ->
				!categories.length || categories.filter("[data-id=#{event.category}]").length
		events_stream_panel	= $('.cs-home-events-stream-panel')
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
		add_events_on_map	= (events) ->
			if stop_updating
				return
			events						= filter_events(events)
			placemarks					= []
			events_stream_panel_content	= ''
			for event, event of events
				if streaming_opened
					if streaming_opened.unique_id == event.id
						old_pixel_coords	= map.options.get('projection').fromGlobalPixels(
							streaming_opened.geometry.getCoordinates()
							map.getZoom()
						)
						new_pixel_coords	= map.options.get('projection').fromGlobalPixels(
							[event.lat, event.lng]
							map.getZoom()
						)
						$('.ymaps-balloon').animate(
							left	: '+=' + (new_pixel_coords[0] - old_pixel_coords[0])
							top		: '+=' + (new_pixel_coords[1] - old_pixel_coords[1])
						)
						streaming_opened.geometry.setCoordinates([event.lat, event.lng])
						bounds	= map.getBounds()
						map.panTo([parseFloat(event.lat) - (bounds[0][0] - bounds[1][0]) / 4, parseFloat(event.lng)])
						return
					continue
				category_name	= cs.home.categories[event.category].name
				t				= new Date(event.timeout * 1000)
				timeout			=
					add_zero(t.getHours()) + ':' + add_zero(t.getMinutes()) + ' ' +
					add_zero(t.getDate()) + '.' + add_zero(t.getMonth() + 1) + '.' + t.getFullYear()
				timeout			= if event.timeout > 0 then "<time>Актуально до #{timeout}</time>" else ''
				a				= new Date(event.added * 1000)
				added			=
					add_zero(a.getHours()) + ':' + add_zero(a.getMinutes()) + ' ' +
					add_zero(a.getDate()) + '.' + add_zero(a.getMonth() + 1) + '.' + a.getFullYear()
				added			= "<time>Додано #{added}</time>"
				text			= event.text.replace(/\n/g, '<br>')
				is_streaming	= false
				if text && text.substr(0, 7) == 'stream:'
					timeout			= ''
					is_streaming	= true
					text			= text.substr(7)
					text			= """<p><iframe width="260" height="240" src="#{text}" frameborder="0" scrolling="no"></iframe></p>"""
				else
					text			= if text then """<p>#{text}</p>""" else ''
				img				= if event.img then """<p><img height="240" width="260" src="#{event.img}" alt=""></p>""" else ''
				event.confirmed	= parseInt(event.confirmed)
				placemarks.push(
					new ymaps.Placemark(
						[event.lat, event.lng]
						{
							hintContent				: category_name
							balloonContentHeader	: category_name
							balloonContentBody		: """
								#{added}
								#{timeout}
								#{img}
								#{text}
							"""
							balloonContentFooter	: balloon_footer(event, is_streaming)
						}
						{
							iconLayout			: 'default#image'
							iconImageHref		: '/components/modules/Home/includes/img/events.png'
							iconImageSize		: [59, 56]
							iconImageOffset		: [-24, -56]
							iconImageClipRect	: [[59 * (1 - event.confirmed), 56 * (event.category - 1)], [59 * (2 - event.confirmed), 56 * event.category]]
							iconImageShape		: icons_shape
						}
					)
				)
				placemark_id				= placemarks.length - 1
				events_stream_panel_content	+= """
					<li data-location="#{event.lat},#{event.lng}" data-placemark="#{placemark_id}">
						<img src="/components/modules/Home/includes/img/#{event.category}.png" alt="">
						<h2>#{category_name}</span></h2>
						<br>
						#{added}
						#{timeout}
						#{img}
						#{text}
					</li>
				"""
				if is_streaming
					do (event = event) ->
						placemark			= placemarks[placemarks.length - 1]
						placemark.unique_id	= event.id
						placemark.balloon.events
							.add('open', ->
								streaming_opened	= placemark
								refresh_delay		= 10
								map.update_events()
							)
							.add('close', ->
								streaming_opened	= false
								refresh_delay		= 60
								map.update_events(true)
							)
						return
				else
					do (event = event) ->
						placemark	= placemarks[placemarks.length - 1]
						placemark.balloon.events
							.add('open', ->
								stop_updating	= true
								return
							)
							.add('close', ->
								stop_updating	= false
								#map.update_events(true)
								return
							)
						return
			events_stream_panel.html("<h2>Ефір подій</h2><ul>#{events_stream_panel_content}</ul>")
			clusterer.removeAll()
			clusterer.add(placemarks)
		balloon_footer	= (event, is_streaming) ->
			if cs.home.automaidan_coord
				if !parseInt(event.assigned_to) then """<button class="cs-home-check-assign" data-id="#{event.id}">Відправити водія для перевірки</button>""" else ''
			else if !cs.home.automaidan && event.user && !is_streaming
				confirmation	= if !event.confirmed then """<button class="cs-home-check-confirm" data-id="#{event.id}">Підтвердити подію</button>""" else ''
				"""#{confirmation}<button class="cs-home-edit" data-id="#{event.id}">Редагувати</button> <button onclick="cs.home.delete_event(#{event.id})">Видалити</button>"""
			else
				''
		map.update_events		= (from_cache = false) ->
			if from_cache && map.update_events.cache
				add_events_on_map(map.update_events.cache)
				setTimeout(map.update_events, refresh_delay * 1000)
			else
				$.ajax(
					url			: 'api/Home/events'
					type		: 'get'
					complete	: ->
						setTimeout(map.update_events, refresh_delay * 1000)
					success		: (events) ->
						map.update_events.cache	= events
						add_events_on_map(events)
						return
				)
			return
		map.update_events()
		cs.home.delete_event	= (id) ->
			if !confirm('Точно видалити?')
				return
			$.ajax(
				url			: "api/Home/events/#{id}"
				type		: 'delete'
				success		: ->
					map.update_events()
					return
			)
			return
		focus_map_timer	= 0
		events_stream_panel
			.on(
				'mousemove'
				'li'
				->
					$this	= $(@)
					clearTimeout(focus_map_timer)
					focus_map_timer = setTimeout (->
						location	= $this.data('location').split(',')
						location	= [parseFloat(location[0]), parseFloat(location[1])]
						map.panTo(location).then ->
							map.zoomRange.get(location).then (zoomRange) ->
								map.setZoom(
									zoomRange[1],
									duration	: 500
								)
					), 500
			)
			.on(
				'mouseleave'
				'li'
				->
					clearTimeout(focus_map_timer)
			)
			.on(
				'click'
				'li'
				->
					placemark	= placemarks[$(@).data('placemark')]
					state		= clusterer.getObjectState(placemark)
					if state.isClustered
						state.cluster.state.set('activeObject', placemark)
						state.cluster.events.fire('click')
					else
						placemark.balloon.open()
			)
		if !cs.home.automaidan
			$('#map')
				.on(
					'click'
					'.cs-home-check-confirm'
					->
						$.ajax(
							url			: 'api/Home/events/' + $(@).data('id') + '/check'
							type		: 'put'
							success		: ->
								map.update_events()
								map.balloon.close()
								alert 'Підтвердження отримано, дякуємо вам!'
						)
				)
