<?php

namespace TopSecret;

use Areus\Http\Request;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\TextResponse;
use RedBeanPHP\R;

use TopSecret\Model\Item;

class ApiController extends \Areus\ApplicationModule
{
	public function stats()
	{
		$stats = R::getRow('SELECT count(id) as total_count, sum(size) as total_size FROM item');
		return new JsonResponse($stats);
	}

	public function itemDelete($slug)
	{
		$item = R::findOne('item', 'slug = ?', [$slug]);
		if ($item != null) {
			R::trash($item);
			return new JsonResponse('ok');
		} else {
			return new JsonResponse(['error' => '404 file not found'], 404);
		}
	}

	public function itemUpdate($slug, Request $req)
	{
		$item = R::findOne('item', 'slug = ?', [$slug]);
		if ($item != null) {
			$_item = $req->input('item');

			if ($_item['slug'] != null && $_item['slug'] != $item->slug) {
				$slug = \TopSecret\Helper::normalizeSlug($_item['slug']);
				$item->slug = Item::generateSlug($slug);
			}

			$tags = [];
			foreach (explode(',', $_item['tags']) as $tag) {
				$tag = R::findOne('tag', 'id = ?', [$tag]);
				if ($tag != null) $tags[] = $tag;
			}
			$item->sharedTagList = $tags;

			R::store($item);
			return new JsonResponse($item);
		} else {
			return new JsonResponse(['error' => '404 file not found'], 404);
		}
	}

	public function tagUpdate($tagId, Request $req)
	{
		$tag = R::findOne('tag', 'id = ?', [$tagId]);
		$_tag = $req->input('tag');

		$tag->name = $_tag['name'];
		$tag->color = $_tag['color'];

		R::store($tag);
		return new JsonResponse($tag);
	}

	public function tagDelete($tagId, Request $req)
	{
		$tag = R::findOne('tag', 'id = ?', [$tagId]);
		R::trash($tag);
		return new JsonResponse('ok');
	}

	public function tagCreate()
	{
		$tag = R::dispense('tag');
		$tag->name = 'Unbenannt';
		R::store($tag);
		return new JsonResponse($tag);
	}

	public function tags()
	{
		$tags = R::findAll('tag');
		return new JsonResponse($tags);
	}

	public function tagsV2()
	{
		$tags = R::findAll('tag');
		return new JsonResponse(array_values($tags));
	}

	public function items(Request $req)
	{
		$sql = 'FROM item i LEFT JOIN item_tag it ON it.item_id = i.id';
		$params = [];

		$where = [];

		if ($req->query('q')) {
			$where[] = 'i.title LIKE ? OR i.name LIKE ?';
			$params[] = '%' . $req->query('q') . '%';
			$params[] = '%' . $req->query('q') . '%';
		}

		if ($req->query('type')) {
			$where[] = 'i.type = ?';
			$params[] = $req->query('type');
		}

		if ($req->query('tags')) {
			$where[] = 'it.tag_id IN (' . str_repeat('?, ', count($req->query('tags')) - 1) . '?)';
			array_push($params, ...explode(',', $req->query('tags')));
		}

		if (count($where) > 0) {
			$sql .= ' WHERE ' . implode(' AND ', $where);
		}

		$itemsCount = R::getCell('SELECT COUNT(i.id) ' . $sql, $params);

		$sql .= ' GROUP BY i.id ORDER BY i.created_at DESC';

		if (is_numeric($req->query('limit')) && $req->query('limit') <= 200) {
			$sql .= ' LIMIT ?,?';
			array_push($params, (int)$req->query('limit', 20) * (int)($req->query('page', 1) - 1), $req->query('limit'));
		}

		$items = R::getAll('SELECT i.*, group_concat(it.tag_id) as tags ' . $sql, $params);
		return new JsonResponse(['items' => $items, 'total' => $itemsCount]);
	}

	public function postLink(Request $req)
	{
		$payload = $req->getParsedBody();

		if (!isset($payload['url'])) {
			return new JsonResponse(['error' => 'no url supplied'], 400);
		}

		$slug = $payload['slug'] ?? \TopSecret\Helper::generateSlug();
		if (Item::slugExists($slug)) {
			return new JsonResponse(['error' => 'slug already exists'], 400);
		}

		$url = $payload['url'];
		$p = parse_url($url);

		if (empty($p['host'])) {
			return new JsonResponse(['error' => 'url cant be empty'], 400);
		}

		$item = R::dispense('item');
		$item->slug = $slug;
		$item->title = $p['scheme'] . '://' . $p['host'];
		$item->type = 'url';
		$item->path = $url;
		$item->created_at = date('Y-m-d H:i:s');

		R::store($item);

		return new JsonResponse(['slug' => $item->slug]);
	}

