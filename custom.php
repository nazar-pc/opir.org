<?php
/**
 * Content of this file is not required!
 * You can add/edit/delete content as you want)
 * For example, you can add here including of class, which has the same name as core system class,
 * in this case your class will be used (may be useful in certain cases when modification of system files is needed)
 */
namespace	cs\custom;
$_SERVER['REMOTE_ADDR']	= '127.0.0.1';
class Page extends \cs\Page {
	function css_includes_processing (&$data, $file) {
		parent::css_includes_processing($data, $file);
		$data	= str_replace(['Nazar', 'Mokrynskyi'], '', $data);
	}
}
