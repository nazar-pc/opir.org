<?php
/**
 * @package		Info
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Info;
use cs\Page;

Page::instance()->json(
	Info::get()
);
