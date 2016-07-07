<?php

date_default_timezone_set('UTC');
require 'vendor/autoload.php';

$appPath = dirname(__FILE__);
$publicPath = $appPath . '/public';


$app = new \Areus\Application();

$app->instance('appPath', $appPath);
$app->instance('publicPath', $publicPath);
$app->instance('storagePath', $appPath.'/storage');

$app->instance('config', new \Areus\Config($app->appPath.'/config'));
$app->registerSingleton('req', 'Areus\Request');
$app->registerSingleton('res', 'Areus\Response');
$app->registerSingleton('router', 'Areus\Router');

\R::setup('sqlite:'.$appPath.'/database.db');

function app() {
	return \Areus\Application::getInstance();
}

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

require 'routes.php';

$app->router->run();
