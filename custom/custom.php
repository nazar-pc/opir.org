<?php
/**
 * Content of this file is not required!
 * You can add/edit/delete content as you want)
 * For example, you can add here including of class, which has the same name as core system class,
 * in this case your class will be used (may be useful in certain cases when modification of system files is needed)
 */
namespace	cs\custom;
use			cs\Language,
			cs\Trigger;
$_SERVER['REMOTE_ADDR']	= '127.0.0.1';
class Page extends \cs\Page {
	function init ($title, $theme, $color_scheme) {
		$this->replace('<meta content="CleverStyle CMS by Mokrynskyi Nazar" name="generator">', '');
		parent::init($title, $theme, $color_scheme);
	}
	protected function rebuild_cache () {
		$key	= '';
		Trigger::instance()->run(
			'System/Page/rebuild_cache',
			[
				'key'	=> &$key
			]
		);
		$this->get_includes_list(true);
		foreach ($this->includes as $extension => &$files) {
			$temp_cache = '';
			foreach ($files as $file) {
				if (file_exists($file)) {
					$current_cache = file_get_contents($file);
					if ($extension == 'css') {
						/**
						 * Insert external elements into resulting css file.
						 * It is needed, because those files will not be copied into new destination of resulting css file.
						 */
						$this->css_includes_processing($current_cache, $file);
					}
					if ($extension == 'js') {
						$current_cache .= ';';
					}
					$temp_cache .= $current_cache;
					unset($current_cache);
				}
			}
			if ($extension == 'js') {
				$temp_cache	= "window.cs.Language="._json_encode(Language::instance()).";$temp_cache";
			}
			$temp_cache	= str_replace(['Nazar', 'Mokrynskyi', 'nazar@mokrynskyi.com', 'CleverStyle CMS', 'MIT License, see license.txt'], '', $temp_cache);
			file_put_contents(PCACHE."/$this->pcache_basename$extension", gzencode($temp_cache, 9), LOCK_EX | FILE_BINARY);
			$key .= md5($temp_cache);
		}
		file_put_contents(PCACHE.'/pcache_key', mb_substr(md5($key), 0, 5), LOCK_EX | FILE_BINARY);
		return $this;
	}
}
/*if (
	strpos($_SERVER['REQUEST_URI'], '/api/System/user/sign_in') !== false &&
	(
		$_SERVER['QUERY_STRING'] ||
		strpos($_SERVER['REQUEST_URI'], '/api/System/user/sign_in/') !== false
	)
) {
	error_code(404);
	define('STOP', true);
	exit;
}
if (
	strpos($_SERVER['REQUEST_URI'], '/api/System/user') !== false &&
	strpos($_SERVER['REQUEST_URI'], '/api/System/user/') !== false
) {
	error_code(404);
	define('STOP', true);
	exit;
}
if (
	strpos($_SERVER['REQUEST_URI'], '/api/System/user') !== false &&
	$_SERVER['QUERY_STRING']
) {
	error_code(404);
	define('STOP', true);
	exit;
}

if (isset($_REQUEST['session']) && strlen($_REQUEST['session']) != 32) {
	error_code(404);
	define('STOP', true);
	exit;
}

if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] && strpos($_SERVER['HTTP_REFERER'], 'https://opir.org') !== 0 && $_SERVER['REQUEST_URI'] !== '/') {
	error_code(404);
	define('STOP', true);
	exit;
}
*/
