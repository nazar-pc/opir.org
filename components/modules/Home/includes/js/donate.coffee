$ ->
	$('.cs-home-donate').click ->
		$("""
				<div>
					<div class="uk-form" style="width: 600px;margin-left: -300px;">
						<a class="uk-modal-close uk-close"></a>
						<h2 class="cs-center">Допомогти сайту</h2>
						<p>
							Ми піклуємось про вашу безпеку та анонімність як на сайті, так і на вулицях країни.
						</p>
						<p>
							Ми не збираємо жодної статистики про користувачів, ніде не зберігаємо вашу IP адресу чи будь-які пов’язані з вами дані.
						</p>
						<p>
							Якщо у вас є бажання та можливість підтримати функціонування та розвиток сайту - ви можете зробити це так само безпечно та анонімно.
						</p>
						<p>
							З вказаних вище міркувань на разі ми приймаємо допомогу у вигляді  <a href="https://bitcoin.org/" target="_blank"><u>Bitcoin</u></a>.
						</p>
						<p>
							Гаманець Bitcoin:<b>1F8Wq9t562hHGcXfSDK7ZUZJhUjroZhwRg</b>
						</p>
					</div>
				</div>
			""")
		.appendTo('body')
		.cs().modal('show')
		.on 'uk.modal.hide', ->
				$(this).remove()