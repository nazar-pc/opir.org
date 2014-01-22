$ ->
	if cs.is_guest
		# Sign in
		$('.cs-home-sign-in').click ->
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
		# Sign out
		$('.cs-home-sign-out').click ->
			cs.sign_out()
