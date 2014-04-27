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
			preg_match("#http://www.drv.gov.ua/portal/ext_maps.maps_4\?pf5271=$region_district&ts=[0-9]*#ims", $district_precincts, $district_precincts);
			/**
			 * Download xml with information about all precincts in current district
			 */
			$district_precincts = file_get_contents($district_precincts[0]);
			preg_match_all('#<tr><td>Виборча дільниця №:</td><td><b>(.*)</b></td></tr>#Uims', $district_precincts, $numbers);
			preg_match_all('#<tr><td>Адреса:</td><td><b>(.*)</b></td></tr>#Uims', $district_precincts, $addresses);
			preg_match_all('#<tr><td>Округ №:</td><td><b>(.*)</b></td></tr>#Uims', $district_precincts, $districts);
			preg_match_all('#<coordinates>(.*),#Uims', $district_precincts, $latitudes);
			preg_match_all('#<coordinates>.*,(.*)</coordinates>#Uims', $district_precincts, $longitudes);
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
$Index          = Index::instance();
$Index->buttons = false;
$Index->content(
	h::{'button[type=submit][name=update]'}('Оновити список дільниць з офіційного сайту виборів')
);
Page::instance()->warning('Оновлення призведе до видалення дільниць, дані пов’язані з ними буде втрачено');
