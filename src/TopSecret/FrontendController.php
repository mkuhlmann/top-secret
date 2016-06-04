<?php

namespace TopSecret;

class FrontendController extends \Areus\ApplicationModule {
	public function handleSlug($slug, $res) {
		$item = \R::findOne('item', 'slug = ?', [$slug]);
		if($item == null) {
			$res->status(404)->json(['error' => '404 file not found']);
			return;
		}

		if($item->type == 'url') {
			header('Location: ' . $item->path);
		} else if(true || isset($_GET['raw'])) {
			header('Location: ' . $item->path);
		}
	}

	public function index() {
		include $this->app->appPath.'/views/index.php';
	}
}
