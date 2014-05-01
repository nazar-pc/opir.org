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
					<p><strong>Всеукраїнське Громадське Об’єднання OPIR.ORG</strong> — політично й фінансово незалежна громадська організація, що почала свою діяльність в найтрагічніші дні Української Революції 2013-2014 років за ініціативою групи громадських активістів з метою координації дій організацій Майдану та самоорганізованих загонів самооборони всіх міст України. З огляду на звершення революції та зміну ситуації в країні організація поставила перед собою нові цілі, зокрема створення й впровадження інструментів контролю громадою діяльності державних органів та представників громадян у владі.</p>
					<p><strong>Стратегічні цілі ВГО OPIR.ORG:</strong></p>
					<ul>
						<li>Забезпечення освітлення виборчого процесу на всіх майбутніх виборах</li>
						<li>Розробка та впровадження інноваційної системи інформування про випадки корупції органів державної влади та системи контролю їх діяльності</li>
					</ul>
					<p><strong>Пріоритетні напрямки діяльності ВГО OPIR.ORG:</strong></p>
					<ul>
						<li>Охорона правопорядку: впровадження системи громадського відео-спостереження за порядком на вулицях міст України та координація оперативного реагування загонів самооборони та органів МВС</li>
						<li>Виборчий процес: організація спостерігачів-стрімерів на виборчих дільницях всіх майбутніх виборів в Україні</li>
						<li>Контроль органів державної влади: розробка та впровадження інструменту анонімного інформування щодо випадків корупції в органах державної влади</li>
						<li>Контроль органів державної влади: створення незалежної інформаційної платформи щодо діяльності органів державної влади</li>
					</ul>
					<p><strong>Контакти:</strong></p>
					<p>Центральний офіс ВГО OPIR.ORG:</p>
					<p>м. Київ, вул. Костянтинівська, 25</p>
					<p><a href="mailto:info@opir.org">info@opir.org</a></p>
					<p>+380 93 01 222 11</p>
				';
			case 'ru':
				return '
					<p><strong>Всеукраинское Общественное Объединение OPIR.ORG</strong> — политически и финансово независимая общественная организация, начавшая свою деятельность в самые трагические дни украинской Революции 2013-2014 годов по инициативе группы общественных активистов с целью координации действий организаций Майдана и самоорганизующихся отрядов самообороны всех городов Украины. Учитывая свершение революции и изменение ситуации в стране организация поставила перед собой новые цели, в том числе создание и внедрение инструментов контроля обществом деятельности государственных органов и представителей граждан во власти.</p>
					<p><strong>Стратегические цели ВОО OPIR.ORG:</strong></p>
					<ul>
						<li>Обеспечение освещения избирательного процесса на всех будущих выборах</li>
						<li>Разработка и внедрение инновационной системы информирования о случаях коррупции органов государственной власти и системы контроля их деятельности</li>
					</ul>
					<p><strong>Приоритетные направления деятельности ВОО OPIR.ORG:</strong></p>
					<ul>
						<li>Охрана правопорядка: внедрение системы общественного видеонаблюдения за порядком на улицах городов Украины и координация оперативного реагирования отрядов самообороны и органов МВД</li>
						<li>Избирательный процесс: организация наблюдателей - стримеров на избирательных участках всех будущих выборов в Украине</li>
						<li>Контроль органов государственной власти: разработка и внедрение инструмента анонимного информирования о случаях коррупции в органах государственной власти</li>
						<li>Контроль органов государственной власти: создание независимой информационной платформы относительно деятельности органов государственной власти</li>
					</ul>
					<p><strong>Контакты:</strong></p>
					<p>Центральный офис ВОО OPIR.ORG:</p>
					<p>г. Киев, ул . Константиновская, 25</p>
					<p><a href="mailto:info@opir.org">info@opir.org</a></p>
					<p>+380 93 01 222 11</p>
				';
			case 'en':
			default:
				return '
					<p><strong>All-Ukrainian Public Association OPIR.ORG</strong> — politically and financially independent public organization, which started its activities in the most tragic days of Ukrainian Revolution in 2013-2014 by a group of social activists to coordinate actions of Maidan’s organizations and self-organized self-defense groups of all cities of Ukraine. After the fulfillment of the revolution and changes in the situation of the country organization has set new goals, including the creation and implementation of tools to monitor public activities of government bodies and representatives of citizens in government.</p>
					<p><strong>Strategic objectives APA OPIR.ORG:</strong></p>
					<ul>
						<li>Provide coverage of the electoral process in all future elections</li>
						<li>Development and implementation of innovative information system of corruption of public authorities and control their activities</li>
					</ul>
					<p><strong>Priority activities APA OPIR.ORG:</strong></p>
					<ul>
						<li>Law enforcement: implementation of public CCTV to control an order on the streets of Ukrainian cities and to coordinate a rapid response self-defense units and MIA</li>
						<li>The electoral process: the watchdog organization - streamers at polling stations for all future elections in Ukraine</li>
						<li>Control of public authorities: development and implementation of tool for anonymous reports in instances of corruption in government</li>
						<li>Control of public authorities: the creation of an independent information platform on the activities of public authorities</li>
					</ul>
					<p><strong>Contacts:</strong></p>
					<p>The central office of APA OPIR.ORG:</p>
					<p>25, Kostyantynivska str, Kyiv, Ukraine</p>
					<p><a href="mailto:info@opir.org">info@opir.org</a></p>
					<p>+380 93 01 222 11</p>
				';
		}

	}
}
