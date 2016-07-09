<?php

namespace Areus;

class Session {
	protected $sessionHandler;
	protected $app;

	protected $modified = false;
	protected $cookieSent = false;

	protected $id;
	protected $attributes;

	public function __construct(\Areus\Application $app) {
		$this->app = $app;
		switch($app->config->get('areus.session.driver')) {
			case 'file':
				$this->sessionHandler = new \Areus\SessionHandlerFilesystem(
					$app->storagePath.'/sessions',
					$app->config->get('areus.session.lifetime')
				);
				break;
			default:
				$this->sessionHandler = $app->make($app->config->get('areus.session.driver'));
				break;
		}

		$this->start();
	}

	public function start() {
		$this->id = $this->app->request->cookie($this->app->config->get('areus.session.cookie'));

		if($this->id !== null) {
			$this->attributes = unserialize($this->sessionHandler->read($this->id));
		}

		if($this->attributes == null || !is_array($this->attributes)) {
			$this->attributes = [];
		}
	}

	public function has($key) {
		return isset($this->attributes[$key]);
	}

	public function get($key, $default = null) {
		return $this->has($key) ? $this->attributes[$key] : $default;
	}

	public function put($key, $value) {
		$this->modified = true;
		$this->attributes[$key] = $value;
	}

	public function save() {
		if($this->id === null) {
			$this->id = sha1(uniqid('', true));
		}

		$this->sessionHandler->write($this->id, serialize($this->attributes));
	}

	public function sendCookie() {
		if($this->cookieSent || (!$this->modified && $this->id === null)) {
			return;
		}

		$this->save();

		$config = $this->app->config->get('areus.session');
		$this->app->response->withCookie(
			$config['cookie'],
			$this->id,
			time() + 60* $config['lifetime'],
			$config['path'],
			$config['domain'],
			$config['secure'],
			$config['http_only']
		);
	}

	public function token() {
		$token = $this->get('_token');
		if($token == null) {
			$token = sha1(uniqid('', true));
			$this->put('_token', $token);
		}
		return $token;
	}
}
