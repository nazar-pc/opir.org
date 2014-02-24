$ ->
	panel				= $('.cs-stream-add-panel')
	coords				= [0, 0]
	event_coords		= null
	put_events_coords	= false
	map_cursor			= null
	address_timeout		= 0
	reset_options		= ->
		# Reset
		coords				= [0, 0]
		event_coords && map.geoObjects.remove(event_coords)
		event_coords		= null
		map_cursor && map_cursor.remove()
		map_cursor			= null
		put_events_coords	= false
	$(document).on(
		'click'
		'.cs-stream-add, .cs-stream-add-close'
		->
			reset_options()
			panel
				.html('')
				.toggle('fast', ->
					if panel.css('display') != 'none'
						if $('.cs-home-settings-panel').css('width') != '0px'
							$('.cs-home-settings').click()
						content		= """
							<h2>Додати камеру</h2>
							<textarea placeholder="Код з ustream.tv"></textarea>
							<p class="help">при виникненні питань звертайтесь за телефоном +38 093 01 222 11</p>
							<input type="text" class="cs-stream-add-location-address" placeholder="Адреса або точка на карті">
							<button class="cs-stream-add-location uk-icon-location-arrow"></button>
							<p class="help">не забувайте вказувати місто для точного пошуку, і перевіряйте де помістилась точка</p>
							<div>
								<button class="cs-stream-add-close uk-icon-times"></button>
								<button class="cs-stream-add-process">Додати</button>
							</div>
						"""
						panel.html(content)
						put_events_coords	= true
						map_cursor			= map.cursors.push('pointer')
						panel.html(content)
				)
	)
	add_stream_coords	= (point) ->
		coords			= point
		event_coords && map.geoObjects.remove(event_coords)
		event_coords	= new ymaps.Placemark coords, {}, {
			draggable			: true
			iconLayout			: 'default#image'
			iconImageHref		: '/components/modules/Home/includes/img/new-event.png'
			iconImageSize		: [91, 86]
			iconImageOffset		: [-36, -86]
			iconImageShape		: new ymaps.shape.Polygon(new ymaps.geometry.pixel.Polygon([
				[
					[35-36, 85-86],
					[65-36, 55-86],
					[71-36, 43-86],
					[72-36, 31-86],
					[64-36, 13-86],
					[53-36, 4-86],
					[37-36, 0-86],
					[22-36, 2-86],
					[11-36, 10-86],
					[3-36, 22-86],
					[0-36, 35-86],
					[3-36, 51-86],
					[35-36, 85-86]
				]
			]))
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
				add_stream_coords(e.get('coords'))
			)
			return
		), 100
		return
	panel
		.on(
			'click'
			'.cs-stream-add-location'
			->
				alert 'Клікніть місце на карті де розташована камера'
		)
		.on(
			'click'
			'.cs-stream-add-process'
			->
				stream_code	= panel.find('textarea').val()
				if stream_code && coords[0] && coords[1]
					ymaps.geocode(
						coords
						json	: true
						results	: 1
					).then (res) ->
						address_details	= res.GeoObjectCollection.featureMember
						if address_details.length
							address_details	= address_details[0].GeoObject.metaDataProperty.GeocoderMetaData.text
						else
							address_details	= ''
						$.ajax(
							url		: 'api/Streams/streams'
							type	: 'post'
							data	:
								lat				: coords[0]
								lng				: coords[1]
								stream_code		: stream_code
								address_details	: address_details
							success	: ->
								panel.hide('fast')
								map.geoObjects.remove(event_coords)
								event_coords		= null
								put_events_coords	= false
								map_cursor.remove()
								alert 'Камеру успішно додано, дякуємо вам! Після перевірки вона з’явиться на карті.'
						)
				else if !stream_code
					alert 'Код стріму з ustream обов’язковий'
				else
					alert 'Вкажіть місце на карті де розташована камера'
		)
		.on(
			'keyup change'
			'.cs-stream-add-location-address'
			->
				$this	= $(@)
				if $this.val().length < 4
					return
				clearTimeout(address_timeout)
				address_timeout	= setTimeout (->
					ymaps.geocode($this.val()).then (res) ->
						coords	= res.geoObjects.get(0).geometry.getCoordinates()
						map.panTo(
							coords
							fly				: true
							checkZoomRange	: true
						).then ->
							map.zoomRange.get(coords).then (zoomRange) ->
								map.setZoom(
									zoomRange[1],
									duration	: 500
								)
						add_stream_coords(coords)
				), 300
		)
