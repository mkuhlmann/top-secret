<?php

return [
	'pageName' => 'Top Secret!',
	'baseUrl' => 'https://top-secret.xyz',
	'rbfluid' => false,
	'adminPassword' => 'generate with password_hash("xyz", PASSWORD_BCRYPT)', // then just enter this on the index page and the admin interface will magically open

	'loginSecret' => 'qwertzuiop', // used internally to forge admin cookie
	'apiKey' => '12457890qwertzuiop' // i think you use this to protect the upload api
];
