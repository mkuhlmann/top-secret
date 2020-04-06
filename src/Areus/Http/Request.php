<?php

declare(strict_types=1);

namespace Areus\Http;

use Laminas\Diactoros\ServerRequest;

class Request extends ServerRequest {

	public function query($key, $default = null) {
		return $this->getQueryParams()[$key] ?? $default;
	}

	public function input($key, $default = null) {
		if($this->getMethod() == 'GET') {
			return $this->query($key, $default);
		}
		return $this->getParsedBody()[$key] ?? $default;
	}

	public function path() {
		return $this->getUri()->getPath();
	}

	public function header($key, $default = null) {
		return $this->getHeaders()[$key] ?? $default;
	}
}
