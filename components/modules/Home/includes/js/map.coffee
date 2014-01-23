$ ->
#	$('#map').css(
#		height	: $(window).height() - 62
#		width	: $(window).width()
#	)
	ymaps.ready ->
		window.map	= new ymaps.Map 'map', {
			center		: [50.45, 30.523611]
			zoom		: 13
			controls	: ['typeSelector', 'zoomControl']
		}
