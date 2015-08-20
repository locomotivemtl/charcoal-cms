<?php

namespace Charcoal\Cms;

// Module `charcoal-base` dependencies
use \Charcoal\Object\Content as Content;

class Text extends Content
{
	private $_title;
	private $_subtitle;
	private $_content;

	public function set_data(array $data)
	{
		return $this;
	}
}
