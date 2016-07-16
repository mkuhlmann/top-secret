<?php

///
/// BACKEND
///
$app->router->get('/', 'TopSecret\FrontendController@index');
$app->router->get('/tsa', ['before' => 'auth.admin', 'uses' => 'TopSecret\AdminController@index']);
$app->router->get('/tsa/logout', ['before' => 'auth.admin', 'uses' => 'TopSecret\AdminController@logout']);
$app->router->get('/tsa/Tasker.prf.xml', ['before' => 'auth.admin', 'uses' => 'TopSecret\AdminController@tasker']);
$app->router->get('/tsa/getConfig', ['before' => 'auth.admin', 'uses' => 'TopSecret\AdminController@getConfig']);
$app->router->post('/tsa/saveConfig', ['before' => 'auth.admin', 'uses' => 'TopSecret\AdminController@saveConfig']);
$app->router->post('/l', 'TopSecret\AdminController@login');

//
// API
//
$app->router->get('/api/v1/items', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@items']);
$app->router->post('/api/v1/item/{slug}/delete', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@itemDelete']);
$app->router->post('/api/v1/item/{slug}/update', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@itemUpdate']);

$app->router->post('/api/v1/link', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@postLink']);
$app->router->post('/api/v1/upload', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@postUpload']);

$app->router->post('/api/v1/tasker', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@taskerUpload']);
$app->router->get('/api/v1/taskerLast', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@taskerLast']);

///
/// Frontend
///
$app->router->get('/thumbs?/{slug}([/\.].*)?', ['uses' => 'TopSecret\FrontendController@handleThumbSlug']);
$app->router->get('/{slug}([/\.].*)?', ['uses' => 'TopSecret\FrontendController@handleSlug']);

$app->router->get('/404', ['as' => '404', 'uses' => function($res) {
	$res->status(404);
	echo '404';
}]);
