<?php

date_default_timezone_set('UTC');
require 'vendor/autoload.php';

$appPath = dirname(__FILE__);
$publicPath = $appPath . '/public';



$app = new \Areus\Application();

$app->instance('appPath', $appPath);
$app->instance('publicPath', $publicPath);

$app->registerSingleton('req', 'Areus\Request');
$app->registerSingleton('res', 'Areus\Response');
$app->registerSingleton('router', 'Areus\Router');

R::setup('sqlite:'.$appPath.'/database.db');

$app->router->filter('auth.api', function($req, $res) {
	if($req->query('key') != '259dae02edeb362c272fd65dfccef66e') {
		$res->status(401)->json(['error' => '401 unauthorized']);
		return false;
	}
});

$app->router->get('/api/v1/link', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@linkCreate']);
$app->router->get('/{slug}(/.*)?', ['uses' => 'TopSecret\FrontendController@handleSlug']);

$app->router->run();
