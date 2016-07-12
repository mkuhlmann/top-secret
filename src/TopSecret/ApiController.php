<?php

namespace TopSecret;

use \Areus\Response;
use \Areus\Request;

class ApiController extends \Areus\ApplicationModule {

	public function itemDelete($slug, Response $res) {
		$item = \R::findOne('item', 'slug = ?', [$slug]);
		if($item != null) {
			// delete physical files
			if(isset($item->path) && file_exists($this->app->storagePath.'/uploads'.$item->path)) {
				unlink($this->app->storagePath.'/uploads'.$item->path);
				if(file_exists($this->app->storagePath.'/thumb/'.$item->slug.'.jpg')) {
					unlink($this->app->storagePath.'/thumb/'.$item->slug.'.jpg');
				}
			}
			\R::trash($item);
			$res->json('ok');
		} else {
			$res->status(404)->json(['error' => '404 file not found']);
		}
	}

	private function normalizeSlug($text) {
		$text = preg_replace('~[^_\pL\d]+~u', '-', $text);
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
		$text = preg_replace('~[^-\w]+~', '', $text);
		$text = trim($text, '-');
		$text = preg_replace('~-+~', '-', $text);
		$text = strtolower($text);
		if (empty($text)) {
			return 'n-a';
		}

		$slug = $text;
		$i = 1;
		while(\R::count('item', 'slug = ?', [$slug]) > 0) {
			$slug = $text.'-'.$i++;
		}

		return $slug;
	}

	public function itemUpdate($slug, Request $req, Response $res) {
		$item = \R::findOne('item', 'slug = ?', [$slug]);
		if($item != null) {
			$_item = $req->input('item');
			$item->slug = $this->normalizeSlug($_item['slug']);
			\R::store($item);
			$res->json($item);
		} else {
			$res->status(404)->json(['error' => '404 file not found']);
		}
	}

	public function items(Request $req, Response $res) {
		list($sql, $params) = \TopSecret\Helper::buildQuery([
			['SELECT * FROM item'],
			[$req->query('type'), 'WHERE', 'type = ?'],
			['ORDER BY created_at DESC']
		]);
		$items = \R::getAll($sql, $params);
		$res->json($items);
	}

	public function postLink() {
		if(!isset($_POST['url'])) return;

		$url = $_POST['url'];
		$p = parse_url($url);
		$item = \R::dispense('item');
		$item->slug = \TopSecret\Helper::generateRandomString(6);
		$item->title = $p['scheme'].'://'.$p['host'];
		$item->type = 'url';
		$item->path = $url;
		$item->created_at = date('Y-m-d H:i:s');

		\R::store($item);

		echo json_encode(['slug' => $item->slug]);
	}

	public function postUpload(Response $res) {
		if(!isset($_FILES['file'])) return;

		if (move_uploaded_file($_FILES['file']['tmp_name'], $this->app->appPath.'/storage/'.$_FILES['file']['name'])) {
			$item = $this->handleUpload($this->app->appPath.'/storage/'.$_FILES['file']['name']);
			$res->json(['slug' => $item->slug, 'title' => $item->title, 'extension' => $item->extension, 'extensionIfImage' => ($item->type == 'image') ? '.'.$item->extension:'']);
		}
	}

	public function taskerUpload(Response $res) {
		header('Content-Type: text/html; charset=utf-8;');
		if(isset($_GET['fileName'])) {
			$pathInfo = pathinfo($_GET['fileName']);
			$targetPath = $this->app->appPath.'/storage/'.$pathInfo['basename'];

			$input = file_get_contents('php://input', 'r');
			file_put_contents($targetPath, $input);

			if($pathInfo['extension'] == 'png' && filesize($targetPath) > 200*1000) {
				$newTarget = $this->app->appPath.'/storage/'.$pathInfo['filename'].'.jpg';
				\TopSecret\Helper::resizeImage($targetPath, $newTarget, 100000);
				unlink($targetPath);
				$targetPath = $newTarget;
			}

			$item = $this->handleUpload($targetPath);

			$res->send($this->app->config->baseUrl.'/'.$item->slug);
		}
	}

	public function taskerLast(Response $res) {
		$item = \R::findOne('item', 'ORDER BY created_at DESC LIMIT 1');
		$res->send($this->app->config->baseUrl.'/'.$item->slug);
	}

	private function handleUpload($path) {
		$pathInfo = pathinfo($path);

		$uploadDir = date('Y/m').'/';
		$uploadPath = $this->app->storagePath.'/uploads/'.$uploadDir;
		if(!file_exists($uploadPath)) {
			mkdir($uploadPath, $this->app->config->defaultChmod, true);
		}

		$fileName = $pathInfo['basename'];
		for($i = 1; file_exists($uploadPath.$fileName); $i++) {
			$fileName = $i . '_' . $pathInfo['basename'];
		}
		$uploadPath .= '/'.$fileName;

		rename($path, $uploadPath);

		$item = \R::dispense('item');
		$item->slug = \TopSecret\Helper::generateRandomString(6);
		$item->title = $pathInfo['basename'];
		$item->name = $fileName;
		$item->path = '/'.$uploadDir.$fileName;
		$item->size = filesize($uploadPath);
		$item->mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $uploadPath);
		$item->extension = strtolower($pathInfo['extension']);
		$item->created_at = date('Y-m-d H:i:s');

		// type
		if(strpos($item->mime, 'image/') === 0) $item->type = 'image';
		if(strpos($item->mime, 'text/') === 0) $item->type = 'text';

		if(!$item->type) {
			$item->type = 'binary';
		}

		\R::store($item);

		return $item;
	}
}
