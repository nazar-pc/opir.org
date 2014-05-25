<?php
/**
 * @package        Download
 * @category       modules
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
 */
namespace cs;

if (!file_exists(DIR.'/downloads')) {
	$downloads = 0;
} else {
	$downloads = file_get_json(DIR.'/downloads');
}
$downloads++;
file_put_json(DIR.'/downloads', $downloads);
header('Location: https://opir.org/storage/public/opir.org.apk', true, 301);
interface_off();
Index::instance()->stop = true;
