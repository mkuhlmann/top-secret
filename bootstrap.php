<?php
date_default_timezone_set('UTC');

require 'router.php';
require 'r.php';

$appPath = dirname(__FILE__);
$publicPath = $appPath . '/public';

R::setup('sqlite:'.$appPath.'/database.db');

function generateRandomString($length = 10) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

require 'dispatch.php';
