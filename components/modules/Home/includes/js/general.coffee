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
							<button class="uk-button" onclick="cs.sign_in($('#login').val(), $('#password').val());">#{cs.Language.sign_in}</button>
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
#		if !cs.getcookie('opir_reformat')
#			cs.setcookie('opir_reformat', 1)
#			$.cs.simple_modal("""
#				<p>Оскільки нагальна потреба в координації сил самооборони відпала, сайт переорієнтовується згідно ситуації в Україні.</p>
#				<p>Основні цілі, що ставить команда opir.org перед собою - фіксування та контроль порушень під час процесу волевиявлення громадян на найближчих виборах, а саме: вибори Призидента, Парламенту та Мера Києва.</p>
#				<p>Opir.org стане інструментом спостерігачів від різних партій та громадських організацій. Процес буде проходити наступним чином: спостерігач зніматиме все що відбувається на власний телефон з за допомогою ustream або youtube і транслюватиме це на сайт. В разі фіксування порушення запис зберігатиметься на окремих серверах для подальшого аналізу. Порушення фіксуватиметься на карті та підсвічуватиметься червоним маркером. В разі потреби на місце виїжджатимуть групи кваліфікованих юристів та журналістів для надання допомоги спостерігачам.</p>
#				<p>Скоро оновлений opir.org ви побачите на своїх комп`ютерах а також зожете завантажити в Android Play Market та iOS App Store.</p>
#			""")
	else
		# Sign out
		$('.cs-home-sign-out').click ->
			cs.sign_out()
