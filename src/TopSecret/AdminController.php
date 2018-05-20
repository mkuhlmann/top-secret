<?php

namespace TopSecret;

use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\RedirectResponse;

class AdminController extends \Areus\ApplicationModule {
	private $allowedConfigKeys = 	['defaultChmod', 'baseUrl', 'pageName', 'serveMethod',
	 								'imageLibrary', 'countHitIfLoggedIn', 'slugLength',
									'slugCharset', 'piwikEnableTracking', 'piwikIdSite',
									'piwikUrl', 'piwikAuthToken'];

	public function index() {
		return viewResponse('admin');
	}

	public function logout(Response $res) {
		$this->app->session->forget('user_id');
		return new RedirectResponse('/');
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
		$config['slugLength'] = intval($config['slugLength']);
		$config = array_merge($localConfig, $config);

		file_put_contents($this->app->appPath.'/config/local.php', '<?php return '."\n\n".var_export($config, true).';');
		sleep(2);
		$res->json('ok');
	}

	public function login(Request $request) {
		$password = $request->getParsedBody()['p'];

		if(password_verify($password, $this->app->config->adminPassword)) {
			$this->app->session->put('user_id', 1);
			return new RedirectResponse('/tsa');
		}
		return new RedirectResponse('/');
	}
}
