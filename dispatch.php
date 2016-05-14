<?php

Router::route('/api/v1/upload', function() {
	if(!isset($_GET['key']) && $_GET['key'] != '259dae02edeb362c272fd65dfccef66e') return;

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

		R::store($item);

		echo json_encode(['slug' => $item->slug, 'title' => $item->title]);
	}
});

Router::route('/([\w]*)(/.*)?', function($slug) {
	$item = R::findOne('item', 'slug = ?', [$slug]);
	if($item == null) {
		http_response_code(404);
		echo '404';
		return;
	}

	if($item->type == 'image') {
		header('Location: ' . $item->path);
	}
});

Router::execute($_SERVER['PATH_INFO']);
