<?php

namespace TopSecret;

use \Areus\Response;
use \Areus\Request;

class ApiController extends \Areus\ApplicationModule {
	public function stats(Response $res) {
		$stats = \R::getRow('SELECT count(id) as total_count, sum(size) as total_size FROM item');
		$res->json($stats);
	}

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

	public function itemUpdate($slug, Request $req, Response $res) {
		$item = \R::findOne('item', 'slug = ?', [$slug]);
		if($item != null) {
			$_item = $req->input('item');

			if($_item['slug'] != null && $_item['slug'] != $item->slug) {
				$item->slug = \TopSecret\Helper::normalizeSlug($_item['slug']);
			}

			$tags = [];
			foreach(explode(',', $_item['tags']) as $tag) {
				$tag = \R::findOne('tag', 'id = ?', [$tag]);
				if($tag != null) $tags[] = $tag;
			}
			$item->sharedTagList = $tags;

			\R::store($item);
			$res->json($item);
		} else {
			$res->status(404)->json(['error' => '404 file not found']);
		}
	}

	public function tagUpdate($tagId, Request $req, Response $res) {
		$tag = \R::findOne('tag', 'id = ?', [$tagId]);
		$_tag = $req->input('tag');

		$tag->name = $_tag['name'];
		$tag->color = $_tag['color'];

		\R::store($tag);
		$res->json($tag);
	}

	public function tagDelete($tagId, Request $req, Response $res) {
		$tag = \R::findOne('tag', 'id = ?', [$tagId]);
		\R::trash($tag);
		$res->json('ok');
	}

	public function tagCreate(Response $res) {
		$tag = \R::dispense('tag');
		$tag->name = 'Unbenannt';
		\R::store($tag);
		$res->json($tag);
	}

	public function tags(Response $res) {
		$tags = \R::findAll('tag');
		$res->json($tags);
	}

	public function items(Request $req, Response $res) {
		$sql = 'FROM item i LEFT JOIN item_tag it ON it.item_id = i.id';
		$params = [];

		$where = [];

		if($req->query('q')) {
			$where[] = 'i.title LIKE ? OR i.name LIKE ?';
			$params[] = '%'.$req->query('q').'%';
			$params[] = '%'.$req->query('q').'%';
		}

		if($req->query('type')) {
			$where[] = 'i.type = ?';
			$params[] = $req->query('type');
		}

		if($req->query('tags')) {
			$where[] = 'it.tag_id IN ('.str_repeat('?, ', count($req->query('tags'))-1) . '?)';
			array_push($params, ...explode(',', $req->query('tags')));
		}

		if(count($where) > 0) {
			$sql .= ' WHERE ' . implode(' AND ', $where);
		}

		$itemsCount = \R::getCell('SELECT COUNT(i.id) ' . $sql, $params);

		$sql .= ' GROUP BY i.id ORDER BY i.created_at DESC';

		if(is_numeric($req->query('limit')) && $req->query('limit') <= 200) {
			$sql .= ' LIMIT ?,?';
			array_push($params, $req->query('limit') * $req->query('page', 1)-1, $req->query('limit'));
		}

		$items = \R::getAll('SELECT i.*, group_concat(it.tag_id) as tags ' . $sql, $params);
		$res->json(['items' => $items, 'total' => $itemsCount]);
	}

	public function postLink(Response $res) {
		if(!isset($_POST['url'])) return;

		$url = $_POST['url'];
		$p = parse_url($url);
		$item = \R::dispense('item');
		$item->slug = \TopSecret\Helper::generateSlug();
		$item->title = $p['scheme'].'://'.$p['host'];
		$item->type = 'url';
		$item->path = $url;
		$item->created_at = date('Y-m-d H:i:s');

		\R::store($item);

		$res->json(['slug' => $item->slug]);
	}

	public function postUpload(Response $res) {
		if(!isset($_FILES['file'])) return;

		if (move_uploaded_file($_FILES['file']['tmp_name'], $this->app->appPath.'/storage/'.$_FILES['file']['name'])) {
			$item = $this->handleUpload($this->app->appPath.'/storage/'.$_FILES['file']['name']);
			$res->json(['slug' => $item->slug, 'title' => $item->title, 'extension' => $item->extension, 'extensionIfImage' => ($item->type == 'image') ? '.'.$item->extension:'', 'item' => $item]);
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

		$item = null;
		if($this->app->req->input('overwriteSlug') != null) {
			$item = \R::findOne('item', 'slug = ?', [$this->app->req->input('overwriteSlug')]);
		}

		if($item == null) {
			$item = \R::dispense('item');
			$item->slug = \TopSecret\Helper::generateSlug();

			if($this->app->req->input('tags') !== null) {
				$tags = [];
				foreach(explode(',', $this->app->req->input('tags', '')) as $tag) {
					$tag = \R::findOne('tag', 'id = ?', [$tag]);
					if($tag != null) $tags[] = $tag;
				}
				$item->sharedTagList = $tags;
			}
		} else {
			if(isset($item->path) && file_exists($this->app->storagePath.'/uploads'.$item->path)) {
				unlink($this->app->storagePath.'/uploads'.$item->path);
				if(file_exists($this->app->storagePath.'/thumb/'.$item->slug.'.jpg')) {
					unlink($this->app->storagePath.'/thumb/'.$item->slug.'.jpg');
				}
			}
		}

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
