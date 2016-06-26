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

	public function handleThumbSlug($slug, $res) {
		$item = \R::findOne('item', 'slug = ?', [$slug]);
		if($item == null) {
			$res->status(404)->json(['error' => '404 file not found']);
			return;
		}

		if($item->type != 'image') {
			$res->redirect('Location: /' . $item->slug);
			return;
		}

		$thumbPath = $this->app->publicPath.'/thumb/'.$item->slug.'.jpg';
		if(!file_exists($thumbPath)) {
			if(!file_exists($this->app->publicPath.'/thumb')) {
				mkdir($this->app->publicPath.'/thumb', 0777, true);
			}
			\TopSecret\Helper::resizeImage($this->app->publicPath.$item->path, $thumbPath, 300);
		}
		$res->redirect('/thumb/'.$item->slug.'.jpg');
	}

	public function index() {
		include $this->app->appPath.'/views/index.php';
	}
}
