<?php

date_default_timezone_set('UTC');
umask(0);

require 'vendor/autoload.php';

$appPath = dirname(__FILE__);


$app = new \Areus\Application($appPath);

$register = [
	'config' => 'Areus\Config',
	'legacyRequest' => 'Areus\Request',
	'request' => 'Areus\Http\Request',
	'response' => 'Areus\Response',
	'router' => 'Areus\Router',
	'session' => 'Areus\Session'
];

foreach($register as $alias => $concrete) {
	$app->singleton($alias, $concrete);
}

$app->alias('Areus\Response', 'res');
$app->alias('Areus\Http\Request', 'req');

\R::setup('sqlite:'.$appPath.'/storage/database.db');

require 'helpers.php';
require 'filters.php';
require 'routes.php';

$app->router->run();
$app->res->end();
