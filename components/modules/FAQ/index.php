<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs;
use			h;
Page::instance()->Header	=
	h::{'div.cs-home-logo'}(
		h::{'a[href=/] img'}([
			'src'	=> "components/modules/Home/includes/img/logo.png"
		]).
		'<div class="yashare-auto-init" data-yashareL10n="uk" data-yashareQuickServices="facebook" data-yashareLink="https://www.facebook.com/opir.org" data-yashareTheme="counter" data-yashareImage="https://opir.org/components/modules/Home/includes/img/share.png"></div>'.
		'<div class="yashare-auto-init" data-yashareL10n="uk" data-yashareQuickServices="vkontakte,twitter" data-yashareTheme="counter" data-yashareImage="https://opir.org/components/modules/Home/includes/img/share.png"></div>'
	).
	h::{'a.cs-home-cameras[href=Streams]'}('Камери').
	(
		User::instance()->user() ? h::{'button.cs-home-sign-out'}() : h::{'button.cs-home-sign-in'}()
	).
	h::{'button.cs-home-donate'}('Допомогти ініціативі');
?>
<article class="cs-faq">
	<div>
		<img src="../components/modules/Home/includes/img/faq/1.png" alt=""/>
		1. Підключити камеру до комп’ютера
	</div>
	<div>
		<img src="../components/modules/Home/includes/img/faq/2.png" alt=""/>
		2. Натиснути "Увійти" на <a href="http://ustream.tv" target="_blank">ustream.tv</a>
	</div>
	<div>
		<img src="../components/modules/Home/includes/img/faq/3.png" alt=""/>
		3. Увійти через Facebook
	</div>
	<div>
		<img src="../components/modules/Home/includes/img/faq/4.png" alt=""/>
		4. Погодитись з правилами ustream
	</div>
	<div>
		<img src="../components/modules/Home/includes/img/faq/5.png" alt=""/>
		5. Вибррати безкоштовний аккаунт Basic
	</div>
	<div>
		<img src="../components/modules/Home/includes/img/faq/6.png" alt=""/>
		6. Натиснути кнопку "В ефір"
	</div>
	<div>
		<img src="../components/modules/Home/includes/img/faq/7.png" alt=""/>
		7. Дозволити Flash використовувати аудіо та відео
	</div>
	<div>
		<img src="../components/modules/Home/includes/img/faq/8.png" alt=""/>
		8. Натиснути "Почати трансляцію" та "Почати запис"
	</div>
	<div>
		<img src="../components/modules/Home/includes/img/faq/9.png" alt=""/>
		9. Перейтиза посиланням справа від панелі налаштувань
	</div>
	<div>
		<img src="../components/modules/Home/includes/img/faq/10.png" alt=""/>
		10. Натиснути "Поділитись" та перейти за останнім посиланням
	</div>
	<div>
		<img src="../components/modules/Home/includes/img/faq/11.png" alt=""/>
		11. Скопіювати код iframe натиснувши "скопіювати в буфер"
	</div>
	<div>
		<img src="../components/modules/Home/includes/img/faq/12.png" alt=""/>
		12. На сайті opir.org перейти на <a href="https://opir.org/Streams">сторінку з камерами</a>, натиснувши кнопку "Камери" в лівій частину сторінки
	</div>
	<div>
		<img src="../components/modules/Home/includes/img/faq/13.png" alt=""/>
		13. Натиснути "додати камеру" там же
	</div>
	<div>
		<img src="../components/modules/Home/includes/img/faq/14.png" alt=""/>
		14. Вставити код iframe, вказати адресу та розташування на карті, натиснути "Додати"
	</div>
</article>
