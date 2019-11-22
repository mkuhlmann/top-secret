<?php

namespace Areus;

class Application {
	protected $basePath;	
	protected $container;

	protected static $instance;

	
	public static function setInstance($app) {
		self::$instance = $app;
	}

	public static function getInstance() {
		return self::$instance;
	}


	public function __construct($basePath, $container) {
		if(static::$instance == null) {
			static::setInstance($this);
		}

		$this->container = $container;
		$this->basePath = rtrim($basePath, '\/');
	}

	public function get($id, bool $new = false) {
		$obj = $this->container->get($id, $new);
		if(is_array($obj)) {
			return $obj[0];
		}
		return $obj;
	}

	public function __get($name) {
		if($name == 'container') return $this->container;
		return $this->get($name);
	}

	public function path($subPath = '') {
		$subPath = trim($subPath, '\/');
		return $this->basePath.'/'.$subPath;
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
