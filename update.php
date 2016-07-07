<?php

echo 'Downloading latest composer.phar ...'.PHP_EOL.PHP_EOL;
copy('https://getcomposer.org/composer.phar', 'composer.phar');
exec('php composer.phar install');

$appPath = dirname(__FILE__);

if(!file_exists("$appPath/storage/uploads")) {
	mkdir("$appPath/storage/uploads", 0760, true);
}

if(file_exists("$appPath/public/2016")) {
	rename("$appPath/public/2016", "$appPath/storage/uploads/2016");
}
