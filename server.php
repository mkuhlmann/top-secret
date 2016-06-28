<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = urldecode($uri);
$requested = dirname(__FILE__).'/public/'.$uri;

if ($uri !== '/' and file_exists($requested))
{
	return false;
}

chdir(dirname(__FILE__).'/public');
require_once dirname(__FILE__).'/public/index.php';
