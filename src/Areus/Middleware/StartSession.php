<?php

declare(strict_types = 1);

namespace Areus\Middleware;

use Areus\Session\SessionManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Dflydev\FigCookies\FigResponseCookies;

class StartSession implements MiddlewareInterface {
	/** @var SessionManager */
	private $session;

	public function __construct(SessionManager $session) {
		$this->session = $session;
	}
	
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$this->session->start($request);

		$response = $handler->handle($request);

		$cookie = $this->session->generateCookie();

		if($cookie != null) {
			$response = FigResponseCookies::set($response, $cookie);
		}

		return $response;
	}
}
