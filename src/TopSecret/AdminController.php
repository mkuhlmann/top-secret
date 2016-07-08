<?php

namespace TopSecret;

class AdminController extends \Areus\ApplicationModule {
	public function index() {
		include $this->app->appPath.'/views/admin.php';
	}

	public function getConfig($res) {
		$res->json($this->app->config->asArray());
	}
}
