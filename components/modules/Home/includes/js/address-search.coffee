$ ->
	address_timeout	= 0
	$('.cs-home-address-search').on(
		'keyup change'
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
					add_event_coords(coords)
			), 300
	)
