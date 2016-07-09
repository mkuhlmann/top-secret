<?php

namespace TopSecret;

class AdminController extends \Areus\ApplicationModule {
	public function index() {
		$this->app->res->beginContent();
		include $this->app->appPath.'/views/admin.php';
	}

	public function getConfig($res) {
		$config = $this->app->config->asArray();
		$config['defaultChmod'] = decoct($config['defaultChmod']);
		unset($config['adminPassword'], $config['loginSecret']);
		$res->json($config);
	}

	public function login($req, $res) {
		if(password_verify($req->post('p'), $this->app->config->adminPassword)) {
			$this->app->session->put('user_id', 1);
			$res->redirect('/tsa');
			return;
		}
		$res->redirect('/');
	}
}
