<?php

$app->router->get('/', 'TopSecret\FrontendController@index');
$app->router->post('/api/v1/link', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@postLink']);
$app->router->post('/api/v1/upload', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@postUpload']);
$app->router->post('/api/v1/tasker', ['before' => 'auth.api', 'uses' => 'TopSecret\ApiController@taskerUpload']);

$app->router->get('/thumbs?/{slug}([/\.].*)?', ['uses' => 'TopSecret\FrontendController@handleThumbSlug']);
$app->router->get('/{slug}([/\.].*)?', ['uses' => 'TopSecret\FrontendController@handleSlug']);

$app->router->get('/404', ['as' => '404', 'uses' => function($res) {
	$res->status(404);
	echo '404';
}]);
