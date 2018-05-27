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
$app->router->get('/tsa/retentionDryRun', ['before' => 'auth.admin', 'uses' => 'TopSecret\AdminController@retentionDryRun']);
$app->router->post('/tsa/retentionRun', ['before' => 'auth.admin', 'uses' => 'TopSecret\AdminController@retentionRun']);
$app->router->post('/l', 'TopSecret\AdminController@login');

//
// API
//
$app->router->get('/api/v1/stats', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@stats']);

$app->router->get('/api/v1/items', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@items']);
$app->router->delete('/api/v1/item/{slug}', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@itemDelete']);
$app->router->put('/api/v1/item/{slug}', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@itemUpdate']);


$app->router->get('/api/v1/tags', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@tags']);
$app->router->post('/api/v1/tags', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@tagCreate']);
$app->router->put('/api/v1/tags/{tagId}', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@tagUpdate']);
$app->router->delete('/api/v1/tags/{tagId}', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@tagDelete']);

$app->router->post('/api/v1/link', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@postLink']);
$app->router->post('/api/v1/upload', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@postUpload']);

$app->router->post('/api/v1/tasker', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@taskerUpload']);
$app->router->get('/api/v1/taskerLast', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@taskerLast']);

///
/// Frontend
///
$app->router->get('/thumbs?/{slug}([/\.].*)?', ['uses' => 'TopSecret\FrontendController@handleThumbSlug']);
$app->router->get('/{slug}([/\.].*)?', ['uses' => 'TopSecret\FrontendController@handleSlug']);

$app->router->get('/404', ['as' => '404', 'uses' => function(Response $res) {
	$res->status(404);
	echo '404';
}]);
