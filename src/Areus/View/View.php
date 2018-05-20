<?php

declare(strict_types=1);

namespace Areus\View;

class View {
	private $path;
	private $data;

	public function __construct(string $path, array $data = []) {
		$this->path = $path;
		$this->data = $data;
	}

	public function render() {
		extract($this->data);
		include $this->path;
	}
}