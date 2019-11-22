<?php

function app() {
	return \Areus\Application::getInstance();
}

function view($file, $data = []) {
	return app()->view->make($file, $data);
}

function viewResponse($file, $data = [], $code = 200) {
	return new \Areus\Http\ViewResponse(view($file, $data), $code);
}

if (! function_exists('e')) {
	function e($value) {
		return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
	}
}