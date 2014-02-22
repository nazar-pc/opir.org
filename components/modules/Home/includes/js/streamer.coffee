$ ->
	if !cs.home?.reporter
		return
	modal = $("""
		<div>
			<div class="uk-form" style="width: 600px;margin-left: -300px;">
				<a class="uk-modal-close uk-close"></a>
				<p>
					<textarea class="cs-home-stream-code" placeholder="Вставте код плеєру з трансляцією (youtube чи ustream) або посилання на сторінку з трансляцією в youtube"></textarea>
				</p>
				<p>Дозвольте браузеру завжди отримувати доступ до вашого місцезнаходження, це необхідно для трансляції!</p>
				<p class="cs-right">
					<button class="uk-button cs-home-stream-code-save">Почати трансляцію</button>
				</p>
			</div>
		</div>
	""")
		.appendTo('body')
		.cs().modal('show')
		.on 'uk.modal.hide', ->
			$(this).remove()
	stream_code	= $('.cs-home-stream-code')
	if cs.home.reporter != 1
		stream_code.val(cs.home.reporter)
	$('.cs-home-stream-code-save').click ->
		$(@).html($(@).html() + ' <i class="uk-icon-spinner uk-icon-spin"></i>')
		if navigator.geolocation
			navigator.geolocation.getCurrentPosition(
				(position) ->
					$.ajax(
						url		: 'api/Home/stream'
						type	: 'put'
						data	:
							stream_code	: stream_code.val()
							lat			: position.coords.latitude
							lng			: position.coords.longitude
						success	: ->
							map.panTo([position.coords.latitude, position.coords.longitude])
							setTimeout(location_updating, 10 * 1000)
							modal.cs().modal('hide')
							map.update_events()
					)
				->
					alert 'Не вдалось отримати доступ до вашого місцеположення'
				{
					enableHighAccuracy	: true
					timeout				: 2 * 60 * 1000	#Wait for 2 minutes max
				}
			)
		else
			alert 'Потрібен доступ до вашого місцеположення, інакше трансляція не працюватиме'
	location_updating	= ->
		navigator.geolocation.getCurrentPosition(
			(position) ->
				$.ajax(
					url			: 'api/Home/stream_location'
					type		: 'put'
					data		:
						lat			: position.coords.latitude
						lng			: position.coords.longitude
					complete	: ->
						setTimeout(location_updating, 10 * 1000)
					success		: ->
						map.update_events()
				)
			->
			{
				enableHighAccuracy	: true
				timeout				: 2 * 60 * 1000	#Wait for 2 minutes max
			}
		)
	return
