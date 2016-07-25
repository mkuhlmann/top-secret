<?php

namespace TopSecret;

use \Areus\Response;
use \Areus\Request;

class AdminController extends \Areus\ApplicationModule {
	private $allowedConfigKeys = ['defaultChmod', 'baseUrl', 'pageName', 'serveMethod', 'imageLibrary', 'countHitIfLoggedIn'];

	public function index() {
		$this->app->res->beginContent();
		include $this->app->appPath.'/views/admin.php';
	}

	public function logout(Response $res) {
		$this->app->session->forget('user_id');
		$res->redirect('/');
	}

	public function tasker(Response $res) {
		$res->header('Content-Type', 'application/xml')
			->beginContent();
		readfile($this->app->appPath.'/views/Tasker.prf.xml.php');
	}

	public function getConfig(Response $res) {
		$config = $this->app->config->asArray();
		$config = \Areus\Arr::only($config, $this->allowedConfigKeys);
		$config['defaultChmod'] = decoct($config['defaultChmod']);
		$config['countHitIfLoggedIn'] = ($config['countHitIfLoggedIn']) ? 'true' : 'false';

		$res->json($config);
	}

	public function saveConfig(Request $req,  Response $res) {
		$config = $req->input('config', []);
		$config = \Areus\Arr::only($config, $this->allowedConfigKeys);
		$localConfig = [];
		if(file_exists($this->app->appPath.'/config/local.php')) {
			$localConfig = require $this->app->appPath.'/config/local.php';
		}
		$config['defaultChmod'] = octdec($config['defaultChmod']);
		$config['countHitIfLoggedIn'] = $config['countHitIfLoggedIn'] == 'true';
		$config = array_merge($localConfig, $config);

		file_put_contents($this->app->appPath.'/config/local.php', '<?php return '."\n\n".var_export($config, true).';');
		sleep(1);
		$res->json('ok');
	}

	public function login(Request $req, Response $res) {
		if(password_verify($req->post('p'), $this->app->config->adminPassword)) {
			$this->app->session->put('user_id', 1);
			$res->redirect('/tsa');
			return;
		}
		$res->redirect('/');
	}
}
