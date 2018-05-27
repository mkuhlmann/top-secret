<?php

namespace TopSecret;

use Areus\Http\Request as Request;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class AdminController extends \Areus\ApplicationModule {
	private $allowedConfigKeys = 	['defaultChmod', 'baseUrl', 'pageName', 'serveMethod',
	 								'imageLibrary', 'countHitIfLoggedIn', 'slugLength',
									'slugCharset', 'piwikEnableTracking', 'piwikIdSite',
									'piwikUrl', 'piwikAuthToken', 'retentionDays', 'retentionOnlyUntagged'];

	public function index() {
		return viewResponse('admin');
	}

	public function logout() {
		$this->app->session->forget('user_id');
		return new RedirectResponse('/');
	}

	public function tasker() {
		return (new Response(new Stream($this->app->appPath.'/views/Tasker.prf.xml.php')))
			->withHeader('Content-Type', 'application/xml');
	}

	public function getConfig() {
		$config = $this->app->config->asArray();
		$config = \Areus\Arr::only($config, $this->allowedConfigKeys);
		$config['defaultChmod'] = decoct($config['defaultChmod']);
		$config['countHitIfLoggedIn'] = ($config['countHitIfLoggedIn']) ? 'true' : 'false';

		return new JsonResponse($config);
	}

	public function saveConfig(Request $req) {
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
		return new JsonResponse('ok');
	}

	public function login(Request $request) {
		$password = $request->getParsedBody()['p'];

		if(password_verify($password, $this->app->config->adminPassword)) {
			$this->app->session->put('user_id', 1);
			return new RedirectResponse('/tsa');
		}
		return new RedirectResponse('/');
	}

	private function retentionSql() {
		$sql = 'FROM item i LEFT JOIN item_tag it ON it.item_id = i.id';
		$params = [];

		$sql .= ''
			. ' WHERE (i.last_hit_at IS NULL AND julianday() - julianday(i.created_at) > ?)'
			. ' OR (julianday() - julianday(i.last_hit_at) > 60)'
			. ' AND it.id IS NULL';

		$params[] = $this->app->config->get('retentionDays');

		return [$sql, $params];
	}

	public function retentionDryRun() {
		
		list($sql, $params) = $this->retentionSql();

		$items = \R::getCell('SELECT COUNT(i.id) ' . $sql, $params);

		return new JsonResponse(['deletedItems' => $items]);
	}

	public function retentionRun() {
		list($sql, $params) = $this->retentionSql();

		$items = \R::getAll('SELECT i.* ' . $sql, $params);

		foreach($items as $item) {
			\TopSecret\Helper::itemDelete($item['slug']);
		}

		return new JsonResponse(['success' => true]);
	}
}
