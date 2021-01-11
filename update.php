<?php declare(strict_types=1);

use \RedBeanPHP\R;

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

if(file_exists("$appPath/config/local.php") && !file_exists("$appPath/storage/config.php")) {
	rename("$appPath/config/local.php", "$appPath/storage/config.php");
}


/*$db = \ParagonIE\EasyDB\Factory::create("sqlite:$appPath/storage/database.db");
$migrationRunner = new \TopSecret\Migration\MigrationRunner($db);
$migrationRunner->migrate();*/

echo 'Upgrade finished ...';
