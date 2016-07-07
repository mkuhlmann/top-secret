<?php

$app->router->filter('auth.admin', function($req, $res) {
	if($req->cookie('tsa') != \TopSecret\Helper::getAdminCookie()) {
		$res->redirect('/');
		return false;
	}
});

$app->router->filter('auth.api', function($req, $res) {
	if($req->query('key') != app()->config->apiKey && $req->cookie('tsa') != \TopSecret\Helper::getAdminCookie()) {
		$res->status(401)->json(['error' => '401 unauthorized']);
		return false;
	}
});
