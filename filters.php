<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Response\JsonResponse;

$app->router->filter('auth.admin', function(Request $request) {
	if(app()->session->get('user_id') !== 1) {
		return new RedirectResponse('/');
	}


	if($request->getMethod() == 'POST' && app()->session->token() != $request->getParsedBody()['_csrf']) {
		$res->header('Content-Type', 'text/plain')
			->status(403)
			->send('invalid csrf token')
			->end();
		return false;
	}


});

$app->router->filter('auth.api', function(Request $request) {
	if($request->getQueryParams()['key'] != app()->config->apiKey && app()->session->get('user_id') !== 1) {
		return new JsonResponse(['error' => '401 unauthorized'], 401);
	}
	if($request->getQueryParams()['key'] != app()->config->apiKey && !$request->getMethod() == 'GET' && app()->session->token() != $request->getParsedBody()['_csrf']) {
		$res->header('Content-Type', 'text/plain')
			->status(403)
			->send('invalid csrf token')
			->end();
		return false;
	}
});
