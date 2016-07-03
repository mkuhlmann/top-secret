<?php

namespace Areus;

class Request {
	private $props = [];

	public function __get($name) {
		if($name == 'query') return (object) $_GET;
		else if($name == 'cookies' || $name == 'cookie') return (object) $_COOKIE;

		if(!isset($this->props[$name])) {
			$this->props[$name] = $this->$name();
		}

		return $this->props[$name];
	}

	public function ip() {
		return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
	}

	public function method() {
		return $_SERVER['REQUEST_METHOD'];
	}

	public function path() {
		$r = explode('?', $_SERVER['REQUEST_URI'])[0];
		if(isset($_GET['r'])) {
			$r = $_GET['r'];
		}
		return $r;
	}

	public function query($key, $default = null) {
		return (isset($_GET[$key])) ? $_GET['key'] : $default;
	}

	public function cookie($key, $default = null) {
		return (isset($_COOKIE[$key])) ? $_COOKIE[$key] : $default;
	}

	public function cookies($key, $default = null) {
		return $this->cookie($key, $default);
	}

	public function post($key, $default = null) {
		return (isset($_POST[$key])) ? $_GET['key'] : $default;
	}

	public function protocol() {
		if (isset($_SERVER['HTTPS']) &&	($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
			return 'https';
		}
		return 'http';
	}

	public function secure() {
		return $this->protocol == 'https';
	}

	public function xhr() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
	}
}
