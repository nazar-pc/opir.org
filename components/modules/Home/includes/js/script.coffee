$ ->
	$('#map').css(
		height	: $(window).height()
		width	: $(window).width()
	)
	ymaps.ready ->
		window.map	= new ymaps.Map 'map', {
			center		: [50.45, 30.523611]
			zoom		: 13
			controls	: ['typeSelector', 'zoomControl']
		}
		if cs.is_guest
			# Sign in
			sign_in		= new ymaps.control.Button(
				data	:
					content	: 'Увійти',
				options :
					maxWidth		: 200
					selectOnClick	: false
			);
			sign_in.events
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
			map.controls.add(sign_in)
			$(document).on(
				'keyup'
				'#login, #password'
				(event) ->
					if event.which == 13
						$(this).parent().parent()
							.find('button')
								.click()
			)
		else
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
			# Sign out
			sign_out	= new ymaps.control.Button(
				data	:
					content	: 'Вийти',
				options :
					maxWidth		: 200
					selectOnClick	: false
			);
			sign_out.events
				.add('click', ->
					cs.sign_out()
				)
			map.controls.add(sign_out)
	window.cs.home	=
		add_event	: (coords) ->
