<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\Response\JsonResponse;

$app->router->filter('auth.admin', function(Request $request) {
	if(app()->session->get('user_id') !== 1) {
		return new RedirectResponse('/');
	}


	if($request->getMethod() == 'POST' && app()->session->token() != $request->getParsedBody()['_csrf']) {
		return new JsonResponse(['error' => '403 invalid csrf token'], 403);
	}
});

$app->router->filter('auth.api', function(Request $request) {
	$key = $request->getQueryParams()['key'] ?? null;
	
	if($request->hasHeader('Authorization')) {
		$key = str_replace('Bearer ', '', $request->getHeader('Authorization')[0]);
	}

	if($key !== app()->config->apiKey && app()->session->get('user_id') !== 1) {
		return new JsonResponse(['error' => '401 unauthorized'], 401);
	}

	if($key != app()->config->apiKey && !$request->getMethod() == 'GET' && app()->session->token() != $request->getParsedBody()['_csrf']) {
		return new JsonResponse(['error' => '403 invalid csrf token'], 403);
	}
});
