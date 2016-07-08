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
		$this->instance('publicPath', $this->publicPath());
		$this->instance('storagePath', $this->storagePath());
		$this->instance('configPath', $this->configPath());

		$this->instance('Areus\Application', $this);
		$this->alias('Areus\Application', 'app');
	}

	public function appPath() {
		return $this->basePath;
	}

	public function publicPath() {
		return $this->basePath.'/public';
	}

	public function storagePath() {
		return $this->basePath.'/storage';
	}

	public function configPath() {
		return $this->basePath.'/config';
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
