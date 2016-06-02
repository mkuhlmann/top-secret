<?php

namespace Areus;

class Response {
	public function status($code = 200) {
		http_response_code($code);
		return $this;
	}

	public function json($arr) {
		header('Content-Type: application/json; charset=utf-8;');
		echo json_encode($arr);
	}
}
