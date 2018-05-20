<?php

declare(strict_types=1);

namespace Areus\Middleware;

use Areus\Application;

use Psr\Http\Message\StreamInterface;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class JsonPayload implements MiddlewareInterface {

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		if(!$request->getParsedBody()) {
			$contentType = $request->getHeaderLine('Content-Type');

			if(stripos($contentType, 'application/json') === 0) {
				$request = $request->withParsedBody($this->parse($request->getBody()));
				app()->request = $request; // small hack
			}
		}
		return $handler->handle($request);
	}

	private function parse(StreamInterface $stream) : array {
		$json = trim((string) $stream);
		if ($json === '') {
			return [];
		}

		$data = json_decode($json, true, 512);

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new DomainException(json_last_error_msg());
		}

		return $data ?? [];
	}
}