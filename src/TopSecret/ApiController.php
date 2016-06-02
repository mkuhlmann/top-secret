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

	public function linkCreate() {
		if(!isset($_POST['url'])) return;

		$item = R::dispense('item');
		$item->slug = $this->generateRandomString(6);
		$item->type = 'url';
		$item->path = $_POST['url'];
		$item->created_at = date('Y-m-d H:i:s');

		R::store($item);

		echo json_encode(['slug' => $item->slug]);
	}
}
