<?php

///
/// BACKEND
///
$app->router->get('/', 'TopSecret\FrontendController@index');
$app->router->get('/tsa', ['before' => 'auth.admin', 'uses' => 'TopSecret\FrontendController@admin']);
$app->router->post('/l', 'TopSecret\FrontendController@login');

//
// API
//
$app->router->get('/api/v1/items', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@items']);
$app->router->post('/api/v1/item/{slug}/delete', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@itemDelete']);

$app->router->post('/api/v1/link', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@postLink']);
$app->router->post('/api/v1/upload', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@postUpload']);

$app->router->post('/api/v1/tasker', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@taskerUpload']);
$app->router->get('/api/v1/taskerLast', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@taskerLast']);

///
/// Frontend
///
$app->router->get('/f/{slug}', ['uses' => 'TopSecret\FrontendController@seafile']);
$app->router->get('/thumbs?/{slug}([/\.].*)?', ['uses' => 'TopSecret\FrontendController@handleThumbSlug']);
$app->router->get('/{slug}([/\.].*)?', ['uses' => 'TopSecret\FrontendController@handleSlug']);

$app->router->get('/404', ['as' => '404', 'uses' => function($res) {
	$res->status(404);
	echo '404';
}]);
