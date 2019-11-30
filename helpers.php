<?php declare(strict_types=1);

function app() : Areus\Application {
	return \Areus\Application::getInstance();
}

function view($file, $data = []) {
	return app()->view->make($file, $data);
}

function viewResponse($file, $data = [], $code = 200) {
	return new \Areus\Http\Response\ViewResponse(view($file, $data), $code);
}

if (! function_exists('e')) {
	function e($value) {
		return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
	}
}