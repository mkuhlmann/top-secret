<?php

echo 'Downloading latest composer.phar ...'.PHP_EOL.PHP_EOL;
copy('https://getcomposer.org/composer.phar', 'composer.phar');
exec('php composer.phar install');
