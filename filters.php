<?php

$app->router->filter('auth.admin', function($req, $res) {
	if(app()->session->get('user_id') !== 1) {
		$res->redirect('/');
		return false;
	}
});

$app->router->filter('auth.api', function($req, $res) {
	if($req->query('key') != app()->config->apiKey && app()->session->get('user_id') !== 1) {
		$res->status(401)->json(['error' => '401 unauthorized']);
		return false;
	}
});
