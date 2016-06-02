<?php

namespace Areus;

class Router {
	private $routes = [];

	private function __construct() {}
	private function __clone() {}

	public function get($pattern, $callback) {
		$this->route('get', $pattern, $callback);
	}

	public function route($method, $pattern, $params) {
		if(is_callable($params)) {
			$params = ['callback' => $params];
		}
		$pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';
		$this->$routes[$pattern] = ['method' => 'any', 'pattern' => $pattern, 'params' => $params];
	}


	public function execute($url) {
		$req = new Request();
		$res = new Reponse();
		foreach ($this->$routes as $pattern => $args) {
			if (preg_match($pattern, $url, $params)) {
				array_shift($params);
				return call_user_func_array($callback, [$req, $res] + array_values($params));
			}
		}
	}
}

class Request {
	private $props = [];
	private $params = [];

	public function __get($name) {
		if($name == 'query') return (object) $_GET;
		if($name == 'params') return (object) $this->params;

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
		return $_GET[$key] ?? $default;
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
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
	}}
}

class Response {
	public function status($code = 200) {
		http_response_code($code);
		return $this;
	}

	public function json($arr) {
		header('Content-Type: application/json; charset=utf-8;');
		echo json_encode($arr);
	}
}
