<?php

date_default_timezone_set('UTC');
umask(0);

require 'vendor/autoload.php';

$appPath = dirname(__FILE__);


$app = new \Areus\Application($appPath);

$app->singleton('config', 'Areus\Config');

$app->singleton('request', 'Areus\Request');
$app->singleton('response', 'Areus\Response');
	$app->alias('response', 'res');
	$app->alias('request', 'req');
$app->singleton('router', 'Areus\Router');
$app->singleton('session', 'Areus\Session');

\R::setup('sqlite:'.$appPath.'/storage/database.db');

require 'helpers.php';
require 'filters.php';
require 'routes.php';

$app->router->run();
$app->res->end();
