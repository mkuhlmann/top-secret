<?php

namespace Areus;

class Application
{
	protected $basePath;
	protected Container $container;

	protected static $instance;

	public static function setInstance($app)
	{
		self::$instance = $app;
	}

	public static function getInstance(): self
	{
		return self::$instance;
	}

	public function getContainer(): Container
	{
		return $this->container;
	}

	public function __construct($basePath, $container)
	{
		if (static::$instance == null) {
			static::setInstance($this);
		}

		$this->container = $container;
		$this->basePath = rtrim($basePath, '\/');
	}

	public function has($id)
	{
		return $this->container->has($id);
	}

	public function get($id, bool $new = false, $throw = false)
	{
		$obj = null;
		try {
			if (!$new)
				$obj = $this->container->get($id);
			else
				$obj = $this->container->getNew($id);
		} catch (\Exception $e) {
			if ($throw) {
				throw $e;
			}
			return null;
		}

		if ($this->container->hasTag($id)) {
			return $obj[0];
		}

		return $obj;
	}

	public function set($id, $obj)
	{
		$this->container->add($id, $obj);
	}

	public function __get($name)
	{
		if ($name == 'container') return $this->container;
		return $this->get($name);
	}

	public function path($subPath = '')
	{
		$subPath = trim($subPath, '\/');
		return $this->basePath . '/' . $subPath;
	}
}

abstract class ApplicationModule
{
	protected $app;

	public function __construct(\Areus\Application $app)
	{
		$this->app = $app;
	}
}
