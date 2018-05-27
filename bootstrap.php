<?php

date_default_timezone_set('UTC');
umask(0);

require 'vendor/autoload.php';

$appPath = dirname(__FILE__);


$app = new \Areus\Application($appPath);

$register = [
	'config' => 'Areus\Config',
	'router' => 'Areus\Router',
	'session' => 'Areus\Session',


	'viewFactory' => function($app) {
		return new \Areus\View\Factory($app->viewPath);
	},
	'request' => function($app) {
		return \Areus\Http\RequestFactory::fromGlobals();
	}
];

foreach($register as $alias => $concrete) {
	$app->singleton($alias, $concrete);
}

$app->alias('request', 'Psr\Http\Message\ServerRequestInterface');
$app->alias('request', 'Areus\Http\Request');

\R::setup('sqlite:'.$appPath.'/storage/database.db');

require 'helpers.php';
require 'filters.php';
require 'routes.php';

$middlewares = [
	\Areus\Middleware\Session::class,
	\Areus\Middleware\JsonPayload::class,
	\Areus\Middleware\Router::class
];

foreach($middlewares as &$val) {
	$val = $app->make($val);
}


$requestHandler = new \Relay\Relay($middlewares);
$response = $requestHandler->handle($app->request);

$emitter = new \Zend\Diactoros\Response\SapiStreamEmitter();
$emitter->emit($response);
