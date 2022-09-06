<?php

namespace Areus;

class Container extends \League\Container\Container
{
	public function hasTag($tag)
	{
		return $this->definitions->hasTag($tag);
	}
}
