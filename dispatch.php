<?php

Router::route('/api/v1/upload', function() {
	if(!isset($_GET['key']) && $_GET['key'] != '259dae02edeb362c272fd65dfccef66e') {
		http_response_code(401);
		echo json_encode(['error' => '401 unauthorized'])
		return;
	}

	global $publicPath;

	if(!isset($_FILES['file'])) return;

	$uploadDir = $publicPath.'/'.date('Y/m').'/';
	if(!file_exists($uploadDir)) {
		mkdir($uploadDir, 0777, true);
	}

	$fileName = $_FILES['file']['name'];
	for($i = 1; file_exists($uploadDir.$fileName); $i++) {
		$fileName = $i . '_' . $_FILES['file']['name'];
	}

	if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir.$fileName)) {
		$item = R::dispense('item');
		$item->slug = generateRandomString(6);
		$item->title = $_FILES['file']['name'];
		$item->name = $fileName;
		$item->path = '/'.date('Y/m').'/'.$fileName;
		$item->size = filesize($uploadDir.$fileName);
		$item->mime = $_FILES['file']['type'];
		$item->created_at = date('Y-m-d H:i:s');

		// type
		if(strpos($item->mime, 'image/') === 0) $item->type = 'image';
		if(strpos($item->mime, 'text/') === 0) $item->type = 'text';

		if(!$item->type) {
			$item->type = 'binary';
		}

		R::store($item);

		echo json_encode(['slug' => $item->slug, 'title' => $item->title]);
	}
});

Router::route('/api/v1/link', function() {
	if(!isset($_GET['key']) && $_GET['key'] != '259dae02edeb362c272fd65dfccef66e') {
		http_response_code(401);
		echo json_encode(['error' => '401 unauthorized'])
		return;
	}

	if(!isset($_POST['url'])) return;

	$item = R::dispense('item');
	$item->slug = generateRandomString(6);
	$item->type = 'url';
	$item->path = $_POST['url'];
	$item->created_at = date('Y-m-d H:i:s');

	R::store($item);

	echo json_encode(['slug' => $item->slug]);
});

Router::route('/([\w]*)(/.*)?', function($slug) {
	$item = R::findOne('item', 'slug = ?', [$slug]);
	if($item == null) {
		http_response_code(404);
		echo json_encode(['error' => '404 file not found']);
		return;
	}

	if($item->type == 'url') {
		header('Location: ' . $item->path);
	} else if(true) {
		header('Location: ' . $item->path);
	}
});

$r = explode('?', $_SERVER['REQUEST_URI'])[0];
if(isset($_GET['r'])) {
	$r = $_GET['r'];
}
Router::execute($r);
