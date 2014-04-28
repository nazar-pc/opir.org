<?php
/**
 * Content of this file is not required!
 * You can add/edit/delete content as you want)
 * For example, you can add here including of class, which has the same name as core system class,
 * in this case your class will be used (may be useful in certain cases when modification of system files is needed)
 */
//namespace	cs\custom;
//use			cs\Language,
//			cs\Trigger;
//$_SERVER['REMOTE_ADDR']	= '127.0.0.1';
//class Page extends \cs\Page {
//	function init ($title, $theme, $color_scheme) {
//		$this->replace('<meta content="CleverStyle CMS by Mokrynskyi Nazar" name="generator">', '');
//		parent::init($title, $theme, $color_scheme);
//	}
//	/**
//	 * Creates cached version of given js and css files.<br>
//	 * Resulting file name consists of <b>$filename_prefix</b> and <b>$this->pcache_basename</b>
//	 *
//	 * @param string	$filename_prefix
//	 * @param array		$includes			Array of paths to files, may have keys: <b>css</b> and/or <b>js</b>
//	 *
//	 * @return array
//	 */
//	protected function create_cached_includes_files ($filename_prefix, $includes) {
//		$cache_hash	= [];
//		foreach ($includes as $extension => &$files) {
//			$files_content = '';
//			foreach ($files as $file) {
//				if (!file_exists($file)) {
//					continue;
//				}
//				/**
//				 * Insert external elements into resulting css file.
//				 * It is needed, because those files will not be copied into new destination of resulting css file.
//				 */
//				if ($extension == 'css') {
//					$files_content .= $this->css_includes_processing(
//						file_get_contents($file),
//						$file
//					);
//				} else {
//					$files_content .= file_get_contents($file).';';
//				}
//			}
//			if ($filename_prefix == '' && $extension == 'js') {
//				$files_content	= "window.cs.Language="._json_encode(Language::instance()).";$files_content";
//			}
//			$files_content	= str_replace(['Nazar', 'Mokrynskyi', 'nazar@mokrynskyi.com', 'CleverStyle CMS', 'MIT License, see license.txt'], '', $files_content);
//			file_put_contents(PCACHE."/$filename_prefix$this->pcache_basename.$extension", gzencode($files_content, 9), LOCK_EX | FILE_BINARY);
//			$cache_hash[$extension]	= substr(md5($files_content), 0, 5);
//		}
//		return $cache_hash;
//	}
//}
