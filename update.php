<?php

use \RedBeanPHP\R;

echo 'Downloading latest composer.phar ...'.PHP_EOL.PHP_EOL;
copy('https://getcomposer.org/composer.phar', 'composer.phar');
exec('php composer.phar install');

require 'vendor/autoload.php';

$appPath = dirname(__FILE__);

if(!file_exists("$appPath/storage/uploads")) {
	mkdir("$appPath/storage/uploads", 0766, true);
}

if(file_exists("$appPath/public/2016")) {
	rename("$appPath/public/2016", "$appPath/storage/uploads/2016");
}

if(file_exists("$appPath/public/thumbs")) {
	rename("$appPath/public/thumbs", "$appPath/storage/thumbs");
}

if(file_exists("$appPath/database.db")) {
	rename("$appPath/database.db", "$appPath/storage/database.db");
}


R::setup('sqlite:'.$appPath.'/storage/database.db');

$tag = R::count('tag');
if($tag == 0) {
	$item = R::dispense('item');
	$tag = R::dispense('tag');
	$tag->name = 'Unkategorisiert';
	$tag->color = 'grey';
	$item->sharedTagList[] = $tag;
	R::store($item);
	R::trash($item);
}


if(file_exists("$appPath/config/local.php") && !file_exists("$appPath/storage/config.php")) {
	rename("$appPath/config/local.php", "$appPath/storage/config.php");
}

echo 'Upgrade finished ...';
