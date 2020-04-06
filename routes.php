<?php

$router = $app->router;

///
/// BACKEND
///
$router->get('/', 'TopSecret\FrontendController@index');
$router->get('/tsa', ['before' => 'auth.admin', 'uses' => 'TopSecret\AdminController@index']);
$router->get('/tsa2', ['before' => 'auth.admin', 'uses' => 'TopSecret\AdminController@index2']);
$router->get('/tsa/logout', ['before' => 'auth.admin', 'uses' => 'TopSecret\AdminController@logout']);
$router->get('/tsa/Tasker.prf.xml', ['before' => 'auth.admin', 'uses' => 'TopSecret\AdminController@tasker']);
$router->get('/tsa/getConfig', ['before' => 'auth.admin', 'uses' => 'TopSecret\AdminController@getConfig']);
$router->post('/tsa/saveConfig', ['before' => 'auth.admin', 'uses' => 'TopSecret\AdminController@saveConfig']);
$router->get('/tsa/retentionDryRun', ['before' => 'auth.admin', 'uses' => 'TopSecret\AdminController@retentionDryRun']);
$router->post('/tsa/retentionRun', ['before' => 'auth.admin', 'uses' => 'TopSecret\AdminController@retentionRun']);
$router->post('/l', 'TopSecret\AdminController@login');

//
// API
//
$router->get('/api/v1/stats', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@stats']);

$router->get('/api/v1/items', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@items']);
$router->delete('/api/v1/item/{slug}', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@itemDelete']);
$router->put('/api/v1/item/{slug}', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@itemUpdate']);


$router->get('/api/v1/tags', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@tags']);
$router->get('/api/v2/tags', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@tagsv2']);
$router->post('/api/v1/tags', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@tagCreate']);
$router->put('/api/v1/tags/{tagId}', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@tagUpdate']);
$router->delete('/api/v1/tags/{tagId}', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@tagDelete']);

$router->post('/api/v1/link', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@postLink']);
$router->post('/api/v1/upload', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@postUpload']);

$router->post('/api/v1/tasker', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@taskerUpload']);
$router->get('/api/v1/taskerLast', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@taskerLast']);

///
/// Frontend
///
$router->get('/thumbs?/{slug}([/\.].*)?', ['uses' => 'TopSecret\FrontendController@handleThumbSlug']);
$router->get('/og/{slug}([/\.].*)?', ['uses' => 'TopSecret\FrontendController@openGraphSlug']);
$router->get('/{slug}([/\.].*)?', ['uses' => 'TopSecret\FrontendController@handleSlug']);

$router->get('/404', ['as' => '404', 'uses' => function(Response $res) {
	$res->status(404);
	echo '404';
}]);
