<?php
/**
 * @package        Info
 * @category       modules
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
 */
namespace cs\modules\Info;

use cs\Language;

class Info {
	/**
	 * Get info
	 *
	 * @return string
	 */
	static function get () {
		switch (Language::instance()->clang) {
			case 'uk':
				return '
					<p><strong>Всеукраїнське Громадське Об’єднання OPIR.ORG</strong> — політично й фінансово незалежна громадська організація, що почала свою діяльність в найтрагічніші дні Української Революції 2013-2014 років за ініціативою групи молодих громадських активістів.</p>
					<p>Діяльність нашої організації направлена на створення <strong>інноваційних інструментів</strong> для вирішення суспільно-важливих проблем</p>
					<p>Ми розраховуємо на набуту Україною <strong>громадську відповідальність та ініціативність</strong> для використання таких інструментів для вирішення поставлених задач та <strong>проблем</strong></p>
					<h2 class="cs-center">Очі України</h2>
					<p>Проект в пілотному режимі працював  на Президентських виборах 25.05.2014 і розрахований на циклічну реалізацію під час кожних наступних парламентських та президентських виборів.</p>
					<p><strong>Мета:</strong> забезпечення освітлення виборчого процесу, в разі виявлення значних фальсифікацій — використання доказів для анулювання результатів та за можливості перешкоджання порушенням через оперативне втручання.</p>
					<p><strong>Суть:</strong> можливість оперативного фіксування порушень та введення он-лайн відео-стрім спостереження з виборчих дільниць за допомогою мобільних додатків. Моніторинг таких порушень на геолокаційному веб-сервісі з відповідним організованим реагуванням громади в складі мобільних груп.</p>
					<h2 class="cs-center">Контакти</h2>
					<p>Центральний офіс ВГО OPIR.ORG:</p>
					<p>м. Київ, вул. Костянтинівська, 25</p>
					<p><a href="mailto:info@opir.org">info@opir.org</a></p>
					<p>+380 93 01 222 11</p>
					<h2 class="cs-center">Партнери</h2>
					<p style="display:flex;display:-webkit-flex;justify-content:space-around;text-align:center;">
						<a href="http://www.hromadske.tv/">
							<img src="/components/modules/Info/includes/img/1.png" alt=""/>
						</a>
						<a href="http://www.cvu.org.ua/">
							<img src="/components/modules/Info/includes/img/2.png" alt=""/>
						</a>
						<a href="http://chesno.org/">
							<img src="/components/modules/Info/includes/img/3.png" alt=""/>
						</a>
						<a href="http://gpp.kiev.ua/">
							<img src="/components/modules/Info/includes/img/4.png" alt=""/>
						</a>
					</p>
				';
			case 'ru':
				return '
					<p><strong>Общественная Организация OPIR.ORG</strong> — политически и финансово независимая общественная организация, начавшая свою деятельность в самые трагические дни Украинской Революции 2013-2014 годов по инициативе группы молодых общественных активистов.</p>
					<p>Деятельность нашей организации направлена на создание <strong>инновационных инструментов</strong> для решения общественно важных проблем</p>
					<p>Мы полагаемся на возросшую в Украине <strong>гражданскую ответственность и инициативность</strong> для использования таких инструментов с целью решения поставленных задач и <strong>проблем</strong></p>
					<h2 class="cs-center">Глаза Украины</h2>
					<p>Проект в пилотном режиме прошел во время президентских выборов 25.05.2014 и рассчитан на циклическую реализацию во время каждых следующих парламентских и президентских выборов.</p>
					<p><strong>Цель:</strong> обеспечение освещения избирательного процесса, в случае выявления значительных фальсификаций - использование доказательств для аннулирования результатов и по возможности препятствования нарушением через оперативное вмешательство.</p>
					<p><strong>Суть:</strong> возможность оперативного фиксирования нарушений и введение онлайн видео-стрим наблюдения с избирательных участков с помощью мобильных приложений. Мониторинг таких нарушений на геолокационном веб-сервисе с соответствующим организованным реагированием общества в составе мобильных групп.</p>
					<h2 class="cs-center">Контакты</h2>
					<p>Центральный офис ОО OPIR.ORG:</p>
					<p>г. Киев, ул . Константиновская, 25</p>
					<p><a href="mailto:info@opir.org">info@opir.org</a></p>
					<p>+380 93 01 222 11</p>
					<h2 class="cs-center">Партнеры</h2>
					<p style="display:flex;display:-webkit-flex;justify-content:space-around;text-align:center;">
						<a href="http://www.hromadske.tv/">
							<img src="/components/modules/Info/includes/img/1.png" alt=""/>
						</a>
						<a href="http://www.cvu.org.ua/">
							<img src="/components/modules/Info/includes/img/2.png" alt=""/>
						</a>
						<a href="http://chesno.org/">
							<img src="/components/modules/Info/includes/img/3.png" alt=""/>
						</a>
						<a href="http://gpp.kiev.ua/">
							<img src="/components/modules/Info/includes/img/4.png" alt=""/>
						</a>
					</p>
				';
			case 'en':
			default:
				return '
					<p><strong>NGO OPIR.ORG</strong> — politically and financially independent NGO that started its activities in the most tragic days of Ukrainian Revolution 2013-2014 on the initiative of a group of young activists.</p>
					<p>The activities of our organization aimed at creating <strong>innovative tools</strong> to address socially important problems</p>
					<p>We look forward to Ukraine acquired <strong>social responsibility and initiative</strong> to use such tools for solving tasks and <strong>problems</strong></p>
					<h2 class="cs-center">Eyes of Ukraine</h2>
					<p>The project was in pilot mode during the presidential election held on 25/05/2014 and is designed for cyclical implementation during each of the next parliamentary and presidential elections</p>
					<p><strong>Objective:</strong> to ensure lighting electoral process in case of significant falsifications - use of evidence for the annulment of results and if possible obstruction violations through surgery.</p>
					<p><strong>Core:</strong> the possibility to quickly fix violations and the introduction of online video stream observation at polling stations through mobile applications. Monitoring of such violations on geolocation web service with an appropriate organized response of the community as part of mobile teams.</p>
					<h2 class="cs-center">Contacts</h2>
					<p>The central office of NGO OPIR.ORG:</p>
					<p>25, Kostyantynivska str, Kyiv, Ukraine</p>
					<p><a href="mailto:info@opir.org">info@opir.org</a></p>
					<p>+380 93 01 222 11</p>
					<h2 class="cs-center">Partners</h2>
					<p style="display:flex;display:-webkit-flex;justify-content:space-around;text-align:center;">
						<a href="http://www.hromadske.tv/">
							<img src="/components/modules/Info/includes/img/1.png" alt=""/>
						</a>
						<a href="http://www.cvu.org.ua/">
							<img src="/components/modules/Info/includes/img/2.png" alt=""/>
						</a>
						<a href="http://chesno.org/">
							<img src="/components/modules/Info/includes/img/3.png" alt=""/>
						</a>
						<a href="http://gpp.kiev.ua/">
							<img src="/components/modules/Info/includes/img/4.png" alt=""/>
						</a>
					</p>
				';
		}
	}
}
