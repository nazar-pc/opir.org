$ ->
	$('#map').css(
		height	: $(window).height() - 62
		width	: $(window).width()
	)
	ymaps.ready ->
		window.map	= new ymaps.Map 'map', {
			center		: [50.45, 30.523611]
			zoom		: 13
			controls	: ['typeSelector', 'zoomControl']
		}
		if cs.is_user
			# Add event
			add_event	= new ymaps.control.Button(
				data	:
					content	: 'Додати подію',
				options :
					maxWidth		: 200
					selectOnClick	: false
			);
			add_event.events
				.add('click', ->
					$("""
						<div>
							<div class="uk-form" style="width: 600px;margin-left: -300px;">
								<a class="uk-modal-close uk-close"></a>
								<p>
									<input type="text" id="login" placeholder="#{cs.Language.login}" autofocus>
								</p>
								<p>
									<input type="password" id="password" placeholder="#{cs.Language.password}">
								</p>
								<p class="cs-right">
									<button class="uk-button" onclick="cs.sign_in($('#login').val(), $('#password').val());" class="uk-button">#{cs.Language.sign_in}</button>
								</p>
							</div>
						</div>
					""")
						.appendTo('body')
						.cs().modal('show')
						.on 'uk.modal.hide', ->
							$(this).remove()
				)
			map.controls.add(add_event)
	window.cs.home	=
		add_event	: (coords) ->
