<?php
/**
 * @package        Elections
 * @category       modules
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
 */
namespace cs\modules\Precincts;

use
	h,
	cs\Core,
	cs\Index,
	cs\Language,
	cs\Page,
	cs\User;

$Index             = Index::instance();
$Index->title_auto = false;
$L                 = Language::instance();
$Page              = Page::instance();
if (isset($Index->route_path[0], $Index->route_ids[1]) && $Index->route_path[0] == 'violation') {
	$violation = Violations::instance()->get($Index->route_ids[1]);
	if ($violation['images']) {
		$Page->replace('/<meta content="[^"]*share.png" property="og:image">/Uims', '');
		$Page->replace('/<link href="[^"]*share.png" rel="image_src">/Uims', '');
		$Page->link([
			'rel'  => 'image_src',
			'href' => $violation['images'][0]
		]);
		foreach ($violation['images'] as $image) {
			$Page->og('image', $image);
			$Page->og('image:secure_url', $image);
		}
	}
	if ($violation['text']) {
		$Page->Description = description($violation['text']);
	}
}
$Page->Header .=
	h::{'button.cs-elections-add-violation'}(h::{'i.uk-icon-plus'}()." $L->add_violation").
	h::{'button.cs-elections-last-violations'}();
$Page->js(
	"window.disqus_shortname = '".Core::instance()->disqus_shortname."';",
	'code'
);
$Page->content(
	h::{'div.cs-elections-last-violations-panel'}(
		h::h2($L->last_violations).
		h::{'section'}()
	).
	h::{'aside.cs-elections-precinct-sidebar'}().
	h::{'aside.cs-elections-add-violation-sidebar'}().
	h::{'aside.cs-elections-violation-read-more-sidebar'}().
	h::{'aside.cs-elections-main-sidebar'}(
		h::{'div.cs-elections-opirzagin a[href=become_eyes]'}($L->become_eyes).
		h::{'div.cs-elections-opirzagin a[href=opirzagin]'}($L->opircrew).
		h::{'div.cs-elections-opirzagin a[href=instruktsia]'}($L->instruktsia).
		h::{'div.cs-elections-socials'}(
			'<div class="yashare-auto-init" data-yashareL10n="uk" data-yashareQuickServices="facebook" data-yashareLink="https://www.facebook.com/opir.org" data-yashareTheme="counter" data-yashareImage="https://opir.org/components/modules/Common/includes/img/share.png"></div>'.
			'<div class="yashare-auto-init" data-yashareL10n="uk" data-yashareQuickServices="vkontakte,twitter" data-yashareLink="https://opir.org/" data-yashareTheme="counter" data-yashareImage="https://opir.org/components/modules/Common/includes/img/share.png"></div>'
		).
		h::h2($L->precincts_search).
		h::{'input.cs-elections-precincts-search[type=search]'}([
			'placeholder' => $L->number_or_address
		]).
		h::{'section.cs-elections-precincts-search-results[level=0]'}().
		h::h2($L->mobile_apps).
		h::{'div.cs-elections-mobile-apps'}(
			h::{'a[target=_blank'}(
				$L->download_in('App Store'),
				[
					'href' => 'https://itunes.apple.com/ua/app/opir.org/id896488790'
				]
			).
			h::{'a[target=_blank]'}(
				$L->download_in('Google Play'),
				[
					'href' => 'https://play.google.com/store/apps/details?id=example.yariksoffice'
				]
			)/*.
			h::a(
				$L->download_in('Market Place'),
				[
					'onclick' => "$.cs.simple_modal('$L->soon')"
				]
			)*/
		).
		h::h2($L->contacts).
		h::{'div.cs-elections-contacts'}(
			h::a(
				h::icon('phone').'+38 093 012 22 11'
			).
			h::a(
				h::icon('phone').'+38 067 708 42 90'
			).
			h::{'a[href=mailto:info@opir.org]'}(
				h::icon('envelope').'info@opir.org'
			)
		).
		h::h2($L->map_legend).
		h::{'div.cs-elections-map-legend'}(
			h::div($L->precincts_with_violations).
			h::div($L->precincts_without_violations).
			h::div($L->district_precincts)
		)
	)
);
