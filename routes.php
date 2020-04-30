<?php

/** @var Areus\Http\Router */
$router = $app->router;

///
/// BACKEND
///
$router->get('/', 'TopSecret\FrontendController@index');
$router->post('/l', 'TopSecret\AdminController@login');

$router->group(['before' => 'auth.admin'], function() use ($router) {
	$router->get('/tsa', 'TopSecret\AdminController@index2');
	$router->get('/tsa2', 'TopSecret\AdminController@index2');
	$router->get('/tsa/logout', 'TopSecret\AdminController@logout');
	$router->get('/tsa/Tasker.prf.xml', 'TopSecret\AdminController@tasker');
	$router->get('/tsa/getConfig', 'TopSecret\AdminController@getConfig');
	$router->post('/tsa/saveConfig', 'TopSecret\AdminController@saveConfig');
	$router->get('/tsa/retentionDryRun', 'TopSecret\AdminController@retentionDryRun');
	$router->post('/tsa/retentionRun', 'TopSecret\AdminController@retentionRun');
	$router->get('/tsa/sharexPreset', 'TopSecret\AdminController@downloadSharexPreset');
	$router->post('/tsa/regenerateApiKey', 'TopSecret\AdminController@regenerateApiKey');
});

//
// API
//

$router->group(['before' => 'auth.api'], function() use ($router) {
	$router->get('/api/v1/stats', 'TopSecret\ApiController@stats');

	$router->get('/api/v1/items', 'TopSecret\ApiController@items');
	$router->delete('/api/v1/item/{slug}', 'TopSecret\ApiController@itemDelete');
	$router->put('/api/v1/item/{slug}', 'TopSecret\ApiController@itemUpdate');
	
	
	$router->get('/api/v1/tags', 'TopSecret\ApiController@tags');
	$router->get('/api/v2/tags', 'TopSecret\ApiController@tagsv2');
	$router->post('/api/v1/tags', 'TopSecret\ApiController@tagCreate');
	$router->put('/api/v1/tags/{tagId}', 'TopSecret\ApiController@tagUpdate');
	$router->delete('/api/v1/tags/{tagId}', 'TopSecret\ApiController@tagDelete');
	
	$router->post('/api/v1/link', 'TopSecret\ApiController@postLink');
	$router->post('/api/v1/upload', 'TopSecret\ApiController@postUpload');
	
	$router->post('/api/v1/tasker', 'TopSecret\ApiController@taskerUpload');
	$router->get('/api/v1/taskerLast', 'TopSecret\ApiController@taskerLast');
});


///
/// Frontend
///
$router->get('/thumbs?/{slug}([/\.].*)?', ['uses' => 'TopSecret\FrontendController@handleThumbSlug']);
$router->get('/og/{slug}([/\.].*)?', ['uses' => 'TopSecret\FrontendController@openGraphSlug']);
$router->get('/{slug}([/\.].*)?', ['uses' => 'TopSecret\FrontendController@handleSlug']);

$router->get('/404', ['as' => '404', 'uses' => function() {
	return new \Laminas\Diactoros\Response\TextResponse('404 - Not Found', 404);
}]);

// DEBUG; dump routes
// print_r($router->dump()); exit;

