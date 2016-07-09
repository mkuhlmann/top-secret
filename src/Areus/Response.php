<?php

namespace Areus;

class Response extends \Areus\ApplicationModule {
	protected $headers = [];
	private $headersSent = false;
	private $dataSent = false;

	public function withCookie($key, $value, $expire = 0, $path = null, $domain = null, $secure = false, $httponly = false) {
		setcookie($key, $value, $expire, $path, $domain, $secure, $httponly);
	}

	public function header($header, $value) {
		$this->headers[$header] = $value;
		return $this;
	}

	public function beginContent() {
		if($this->app->resolved('session')) {
			$this->app->session->sendCookie();
		}

		if(!$this->headersSent) {
			$this->sendHeaders();
		}
		$this->dataSent = true;
		return $this;
	}

	public function sendHeaders() {
		if($this->dataSent) {
			new \Exception('Cannot set headers after data has been sent.');
			return false;
		}

		$this->headersSent = true;
		foreach($this->headers as $header => $value) {
			header($header . ': ' . $value);
		}
	}

	public function status($code = 200) {
		http_response_code($code);
		return $this;
	}

	public function redirect($path) {
		$this->header('Location', $path);
		return $this;
	}

	public function send($string) {
		$this->beginContent();
		echo $string;
		return $this;
	}

	public function json($arr) {
		$this->header('Content-Type', 'application/json; charset=utf-8;')
			->beginContent();
		echo json_encode($arr);
		return $this;
	}

	public function readfile($path) {
		$this->beginContent();
		readfile($path);
		return $this;
	}

	public function end() {
		if(!$this->dataSent) {
			$this->beginContent();
		}
	}
}
