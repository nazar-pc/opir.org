<?php
/**
 * @package        Package
 * @category       modules
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
 */
namespace cs\modules\Precincts;

use
	cs\Index,
	cs\Page,
	h;

if (isset($_POST['update'])) {
	time_limit_pause();
	$Precincts = Precincts::instance();
	$Precincts->del($Precincts->get_all());
	/**
	 * Download page with regions list
	 */
	$regions = file_get_contents('https://www.drv.gov.ua/portal/!cm_core.cm_index?option=ext_dvk&prejim=3&pmn_id=132');
	/**
	 * Find regions id
	 */
	preg_match_all('/!cm_core.cm_index\?option=ext_dvk&pid100=([0-9]*)&prejim=3/Uims', $regions, $regions);
	$regions = $regions[1];
	foreach ($regions as $region) {
		/**
		 * Download page with election districts of region
		 */
		$region_districts = file_get_contents("https://www.drv.gov.ua/portal/!cm_core.cm_index?option=ext_dvk&pid100=$region&prejim=3");
		/**
		 * Find districts id
		 */
		preg_match_all("/!cm_core.cm_index\?option=ext_dvk&pid100=$region&pf5271=([0-9]*)&prejim=3/Uims", $region_districts, $region_districts);
		$region_districts = $region_districts[1];
		foreach ($region_districts as $region_district) {
			$district_precincts =
				file_get_contents("https://www.drv.gov.ua/portal/!cm_core.cm_index?option=ext_dvk&pid100=$region&pf5271=$region_district&prejim=3");
			/**
			 * Find link to xml with information about all precincts in current district
			 */
			preg_match("#https://www.drv.gov.ua/portal/ext_maps.maps_4\?pf5271=$region_district&ts=[0-9]*#ims", $district_precincts, $district_precincts);
			/**
			 * Download xml with information about all precincts in current district
			 */
			$district_precincts = file_get_contents($district_precincts[0]);
			preg_match_all('#<tr><td>Виборча дільниця №:</td><td><b>(.*)</b></td></tr>#Uims', $district_precincts, $numbers);
			preg_match_all('#<tr><td>Адреса:</td><td><b>(.*)</b></td></tr>#Uims', $district_precincts, $addresses);
			preg_match_all('#<tr><td>Округ №:</td><td><b>(.*)</b></td></tr>#Uims', $district_precincts, $districts);
			preg_match_all('#<coordinates>(.*),#Uims', $district_precincts, $longitudes);
			preg_match_all('#<coordinates>.*,(.*)</coordinates>#Uims', $district_precincts, $latitudes);
			/**
			 * Make array of arrays with precincts data
			 */
			$district_precincts = array_map(
				null,
				$numbers[1],
				$addresses[1],
				$latitudes[1],
				$longitudes[1],
				$districts[1]
			);
			unset($numbers, $addresses, $latitudes, $longitudes, $districts);
			foreach ($district_precincts as $district_precinct) {
				call_user_func_array([$Precincts, 'add'], $district_precinct);
			}
			unset($district_precincts, $district_precinct);
		}
		unset($region_districts, $region_district);
	}
	unset($regions, $region);
	time_limit_pause(false);
}
if (isset($_POST['update_districts'])) {
	time_limit_pause();
	$Precincts = Precincts::instance();
	$regions   = file_get_contents('http://www.cvk.gov.ua/pls/vnd2014/wp030pt001f01=910.html');
	/**
	 * Find regions id
	 */
	preg_match_all('/wp023pt001f01=910pid100=([0-9]+).html/Uims', $regions, $regions);
	$regions = $regions[1];
	$regions = array_slice($regions, 1);
	foreach ($regions as $region) {
		$districts = file_get_contents("http://www.cvk.gov.ua/pls/vnd2014/wp023pt001f01=910pid100={$region}.html");
		preg_match_all('/№([0-9]+)</Uims', iconv('windows-1251', 'utf-8', $districts), $districts);
		foreach ($districts[1] as $district) {
			$district = file_get_contents("http://www.cvk.gov.ua/pls/vnd2014/wp024pt001f01=910pid100={$region}pf7331={$district}.html");
			if (preg_match('/№([0-9]+)<.*Поштова адреса ОВК<\/td>\n<td class=td2  >(.*)</Uims', iconv('windows-1251', 'utf-8', $district), $district)) {
				$address  = $district[2];
				$location = _json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.rawurlencode($address).'&sensor=false'));
				if ($location['status'] == 'OK') {
					$location = $location['results'][0]['geometry']['location'];
					call_user_func_array([$Precincts, 'add'], [$district[1], $address, $location['lat'], $location['lng'], 0]);
				}
			}
		}
		unset($districts, $district, $address, $location);
	}
	unset($regions, $region);
	$Precincts->db_prime()->q(
		"INSERT INTO `[prefix]precincts` (`id`, `number`, `address_uk`, `address_en`, `address_ru`, `lat`, `lng`, `district`, `violations`) VALUES (NULL, '0', '01196, м.Київ, площа Лесі Українки, 1, 2-й поверх, хол прес-центру ЦВК', '1196, Kyiv, Lesi Ukrainky square 1, 2nd Floor, Hall Press Center CEC', '01196, г.Киев, площадь Леси Украинки, 1, 2-й этаж, холл пресс-центра ЦИК', '50.428073', '30.541399', '0', '0')"
	);
}
if (isset($_POST['update_addresses'])) {
	time_limit_pause();
	$Precincts     = Precincts::instance();
	$all_precincts = $Precincts->get_all();
	$cdb           = $Precincts->db_prime();
	foreach ($all_precincts as $p) {
		if ($cdb->qfs("SELECT `address_en` FROM `[prefix]precincts` WHERE `id` = $p")) {
			continue;
		}
		$p          = $Precincts->get($p);
		$en_address = _json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.rawurlencode($p['address']).'&sensor=false&language=en'));
		if ($en_address['status'] == 'OK') {
			$cdb->q(
				"UPDATE `[prefix]precincts`
				SET `address_en` = '%s'
				WHERE `id` = '%s'",
				$en_address['results'][0]['formatted_address'],
				$p['id']
			);
		}
		unset($en_address);
		$ru_address = _json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.rawurlencode($p['address']).'&sensor=false&language=ru'));
		if ($ru_address['status'] == 'OK') {
			$cdb->q(
				"UPDATE `[prefix]precincts`
				SET `address_ru` = '%s'
				WHERE `id` = '%s'",
				$ru_address['results'][0]['formatted_address'],
				$p['id']
			);
		}
		unset($ru_address);
	}
}
$Index          = Index::instance();
$Index->buttons = false;
$Index->content(
	h::{'button[type=submit][name=update]'}('Оновити список дільниць з офіційного сайту виборів').
	h::{'button[type=submit][name=update_districts]'}('Оновити список округів з офіційного сайту виборів').
	h::{'button[type=submit][name=update_addresses]'}('Уточнити адреси дільниць російською та англійською')
);
Page::instance()->warning('Оновлення призведе до видалення дільниць, дані пов’язані з ними буде втрачено');
