<?php

namespace Areus;

class Response extends \Areus\ApplicationModule {
	protected $headers = [];
	protected $headerAlias = [];
	protected $cookies = [];
	private $headersSent = false;
	private $dataSent = false;

	public function withCookie($key, $value, $expire = 0, $path = null, $domain = null, $secure = false, $httponly = false) {
		$this->cookies[$key] = [$key, $value, $expire, $path, $domain, $secure, $httponly];
	}

	public function header($header, $value, $onlyIfNotSet = false) {
		$lowerCaseHeader = strtolower($header);
		if(isset($this->headerAlias[$lowerCaseHeader])) {
			$header = $this->headerAlias[$lowerCaseHeader];
			if($onlyIfNotSet) {
				return $this;
			}
		} else {
			$this->headerAlias[$lowerCaseHeader] = $header;
		}
		$this->headers[$header] = $value;
		return $this;
	}

	public function beginContent() {
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

		foreach($this->cookies as $key => $value) {
			setcookie(...$value);
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

	public function download($path, $name = null, array $headers = [], $disposition = 'attachment') {
		$mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
		$name = ($name == null) ? basename($path) : $name;
		$size = filesize($path);
		$lastModified = filemtime($path);

		$this
			->header('Content-Length', $size)
			->header('Last-Modified', gmdate('r', $lastModified))
			->header('Content-Disposition', $disposition . '; filename="'.rawurlencode($name).'"')
			->readfile($path);
	}

	public function readfile($path) {
		$this->beginContent();
		readfile($path);
		return $this;
	}

	public function end() {
		if($this->app->resolved('session')) {
			$this->app->session->save();
		}

		if(!$this->dataSent) {
			$this->beginContent();
		}
	}
}
