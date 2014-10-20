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
					<p>Принципово вектор розвитку і діяльності нашої організації збігається з інтересами народу України. Ми зобов\'язуємося й надалі відстоювати інтереси громади та лишатися незалежною організацєю. Саме тому ми потребуємо Вашої фінансової підтримки нашого проекту.</p>
					<p>Ми зобов’язуємося вести відкриту звітність щодо використання коштів, оскільки принцип чесності і прозорості лежить у фундаменті нашої організації.</p>
					<p>Рахунок для переказу в UAH/EUR/USD:</p>
					<p>ЄДРПОУ: 39214344</p>
					<p>Р\р: 26001510692100</p>
					<p>Отримувач: ГО ВГО ОПІГОРГ</p>
					<p>МФО банку: 351005 (АТ "УкрСиббанк")</p>
					<p>Призначення: Добровільна пожертва на здійснення статутної діяльності ГО "ВГО ОПІРОРГ".</p>
				';
			case 'ru':
				return '
					<p>Принципиально вектор развития и деятельности нашей организации совпадает с интересами народа Украины. Мы обязуемся и впредь отстаивать интересы общества и оставаться независимой организаций. Именно поэтому мы нуждаемся Вашей финансовой поддержке нашего проекта.</p>
					<p>Мы обязуемся вести открытую отчетность об использовании средств, поскольку принцип честности и прозрачности лежит в фундаменте нашей организации.</p>
					<p>Счёт для перевода в UAH/EUR/USD:</p>
					<p>ЕГРПОУ: 39214344</p>
					<p>Р\с: 26001510692100</p>
					<p>Получатель: ГО ВГО ОПІГОРГ</p>
					<p>МФО банка: 351005 (АО "УкрСиббанк")</p>
					<p>Назначение: Добровольное пожертвование на осуществление уставной деятельности ГО "ВГО ОПИРОРГ".</p>
				';
			case 'en':
			default:
				return '
					<p>Fundamentally vector of development and activities of our organization coincides with the interests of the people of Ukraine. We pledge to continue to defend the interests of the community and stay independent organization. That\'s why we need your financial support for our project.</p>
					<p>We pledge to keep an open reporting on the use of funds, as the principle of honesty and transparency lies at the foundation of our organization.</p>
					<p>Account for transfer in UAH/EUR/USD:</p>
					<p>EDRPOU: 39214344</p>
					<p>Correspondent account: 26001510692100</p>
					<p>Recipient: ГО ВГО ОПІГОРГ</p>
					<p>MFO: 351005 (JSC UkrSibbank)</p>
					<p>Purpose: Donation to carry out statutory activities NGO "APA OPIRORG".</p>
				';
		}

	}
}
