<?php

$app->router->filter('auth.admin', function(\Areus\Request $req,  \Areus\Response $res) {
	if(app()->session->get('user_id') !== 1) {
		$res->redirect('/');
		return false;
	}

	if($req->isMethod('post') && app()->session->token() != $req->input('_csrf')) {
		$res->header('Content-Type', 'text/plain')
			->status(403)
			->send('invalid csrf token')
			->end();
		return false;
	}
});

$app->router->filter('auth.api', function(\Areus\Request $req, \Areus\Response $res) {
	if($req->query('key') != app()->config->apiKey && app()->session->get('user_id') !== 1) {
		$res->status(401)->json(['error' => '401 unauthorized']);
		return false;
	}

	if($req->query('key') != app()->config->apiKey && $req->isMethod('post') && app()->session->token() != $req->input('_csrf')) {
		$res->header('Content-Type', 'text/plain')
			->status(403)
			->send('invalid csrf token')
			->end();
		return false;
	}
});
