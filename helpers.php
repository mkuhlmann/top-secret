<?php

function app() {
	return \Areus\Application::getInstance();
}

function view($file, $data = []) {
	return \TopSecret\Helper::renderView($file, $data);
}

function e($value) {
	return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
}
