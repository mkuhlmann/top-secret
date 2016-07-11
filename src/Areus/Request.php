<?php

namespace Areus;

class Request {
	private $props = [];
	private $body;

	public function __get($name) {
		if($name == 'query') return (object) $_GET;
		else if($name == 'cookies' || $name == 'cookie') return (object) $_COOKIE;

		if(!isset($this->props[$name])) {
			$this->props[$name] = $this->$name();
		}

		return $this->props[$name];
	}

	public function getBody() {
		if($this->body !== null) {
			return $this->body;
		}
		$this->body = file_get_contents('php://input');
		return $this->body;
	}

	public function ip() {
		return (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
	}

	public function method() {
		return $_SERVER['REQUEST_METHOD'];
	}

	public function isMethod($method) {
		return strtolower($this->method()) == strtolower($method);
	}

	public function path() {
		$r = explode('?', $_SERVER['REQUEST_URI'])[0];
		if(isset($_GET['r'])) {
			$r = $_GET['r'];
		}
		return $r;
	}

	public function header($key, $default = null) {
		$key = 'HTTP_'.str_replace(strtoupper($key), '-', '_');
		return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
	}

	public function query($key, $default = null) {
		return isset($_GET[$key]) ? $_GET[$key] : $default;
	}

	public function cookie($key, $default = null) {
		return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;
	}

	public function cookies($key, $default = null) {
		return $this->cookie($key, $default);
	}

	public function post($key, $default = null) {
		return isset($_POST[$key]) ? $_POST[$key] : $default;
	}

	public function input($key, $default = null) {
		if($this->isJson()) {
			$data = json_decode($this->getBody(), true);
		} else {
			$data = $this->method() == 'GET' ? $_GET : $_REQUEST;
		}
		return isset($data[$key]) ? $data[$key] : $default;
	}

	public function isJson() {
		return isset($_SERVER['CONTENT_TYPE']) && (strpos($_SERVER['CONTENT_TYPE'], '/json') !== false || strpos($_SERVER['CONTENT_TYPE'], '+json') !== false);
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
