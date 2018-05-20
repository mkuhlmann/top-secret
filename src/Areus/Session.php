<?php

namespace Areus;

use Dflydev\FigCookies\SetCookie;
use Dflydev\FigCookies\FigRequestCookies;

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
		$cookie = FigRequestCookies::get($this->app->request, $this->app->config->get('areus.session.cookie'));
		$this->id = $cookie->getValue();

		$lottery = $this->app->config->get('areus.session.lottery');
		if(mt_rand(1, $lottery[1]) <= $lottery[0]) {
			$this->sessionHandler->gc($this->app->config->get('areus.session.lifetime'));
		}

		if($this->id !== null) {
			$this->attributes = unserialize($this->sessionHandler->read($this->id));
		}

		if($this->attributes == null || !is_array($this->attributes)) {
			$this->attributes = [];
		}
	}

	public function forget($key) {
		if(isset($this->attributes[$key])) {
			unset($this->attributes[$key]);
		}
	}

	public function has($key) {
		return isset($this->attributes[$key]);
	}

	public function get($key, $default = null) {
		return $this->has($key) ? $this->attributes[$key] : $default;
	}

	public function put($key, $value) {
		$this->ensureSessionInitialised();
		$this->modified = true;
		$this->attributes[$key] = $value;
	}

	private function ensureSessionInitialised() {
		if($this->id === null) {
			$this->id = sha1(uniqid('', true));
			$this->save();
		}
	}

	public function save() {
		if($this->id === null) return;
		$this->sessionHandler->write($this->id, serialize($this->attributes));
	}

	public function generateCookie() {
		if($this->cookieSent || (!$this->modified && $this->id === null)) {
			return;
		}

		$config = $this->app->config->get('areus.session');

		return SetCookie::create($config['cookie'])
			->withValue($this-id)
			->withExpires(time() + 60* $config['lifetime'])
			->withPath($config['path'])
			->withDomain($config['domain'])
			->withSecure($config['secure'])
			->withHttpOnly($config['http_only']);
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
