$ ->
	$('.cs-home-donate').click ->
		$("""
				<div>
					<div class="uk-form" style="width: 600px;margin-left: -300px;">
						<a class="uk-modal-close uk-close"></a>
						<h2 class="cs-center">Допомогти сайту</h2>
						<p>
							opir.org - громадська організація, що здійснює фільтрацію та аналіз інформації, інформування населення, координацію груп самооборони та активістів в містах і селищах.
						</p>
						<p>
							На базі opir.org запускається проект "Очі України" - найбільша мережа відео спостереження, створена на суспільних засадах і гаряча телефонна лінія для повідомлень про корупційні та злочинні дії чиновників.
						</p>
						<p>
							Проект являєтсья соціальним та не комерційним тож ми потребуємо вашої допомоги.
						</p>
						<p>
							Гаманець Bitcoin:<b>1F8Wq9t562hHGcXfSDK7ZUZJhUjroZhwRg</b>
						</p>
						<p>
							Гаманець Яндекс.Деньги: <b>41001991014032</b>
						</p>
						<p>
							Картка Надра-Банку:<br>МФО - 380764<br>ЕДРПОУ - 20025456<br>р/р - 29245270002773<br>Номер картки - 81593665
						</p>
						<p>
							Картка ПриватБанку: 5211 5374 5147 4410
						</p>
						<p>
							Картка ПриватБанку: 5168 7572 3196 6583
						</p>
						<p>
							Для чого:
							<ul>
								<li>Оплата мобільного зв’язку</li>
								<li>Закупівля необхідної техніки</li>
								<li>Оплата серверів</li>
							</ul>
						</p>
					</div>
				</div>
			""")
		.appendTo('body')
		.cs().modal('show')
		.on 'uk.modal.hide', ->
				$(this).remove()
