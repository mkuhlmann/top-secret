<?php

namespace TopSecret;

class AdminController extends \Areus\ApplicationModule {
	private $allowedConfigKeys = ['defaultChmod', 'baseUrl', 'pageName', 'serveMethod', 'imageLibrary'];

	public function index() {
		$this->app->res->beginContent();
		include $this->app->appPath.'/views/admin.php';
	}

	public function logout($res) {
		$this->app->session->forget('user_id');
		$res->redirect('/');
	}

	public function getConfig($res) {
		$config = $this->app->config->asArray();
		$config = \Areus\Arr::only($config, $this->allowedConfigKeys);
		$config['defaultChmod'] = decoct($config['defaultChmod']);

		$res->json($config);
	}

	public function saveConfig($req, $res) {
		$config = $req->input('config', []);
		$config = \Areus\Arr::only($config, $this->allowedConfigKeys);
		$localConfig = [];
		if(file_exists($this->app->appPath.'/config/local.php')) {
			$localConfig = require $this->app->appPath.'/config/local.php';
		}
		$config['defaultChmod'] = octdec($config['defaultChmod']);
		$config = array_merge($localConfig, $config);

		file_put_contents($this->app->appPath.'/config/local.php', '<?php return '."\n\n".var_export($config, true).';');
		$res->json('ok');
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
