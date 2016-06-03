<?php

namespace TopSecret;

class ApiController {

	private function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	public function postLink() {
		if(!isset($_POST['url'])) return;

		$item = \R::dispense('item');
		$item->slug = $this->generateRandomString(6);
		$item->type = 'url';
		$item->path = $_POST['url'];
		$item->created_at = date('Y-m-d H:i:s');

		\R::store($item);

		echo json_encode(['slug' => $item->slug]);
	}

	public function postUpload($res) {
		if(!isset($_FILES['file'])) return;

		if (move_uploaded_file($_FILES['file']['tmp_name'], $appPath.'/'.$_FILES['file']['name'])) {
			$item = $this->handleUpload($this->app->appPath.'/'.$_FILES['file']['name']);
			$res->json(['slug' => $item->slug, 'title' => $item->title]);
		}
	}

	public function taskerUpload($res) {
		if(isset($_GET['fileName'])) {
			$fileName = basename($_GET['fileName']);
			$targetPath = $this->app->appPath.'/'.$fileName;

			$input = fopen('php://input', 'r');
			file_put_contents($targetPath, $input);

			$item = $this->handleUpload($targetPath);

			echo 'http://s.top-secret.xyz/'.$item->slug;
		}
	}

	private function handleUpload($path) {
		$pathInfo = pathinfo($path);

		$uploadDir = date('Y/m').'/';
		$uploadPath = $this->app->publicPath.'/'.$uploadDir;
		if(!file_exists($uploadDir)) {
			mkdir($uploadPath, 0777, true);
		}

		$fileName = $pathInfo['basename'];
		for($i = 1; file_exists($uploadPath.$fileName); $i++) {
			$fileName = $i . '_' . $pathInfo['basename'];
		}
		$uploadPath .= '/'.$fileName;

		rename($path, $uploadPath);

		$item = \R::dispense('item');
		$item->slug = generateRandomString(6);
		$item->title = $pathInfo['basename'];
		$item->name = $fileName;
		$item->path = '/'.$uploadDir;
		$item->size = filesize($uploadPath);
		$item->mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $uploadPath);
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
