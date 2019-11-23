<?php

declare(strict_types=1);

namespace Areus\Http\Response;

use Zend\Diactoros\Response\HtmlResponse;
use Areus\View\View;

class ViewResponse extends HtmlResponse {
	public function __construct(View $view, int $status = 200, array $headers = []) {
		parent::__construct(
			$view->render(),
			$status,
			$headers
        );
	}
}