<?php

/**
 * PLEASE DO NOT MODIFY THIS FILE, BUT INSTEAD CREATE A 'storage/config.php' TO OVERRIDE CONFIGURATION.
 **/
return [
	'environment' => 'production',

	'pageName' => 'Top Secret!',
	'baseUrl' => 'https://top-secret.site',

	'adminPassword' => '$2y$10$2nlqKDYSbmYNgS8JmNHV6ufJ1UoEzU3z7281Gc/zDo.y.V8AJ1RY6', // = "password#" then just enter this on the index page and the admin interface will magically open
	'loginSecret' => 'qwertzuiop', // used internally to forge admin cookie
	'apiKey' => '12457890qwertzuiop', // i think you use this to protect the upload api

	'slugLength' => 6,
	'slugCharset' => '023456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ',

	'defaultChmod' => 0777,
	'serveMethod' => 'php', // php or nginx
	'imageLibrary' => 'gd', // gd or imagemagick

	'redirectFileName' => false,
	'countHitIfLoggedIn' => true,

	'piwikEnableTracking' => false,
	'piwikUrl' => 'https://piwik.top-secret.site/piwik.php',
	'piwikIdSite' => 1,
	'piwikAuthToken' => '',

	'behindTrustedProxy' => false,

	'retentionDays' => 60,
	'retentionOnlyUntagged' => true,

	'richPreview' => true,
	'disableCacheHeaders' => false,

	'areus' => [
		// session configuration (same as https://github.com/laravel/laravel/blob/master/config/session.php)
		'session' => [
			'driver' => 'file',
			'lifetime' => 24 * 60, // in minutes
			'lottery' => [2, 100],
			'cookie' => 'areus_session',
			'path' => '/',
			'domain' => null,
			'secure' => false,
			'http_only' => true
		]
	]
];