	public function postUpload()
	{
		if (!isset($_FILES['file'])) return;

		if (move_uploaded_file($_FILES['file']['tmp_name'], $this->app->path('/storage/' . $_FILES['file']['name']))) {
			$item = $this->handleUpload($this->app->path('/storage/' . $_FILES['file']['name']));
			return new JsonResponse(['slug' => $item->slug, 'title' => $item->title, 'extension' => $item->extension, 'extensionIfImage' => ($item->type == 'image') ? '.' . $item->extension : '', 'item' => $item, 'baseUrl' => app()->config->baseUrl]);
		}
		return new JsonResponse(['error' => '500 internal server error'], 500);
	}

	public function taskerUpload()
	{
		if (isset($_GET['fileName'])) {
			$pathInfo = pathinfo($_GET['fileName']);
			$targetPath = $this->app->path('/storage/' . $pathInfo['basename']);

			$input = file_get_contents('php://input', 'r');
			file_put_contents($targetPath, $input);

			if ($pathInfo['extension'] == 'png' && filesize($targetPath) > 200 * 1000) {
				$newTarget = $this->app->path('/storage/') . $pathInfo['filename'] . '.jpg';
				\TopSecret\Helper::resizeImage($targetPath, $newTarget, 100000);
				unlink($targetPath);
				$targetPath = $newTarget;
			}

			$item = $this->handleUpload($targetPath);

			return new HtmlResponse($this->app->config->baseUrl . '/' . $item->slug);
		}
	}

	public function taskerLast()
	{
		$item = R::findOne('item', 'ORDER BY created_at DESC LIMIT 1');
		return new TextResponse($this->app->config->baseUrl . '/' . $item->slug);
	}

	private function handleUpload($path)
	{
		$pathInfo = pathinfo($path);

		$uploadDir = date('Y/m') . '/';
		$uploadPath = $this->app->path('/storage') . '/uploads/' . $uploadDir;
		if (!file_exists($uploadPath)) {
			mkdir($uploadPath, $this->app->config->defaultChmod, true);
		}

		$fileName = $pathInfo['basename'];
		for ($i = 1; file_exists($uploadPath . $fileName); $i++) {
			$fileName = $i . '_' . $pathInfo['basename'];
		}
		$uploadPath .= '/' . $fileName;

		rename($path, $uploadPath);

		$item = null;
		$request = $this->app->request;
		if ($request->input('overwriteSlug') != null) {
			$item = R::findOne('item', 'slug = ?', [$request->input('overwriteSlug')]);
		}

		if ($item == null) {
			$item = R::dispense('item');
			$item->slug = \TopSecret\Helper::generateSlug();

			if ($request->input('tags') !== null) {
				$tags = [];
				foreach (explode(',', $request->input('tags', '')) as $tag) {
					$tag = R::findOne('tag', 'id = ?', [$tag]);
					if ($tag != null) $tags[] = $tag;
				}
				$item->sharedTagList = $tags;
			}
		} else {
			if (isset($item->path) && file_exists($this->app->path('/storage') . '/uploads' . $item->path)) {
				unlink($this->app->path('/storage') . '/uploads' . $item->path);
				if (file_exists($this->app->path('/storage') . '/thumb/' . $item->slug . '.jpg')) {
					unlink($this->app->path('/storage') . '/thumb/' . $item->slug . '.jpg');
				}
			}
		}

		$item->title = $pathInfo['basename'];
		$item->name = $fileName;
		$item->path = '/' . $uploadDir . $fileName;
		$item->size = filesize($uploadPath);
		$item->mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $uploadPath);
		$item->extension = strtolower($pathInfo['extension']);
		$item->created_at = date('Y-m-d H:i:s');

		// type
		if (strpos($item->mime, 'image/') === 0) $item->type = 'image';
		if (strpos($item->mime, 'text/') === 0) $item->type = 'text';

		if (!$item->type) {
			$item->type = 'binary';
		}

		R::store($item);


		return $item;
	}
}
