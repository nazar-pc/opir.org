$ ->
	panel	= $('.cs-home-add-panel')
	urgency	= 'unknown'
	actual	= 15 * 60	#15 minutes by default
	$(document).on(
		'click'
		'.cs-home-add, .cs-home-add-close'
		->
			panel
				.html('')
				.toggle('fast', ->
					if panel.css('display') != 'none'
						content	= ''
						for category, category of cs.home.categories
							content	+= """
								<li data-id="#{category.id}">
									<img src="/components/modules/Home/includes/img/#{category.id}.png" alt="">
									<span>#{category.name}</span>
								</li>
							"""
						panel.html("<ul>#{content}</ul>")
				)
	)
	panel
		.on(
			'click'
			'> ul > li'
			->
				id		= $(@).data('id')
				name	= $(@).find('span').text()
				content	= """
					<h2 data-id="#{id}">#{name}</h2>
					<textarea placeholder="Коментар"></textarea>
					<div data-uk-dropdown="{mode:'click'}" class="uk-button-dropdown">
						<button type="button" class="uk-button">
							<span class="uk-icon-caret-down"></span> <span>Терміновість не вказано</span>
						</button>
						<div class="uk-dropdown">
							<ul class="cs-home-add-urgency uk-nav uk-nav-dropdown">
								<li data-id="unknown">
									<a>Терміновість не вказано</a>
								</li>
								<li data-id="can-wait">
									<a>Може почекати</a>
								</li>
								<li data-id="urgent">
									<a>Терміново</a>
								</li>
							</ul>
						</div>
					</div>
					<h3>Актуальність</h3>
					<div>
						<input class="cs-home-add-time-interval" type="number" min="1" value="15"/>
						<div data-uk-dropdown="{mode:'click'}" class="uk-button-dropdown">
							<button type="button" class="uk-button">
								<span class="uk-icon-caret-down"></span> <span>Хвилин</span>
							</button>
							<div class="uk-dropdown">
								<ul class="cs-home-add-time-interval uk-nav uk-nav-dropdown">
									<li data-id="minutes">
										<a>Хвилин</a>
									</li>
									<li data-id="hours">
										<a>Годин</a>
									</li>
									<li data-id="days">
										<a>Днів</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<div class="cs-home-add-location">
						<span>Вказати на карті</span>
					</div>
					<div>
						<button class="cs-home-add-close"></button>
						<button class="cs-home-add-process">Додати</button>
					</div>
				"""
				panel.html(content)
		)
