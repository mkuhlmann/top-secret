<?php

function app() {
	return \Areus\Application::getInstance();
}

function view($file, $data = []) {
	return \TopSecret\Helper::renderView($file, $data);
}
