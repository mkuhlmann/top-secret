<?php

namespace TopSecret;

class AdminController extends \Areus\ApplicationModule {
	public function index() {
		include $this->app->appPath.'/views/admin.php';
	}

	public function getConfig($res) {
		$config = $this->app->config->asArray();
		$config['defaultChmod'] = decoct($config['defaultChmod']);
		unset($config['adminPassword'], $config['loginSecret']);
		$res->json($config);
	}
}
