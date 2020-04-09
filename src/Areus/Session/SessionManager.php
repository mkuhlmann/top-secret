<?php

namespace Areus\Session;

use Dflydev\FigCookies\SetCookie;
use Dflydev\FigCookies\FigRequestCookies;
use Psr\Http\Message\ServerRequestInterface;
use SessionHandlerInterface;

class SessionManager {
	protected $sessionHandler;
	/** @var array */
	protected $config;

	protected $modified = false;
	protected $cookieSent = false;

	protected $id;
	protected $attributes;

	public function __construct(SessionHandlerInterface $sessionHandler, array $config) {
		$this->config = $config;
		$this->sessionHandler = $sessionHandler;
	}

	public function start(ServerRequestInterface $request) {
		$cookie = FigRequestCookies::get($request, $this->config['cookie']);
		$this->id = $cookie->getValue();

		$lottery = $this->config['lottery'];
		if(mt_rand(1, $lottery[1]) <= $lottery[0]) {
			$this->sessionHandler->gc($this->config['lifetime']);
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
			$this->modified = true;
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
		if(!$this->modified && $this->id === null) {
			return;
		}

		if($this->id === null) {
			$this->ensureSessionInitialised();
		}

		if($this->modified) {
			$this->save();
		}

		$config = $this->config;

		return SetCookie::create($config['cookie'])
			->withValue($this->id)
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
