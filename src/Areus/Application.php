<?php

namespace Areus;

class Application extends \Illuminate\Container\Container {
	protected $basePath;

	public function __construct($basePath) {
		if(static::$instance == null) {
			static::setInstance($this);
		}

		$this->basePath = rtrim($basePath, '\/');

		$this->instance('appPath', $this->appPath());
		$this->instance('viewPath', $this->appPath('/views'));
		$this->instance('publicPath', $this->appPath('/public'));
		$this->instance('storagePath', $this->appPath('/storage'));
		$this->instance('configPath', $this->appPath('/config'));

		$this->instance('Areus\Application', $this);
		$this->alias('Areus\Application', 'app');
	}

	public function appPath($subPath = '') {
		return $this->basePath.$subPath;
	}

	public function abort($statusCode = null) {
		exit;
	}
}

abstract class ApplicationModule {
	protected $app;

	public function __construct(\Areus\Application $app) {
		$this->app = $app;
	}
}
