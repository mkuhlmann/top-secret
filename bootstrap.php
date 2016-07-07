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

require 'filters.php';
require 'routes.php';

$app->router->run();
$app->res->end();
