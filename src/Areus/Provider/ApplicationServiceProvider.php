<?php

namespace Areus\Provider;

use Areus\Application;
use League\Container\ServiceProvider\AbstractServiceProvider;

abstract class ApplicationServiceProvider extends AbstractServiceProvider
{
	public function getApp(): Application
	{
		return $this->getContainer()->get(Application::class);
	}
}
