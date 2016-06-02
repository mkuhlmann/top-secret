<?php

function doUpload($path) {
	global $publicPath;

	$pathInfo = pathinfo($path);

	$uploadDir = date('Y/m').'/';
	$uploadPath = $publicPath.'/'.$uploadDir;
	if(!file_exists($uploadDir)) {
		mkdir($uploadPath, 0777, true);
	}

	$fileName = $pathInfo['basename'];
	for($i = 1; file_exists($uploadPath.$fileName); $i++) {
		$fileName = $i . '_' . $pathInfo['basename'];
	}
	$uploadPath .= '/'.$fileName;

	rename($path, $uploadPath);

	$item = R::dispense('item');
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

	R::store($item);

	return $item;
}

Router::route('/api/v1/upload', function() {
	if(!isset($_GET['key']) && $_GET['key'] != '259dae02edeb362c272fd65dfccef66e') {
		http_response_code(401);
		echo json_encode(['error' => '401 unauthorized']);
		return;
	}

	global $appPath;
	if(!isset($_FILES['file'])) return;

	if (move_uploaded_file($_FILES['file']['tmp_name'], $appPath.'/'.$_FILES['file']['name'])) {
		$item = doUpload($appPath.'/'.$_FILES['file']['name']);
		echo json_encode(['slug' => $item->slug, 'title' => $item->title]);
	}
});

Router::route('/api/v1/tasker', function() {
	if(!isset($_GET['key']) && $_GET['key'] != '259dae02edeb362c272fd65dfccef66e') {
		http_response_code(401);
		echo json_encode(['error' => '401 unauthorized']);
		return;
	}

	global $appPath;
	if(isset($_GET['fileName'])) {
		$fileName = basename($_GET['fileName']);
		$targetPath = $appPath.'/'.$fileName;

		$input = fopen('php://input', 'r');
		file_put_contents($targetPath, $input);

		$item = doUpload($targetPath);

		echo 'http://s.top-secret.xyz/'.$item->slug;
	}
});

Router::route('/api/v1/link', function() {
	if(!isset($_GET['key']) && $_GET['key'] != '259dae02edeb362c272fd65dfccef66e') {
		http_response_code(401);
		echo json_encode(['error' => '401 unauthorized']);
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
	} else if(true || isset($_GET['raw'])) {
		header('Location: ' . $item->path);
	}
});

$r = explode('?', $_SERVER['REQUEST_URI'])[0];
if(isset($_GET['r'])) {
	$r = $_GET['r'];
}
Router::execute($r);
