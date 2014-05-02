<?php
/**
 * @package        OAuth2 customization
 * @category       plugins
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
 */
namespace cs\custom\modules\OAuth2;

use cs\modules\OAuth2\OAuth2 as OAuth2_original;

_require_once(MODULES.'/OAuth2/OAuth2.php', false);

class OAuth2 extends OAuth2_original {
	/**
	 * Check granted access for specified client
	 *
	 * @param int      $client
	 * @param bool|int $user If not specified - current user assumed
	 *
	 * @return bool
	 */
	function get_access ($client, $user = false) {
		return true;
	}
}
