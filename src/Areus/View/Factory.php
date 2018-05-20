<?php

declare(strict_types=1);

namespace Areus\View;

class Factory {
	private $viewPath;

	public function __construct(string $viewPath) {
		$this->viewPath = $viewPath;
	}

	public function make(string $view, array $data = []) {
		$view .= '.php';

		if(file_exists($this->viewPath."/$view")) {
			return new View($this->viewPath."/$view", $data);
		} else {
			throw new \Exception('View not found');
		}
	}
}