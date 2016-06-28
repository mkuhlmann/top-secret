<?php

namespace Areus;

class Application extends \Illuminate\Container\Container {

	public function __construct() {
		if(static::$instance == null)
			static::setInstance($this);

		$this->instance('Areus\Application', $this);
		$this->alias('Areus\Application', 'app');

		/*$inject = [
			'request' => 'Areus\Request',
			'session' => 'Areus\Session',
			'auth' => 'Areus\Auth',
			'router' => 'Areus\Router',
			'config' => 'Areus\Config',
			'view' => 'Areus\View\Factory'
		];

		foreach($inject as $key => $value) {
			$this->registerSingleton($key, $value);
		}*/
	}

	public function register($abstract, $concrete = null, $shared = false) {
		if($concrete == null) {
			$this->bind($abstract, $concrete, $shared);
		} else {
			$this->bind($concrete, null, $shared);
			$this->alias($concrete, $abstract, $shared);
		}
	}

	public function registerSingleton($abstract, $concrete = null) {
		$this->register($abstract, $concrete, true);
	}

	/*public function __get($key) {
		return $this->make($key);
	}*/

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
