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
		ob_start();
		extract($this->data);
		include $this->path;
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
}