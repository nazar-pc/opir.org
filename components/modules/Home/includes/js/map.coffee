$ ->
	ymaps.ready ->
		refresh_delay		= 60
		streaming_opened	= false
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
			category	= $('.cs-home-filter-category .active').data('id')
			urgency	= $('.cs-home-filter-urgency .active').data('id')
			events.filter (event) ->
				(!category || category == parseInt(event.category)) && (!urgency || urgency == event.urgency)
		add_events_on_map	= (events) ->
			events		= filter_events(events)
			placemarks	= []
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
				time			=
					add_zero(t.getHours()) + ':' + add_zero(t.getMinutes()) + ' ' +
					add_zero(t.getDate()) + '.' + add_zero(t.getMonth() + 1) + '.' + t.getFullYear()
				urgency			= switch event.urgency
					when 'unknown' then 0
					when 'can-wait' then 1
					when 'urgent' then 2
				time			= if urgency == 0 then '' else "<time>Актуально до #{time}</time>"
				text			= event.text.replace(/\n/g, '<br>')
				is_streaming	= false
				if text && text.substr(0, 7) == 'stream:'
					time			= ''
					is_streaming	= true
					text			= text.substr(7)
					text			= """<p><iframe width="260" height="240" src="#{text}" frameborder="0" scrolling="no"></iframe></p>"""
				else
					text			= if text then """<p>#{text}</p>""" else ''
				img				= if event.img then """<p><img height="240" width="260" src="#{event.img}" alt=""></p>""" else ''
				placemarks.push(
					new ymaps.Placemark(
						[event.lat, event.lng]
						{
							hintContent				: category_name
							balloonContentHeader	: category_name
							balloonContentBody		: """
								#{time}
								#{img}
								#{text}
							"""
							balloonContentFooter	: if event.user && !is_streaming then """<button class="cs-home-edit" data-id="#{event.id}">Редагувати</button> <button onclick="cs.home.delete_event(#{event.id})">Видалити</button>""" else ''
						}
						{
							iconLayout			: 'default#image'
							iconImageHref		: '/components/modules/Home/includes/img/events.png'
							iconImageSize		: [59, 56]
							iconImageOffset		: [-24, -56]
							iconImageClipRect	: [[59 * urgency, 56 * (event.category - 1)], [59 * (urgency + 1), 56 * event.category]]
						}
					)
				)
				if is_streaming
					do (event = event) ->
						placemark				= placemarks[placemarks.length - 1]
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
							)
						return
			clusterer.removeAll()
			clusterer.add(placemarks)
		update_events_timeout	= 0
		map.update_events		= (from_cache = false) ->
			clearTimeout(update_events_timeout)
			if from_cache && map.update_events.cache
				add_events_on_map(map.update_events.cache)
				update_events_timeout	= setTimeout(map.update_events, refresh_delay * 1000)
			else
				$.ajax(
					url			: 'api/Home/events'
					type		: 'get'
					complete	: ->
						update_events_timeout	= setTimeout(map.update_events, refresh_delay * 1000)
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
