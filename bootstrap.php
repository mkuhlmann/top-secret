<?php

date_default_timezone_set('UTC');
umask(0);

require 'vendor/autoload.php';

$appPath = dirname(__FILE__);

$container = new \League\Container\Container();
$container->delegate(
	new \League\Container\ReflectionContainer()
);

$app = new \Areus\Application($appPath, $container);

$request = \Areus\Http\RequestFactory::fromGlobals();
$container->add(\Areus\Http\Request::class, $request)->setShared();

$container
	->add(\Areus\Application::class, $app)
	->addTag('app')
	->setShared();
$container
	->add(\Areus\Config::class)
	->addArgument($app->path('/config'))
	->addTag('config')
	->setShared();
$container
	->add(\Areus\Router::class)
	->addArgument(\Areus\Application::class)
	->addTag('router')
	->setShared();
$container
	->add(\Areus\Session::class)
	->addArgument(\Areus\Application::class)
	->addTag('session')
	->setShared();
$container
	->add(Areus\View\Factory::class)
	->addArgument($app->path('/views'))	
	->addTag('view')
	->setShared();
$container
	->add(\Psr\Http\Message\ServerRequestInterface::class, $request)
	->addTag('request')
	->setShared();

define('READBEAN_MODEL_PREFIX',  '\\TopSecret\\Model');
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
	$val = $app->container->get($val);
}


$requestHandler = new \Areus\Http\Server\RequestHandler($middlewares);
$response = $requestHandler->handle($app->request);

$emitter = new \Zend\HttpHandlerRunner\Emitter\SapiStreamEmitter();
$emitter->emit($response);
