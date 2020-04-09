<?php declare(strict_types=1);

use \RedBeanPHP\R;

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
$container->add(\Areus\Http\Request::class, $request)->setShared(true);

$container
	->add(\Areus\Application::class, $app)
	->addTag('app')
	->setShared(true);
$container
	->add(\Areus\Config::class)
	->addArgument([
		$app->path('/config/default.php'),
		$app->path('/config/local.php'), 
		$app->path('/storage/config.php')
	])
	->addTag('config')
	->setShared(true);
$container
	->add(\Areus\Http\Router::class)
	->addArgument(\Areus\Application::class)
	->addTag('router')
	->setShared(true);
$container
	->add(Areus\View\Factory::class)
	->addArgument($app->path('/views'))	
	->addTag('view')
	->setShared(true);
$container
	->add(\Psr\Http\Message\ServerRequestInterface::class, $request)
	->addTag('request')
	->setShared(true);

$container->addServiceProvider(Areus\Provider\SessionServiceProvider::class);

define('REDBEAN_MODEL_PREFIX',  '\\TopSecret\\Model\\'); 
R::setup('sqlite:'.$appPath.'/storage/database.db');
R::useFeatureSet( 'novice/latest' );

require 'helpers.php';
require 'filters.php';
require 'routes.php';

$middlewares = [
	\Areus\Middleware\StartSession::class,
	\Areus\Middleware\JsonPayload::class,
	\Areus\Middleware\Router::class
];

foreach($middlewares as &$val) {
	$val = $app->container->get($val);
}


$requestHandler = new \Areus\Http\Server\RequestHandler($middlewares);
$response = $requestHandler->handle($app->request);

$emitter = new \Laminas\HttpHandlerRunner\Emitter\SapiStreamEmitter();
$emitter->emit($response);
