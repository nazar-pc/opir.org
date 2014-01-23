$ ->
	add_zero	= (input) ->
		if input < 10 then '0' + input else input
	ymaps.ready ->
		window.map	= new ymaps.Map 'map', {
			center		: [50.45, 30.523611]
			zoom		: 13
			controls	: ['typeSelector', 'zoomControl']
			type		: 'yandex#hybrid'
		}
		categories	= []
		do ->
			for category, category of cs.home.categories
				categories[category.id]	= category.name
			return
		clusterer	= new ymaps.Clusterer
		map.geoObjects.add(clusterer)
		filter_events		= (events) ->
			category	= $('.cs-home-filter-category').data('id')
			urgency	= $('.cs-home-filter-urgency').data('id')
			events.filter (event) ->
				(!category || category == parseInt(event.category)) && (urgency == 'any' || urgency == event.urgency)
		add_events_on_map	= (events) ->
			events		= filter_events(events)
			placemarks	= []
			for event, event of events
				category_name	= categories[event.category]
				t				= new Date(event.timeout * 1000)
				time			=
					add_zero(t.getHours()) + ':' + add_zero(t.getMinutes()) + ' ' +
					add_zero(t.getDate()) + '.' + add_zero(t.getMonth() + 1) + '.' + t.getFullYear()
				urgency			= switch event.urgency
					when 'unknown' then 0
					when 'can-wait' then 1
					when 'urgent' then 2
				placemarks.push(
					new ymaps.Placemark(
						[event.lat, event.lng]
						{
							hintContent	: category_name
							balloonContentHeader	: category_name
							balloonContentBody		: """
								<time>Актуально до #{time}</time>
								<p>#{event.text}</p>
							"""
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
			clusterer.removeAll()
			clusterer.add(placemarks)
		map.update_events	=	(from_cache = false) ->
			if from_cache && map.update_events.cache
				add_events_on_map(map.update_events.cache)
			else
				$.ajax(
					url		: 'api/Home/events'
					type	: 'get'
					success	: (events) ->
						map.update_events.cache	= events
						add_events_on_map(events)
						return
				)
			return
		map.update_events()
