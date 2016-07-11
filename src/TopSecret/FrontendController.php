<?php

namespace TopSecret;

use \Areus\Request;
use \Areus\Response;

class FrontendController extends \Areus\ApplicationModule {
	public function handleSlug($slug, Request $req, Response $res) {
		$item = \R::findOne('item', 'slug = ?', [$slug]);
		if($item == null) {
			$res->status(404)->json(['error' => '404 file not found']);
			return;
		}

		if($item->type != 'url' && $this->app->config->redirectFileName) {
			$url = '/'.$item->slug.'/'.$item->title;
			if($req->path() != $url) {
				$res->redirect($url);
				return;
			}
		}

		if(!isset($item->clicks)) $item->clicks = 0;
		$item->clicks++;
		$item->last_hit_at = date('Y-m-d H:i:s');
		\R::store($item);

		if($item->type == 'url') {
			$res->redirect($item->path);
		} else if($item->type == 'text') {
			if($req->query('raw')) {
					$this->sendFile($item->path, 'text/plain', $item->size, $item->title, strtotime($item->created_at));
			} else if($req->query('dl')) {
					$this->sendFile($item->path, 'text/plain', $item->size, $item->title, strtotime($item->created_at), 'attachment');
			} else {
				include $this->app->appPath.'/views/code.php';
			}
		} else {
			$this->sendFile($item->path, $item->mime, $item->size, $item->title, strtotime($item->created_at));
		}
	}

	private function sendFile($path, $mime, $size, $fileName, $lastModified, $disposition = 'inline') {
		$lastModifiedGm = gmdate('r', $lastModified);
		app()->res
			->header('Content-Type', $mime)
			->header('Content-Disposition', $disposition.'; filename="'.$fileName.'"');

		if($this->app->config->serveMethod == 'nginx') {
			$res
				->header('Content-Length', $size)
				->header('X-Accel-Redirect', '/protected_uploads'.$item->path);
		} else {
			$etag = md5($lastModified.$fileName);
			app()->res
				->header('Cache-Control', 'public, max-age=1800')
				->header('ETag', $etag)
				->header('Last-Modified', $lastModifiedGm);
			if (strtotime($this->app->req->header('If-Modified-Since')) == $lastModified || $this->app->req->header('If-None-Match') == $etag) {
				app()->res->status(304);
			} else {
				app()->res
					->header('Content-Length', $size)
					->readfile($this->app->storagePath.'/uploads'.$path);
			}
		}
	}

	public function handleThumbSlug($slug, Response $res) {
		$item = \R::findOne('item', 'slug = ?', [$slug]);
		if($item == null) {
			$res->status(404)->json(['error' => '404 file not found']);
			return;
		}

		if($item->type != 'image') {
			$res->redirect('/' . $item->slug);
			return;
		}

		$thumbPath = $this->app->storagePath.'/thumb/'.$item->slug.'.jpg';
		if(!file_exists($thumbPath)) {
			if(!file_exists($this->app->storagePath.'/thumb')) {
				mkdir($this->app->storagePath.'/thumb', $this->app->config->defaultChmod, true);
			}
			\TopSecret\Helper::resizeImage($this->app->storagePath.'/uploads'.$item->path, $thumbPath, 300);
		}

		$res->header('Content-Type', 'image/jpeg')
			->header('Content-Disposition', 'inline; filename="'.$item->title.'"')
			->header('Cache-Control', 'public, max-age=1800');
		if(false && $this->app->config->serveMethod == 'nginx') {
			$res->header('X-Accel-Redirect', '/protected_thumbs'.$item->path);
		} else {
			$res->readfile($this->app->storagePath.'/thumb/'.$item->slug.'.jpg');
		}
	}

	public function index(Response $res) {
		if($this->app->session->get('user_id') === 1) {
			$res->redirect('/tsa');
		} else {
			$res->beginContent();
			include $this->app->appPath.'/views/index.php';
		}
	}
}
