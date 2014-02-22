<?php
/**
 * @package		Streams
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Streams;
use			cs\Page;
Page::instance()->json(
	Streams::instance()->get(
		Streams::instance()->get_all()
	) ?: []
);
