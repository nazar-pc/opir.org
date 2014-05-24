<?php
/**
 * @package        Elections
 * @category       modules
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
 */
namespace cs\modules\Home;

use
	h,
	cs\Index,
	cs\Language,
	cs\Page,
	cs\User;

Index::instance()->title_auto = false;
$L                            = Language::instance();
$Page                         = Page::instance();
$Page->Header .=
	h::{'button.cs-elections-add-violation'}($L->add_violation).
	h::{'button.cs-elections-last-violations'}();
$Page->content(
	h::{'div.cs-elections-last-violations-panel'}(
		h::h2($L->last_violations).
		h::{'input.cs-elections-last-violations-panel-search[type=search]'}([
			'placeholder'	=> $L->number_or_address
		]).
		h::{'section'}()
	).
	h::{'aside.cs-elections-precinct-sidebar'}().
	h::{'aside.cs-elections-add-violation-sidebar'}().
	h::{'aside.cs-elections-violation-read-more-sidebar'}().
	h::{'aside.cs-elections-main-sidebar'}(
		h::{'div.cs-elections-socials'}(
			'<div class="yashare-auto-init" data-yashareL10n="uk" data-yashareQuickServices="facebook" data-yashareLink="https://www.facebook.com/opir.org" data-yashareTheme="counter" data-yashareImage="https://opir.org/components/modules/Elections/includes/img/share.png"></div>'.
			'<div class="yashare-auto-init" data-yashareL10n="uk" data-yashareQuickServices="vkontakte,twitter" data-yashareTheme="counter" data-yashareImage="https://opir.org/components/modules/Elections/includes/img/share.png"></div>'
		).
		h::h2($L->precincts_search).
		h::{'input.cs-elections-precincts-search[type=search]'}([
			'placeholder'	=> $L->number_or_address
		]).
		h::{'section.cs-elections-precincts-search-results[level=0]'}().
		h::h2($L->mobile_apps).
		h::{'div.cs-elections-mobile-apps'}(
			h::a(
				$L->download_in('App Store'),
				[
					'onclick'	=> "$.cs.simple_modal('$L->soon')"
				]
			).
			h::a(
				$L->download_in('Google Play'),
				[
					'onclick'	=> "$.cs.simple_modal('$L->soon')"
				]
			).
			h::a(
				$L->download_in('Market Place'),
				[
					'onclick'	=> "$.cs.simple_modal('$L->soon')"
				]
			)
		).
		h::h2($L->contacts).
		h::{'div.cs-elections-contacts'}(
			h::a(
				h::icon('phone').'+380 93 01 222 11').
			h::{'a[href=mailto:info@opir.org]'}(
				h::icon('envelope').'info@opir.org'
			)
		).
		h::h2($L->map_legend).
		h::{'div.cs-elections-map-legend'}(
			h::div($L->precincts_with_violations).
			h::div($L->precincts_without_violations)
		)
	)
);
