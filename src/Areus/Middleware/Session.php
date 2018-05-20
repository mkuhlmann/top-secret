<?php

declare(strict_types = 1);

namespace Areus\Middleware;

use Areus\Application;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Dflydev\FigCookies\FigResponseCookies;

class Router implements MiddlewareInterface {
	private $app;

	public function __construct(Application $app) {
		$this->app = $app;
	}
	
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$response = $handler->handle($request);

		$cookie = $this->app->session->generateCookie();
		FigResponseCookies::set($response, $cookie);

		return $response;
	}
}