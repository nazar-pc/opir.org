<?php
/**
 * @package        Help
 * @category       modules
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
 */
namespace cs\modules\Help;

use cs\Language;

class Help {
	/**
	 * Get info
	 *
	 * @return string
	 */
	static function get () {
		switch (Language::instance()->clang) {
			case 'uk':
				return '
					<p>Наразі opir.org запускає проект "Очі України" – організація процесу стрім-відео спостереження з виборчих дільниць, розробка веб-платформи для збору інформації щодо порушень та розробка мобільного програмного забезпечення</p>
					<p>Проект являється соціальним та не комерційним тож ми потребуємо вашої допомоги.</p>
					<p>Рахунок для переказу в UAH/EUR/USD:</p>
					<p>ЄДРПОУ: 39214344</p>
					<p>Р\р: 26001510692100</p>
					<p>Отримувач: ГО ВГО ОПІГОРГ</p>
					<p>МФО банку: 351005 (АТ "УкрСиббанк")</p>
					<p>Призначення: Добровільна пожертва на здійснення статутної діяльності ГО "ВГО ОПІРОРГ".</p>
					<p>Гаманець Bitcoin: 1F8Wq9t562hHGcXfSDK7ZUZJhUjroZhwRg</p>
					<p>Від Вашої допомоги напряму залежить кількість спостерігачів-стрімерів, що в свою чергу напряму впливає на об’єм громадського контролю над чесністю виборів в Україні!</p>
				';
			case 'ru':
				return '
					<p>На данный момент opir.org запускает проект "Глаза Украины" - организация процесса стрим-видеонаблюдения с избирательных участков, разработка веб-платформы для сбора информации о нарушениях и разработка мобильного программного обеспечения.</p>
					<p>Проект является социальным но не коммерческим и мы нуждаемся в вашей помощи</p>
					<p>Счёт для перевода в UAH/EUR/USD:</p>
					<p>ЕГРПОУ: 39214344</p>
					<p>Р\с: 26001510692100</p>
					<p>Получатель: ГО ВГО ОПІГОРГ</p>
					<p>МФО банка: 351005 (АО "УкрСиббанк")</p>
					<p>Назначение: Добровольное пожертвование на осуществление уставной деятельности ГО "ВГО ОПИРОРГ".</p>
					<p>Кошелек Bitcoin: 1F8Wq9t562hHGcXfSDK7ZUZJhUjroZhwRg</p>
					<p>От Вашей помощи напрямую зависит количество наблюдателей-стримеров, что в свою очередь прямую влияет на объем общественного контроля над честностью выборов в Украине!</p>
				';
			case 'en':
			default:
				return '
					<p>opir.org is launching "Eyes of Ukraine" project for video stream of surveillance process on polling stations, the development of web-based platform to gather information on violations and mobile software development.</p>
					<p>The project is social and not commercial so we need your help.</p>
					<p>Account for transfer in UAH/EUR/USD:</p>
					<p>EDRPOU: 39214344</p>
					<p>Correspondent account: 26001510692100</p>
					<p>Recipient: ГО ВГО ОПИРОРГ</p>
					<p>MFO: 351005 (JSC UkrSibbank)</p>
					<p>Purpose: Donation to carry out statutory activities NGO "APA OPIRORG".</p>
					<p>Bitcoin Wallet: 1F8Wq9t562hHGcXfSDK7ZUZJhUjroZhwRg</p>
					<p>The number of observers-streamers directly depends on your help, which in turn directly affects the amount of public control over the integrity of elections in Ukraine!</p>
				';
		}

	}
}
