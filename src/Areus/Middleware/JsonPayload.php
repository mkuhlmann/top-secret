<?php

declare(strict_types=1);

namespace Areus\Middleware;

use Exception;
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
				$parsedRequest = $request->withParsedBody($this->parse($request->getBody()));
				$request =& $parsedRequest; // set by reference, so the parsed body gets passed down the handler
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
			throw new Exception(json_last_error_msg());
		}

		return $data ?? [];
	}
}
