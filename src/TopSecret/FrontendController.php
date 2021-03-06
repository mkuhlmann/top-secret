<?php

namespace TopSecret;

use Areus\Http\Request;
use RedBeanPHP\R;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Stream;

use TopSecret\Model\Item;

class FrontendController extends \Areus\ApplicationModule {
	private function isBrowser($userAgent) {
		$userAgent = strtolower($userAgent);


		static $browsers = ['opera', 'edge', 'chrome', 'safari', 'firefox', 'msie', 'wget', 'curl'];
		foreach($browsers as $browser) {
			if(strpos($userAgent, $browser) !== false) {
				return true;
				break;
			}
		}
		return false;
	}


	public function openGraphSlug($slug, Request $req) : Response {
		/** @var Item $item */
		$item = R::findOne('item', 'slug = ?', [$slug]);
		if($item == null) {
			return new JsonResponse(['error' => '404 file not found'], 404);
		}

		$response = new RedirectResponse('/' . $item->slug);

		if(!$this->isBrowser($req->getHeaderLine('User-Agent'))) {
			$response = viewResponse('opengraph', [
				'item' => $item, 
				'thumbSize' => $item->getResolution(1200)
			]);
		}

		return $response
			->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
			->withHeader('Pragma', 'no-cache');
	}

	public function handleSlug($slug, Request $req) {
		$item = R::findOne('item', 'slug = ?', [$slug]);
		if($item == null) {
			return new JsonResponse(['error' => '404 file not found'], 404);
		}

		if($item->type != 'url' && $this->app->config->redirectFileName) {
			$url = '/'.$item->slug.'/'.urlencode($item->title);
			if($req->getUri()->getPath() != $url) {
				return (new RedirectResponse($url))
					->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
					->withHeader('Pragma', 'no-cache');
			}
		}

		if($this->app->config->countHitIfLoggedIn || app()->session->get('user_id') !== 1) {
			if(!isset($item->clicks)) $item->clicks = 0;
			$item->clicks++;
			$item->last_hit_at = date('Y-m-d H:i:s');
			R::store($item);

			if($this->app->config->piwikEnableTracking) {
				$data = [
					'rec' => 1,
					'idsite' => $this->app->config->piwikIdSite,
					'url' => $this->app->config->baseUrl . '/' . $item->slug,
					'token_auth' => $this->app->config->piwikAuthToken,
					'rand' => uniqid(),

					'action_name' => $item->title,
					'ua' => $_SERVER['HTTP_USER_AGENT'],
					'cip' => ($this->app->config->behindTrustedProxy && isset($_SERVER['HTTP_X_FORWARDED_FOR']))
								? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'],
					'urlref' => $_SERVER['HTTP_REFERER']
				];

				$_data = [];
				foreach($data as $k => $v) {
					$_data[] = $k.'='.urlencode($v);
				}
				$piwikUrl = $this->app->config->piwikUrl.'?'.implode('&', $_data);
				@file_get_contents($piwikUrl); // supress any errors
			}
		}

		if($this->app->config->richPreview && $item->type == 'image') {
			if(!$this->isBrowser($req->getHeaderLine('User-Agent'))) {
				return $this->openGraphSlug($item->slug, $req);
			}
		}

		if($item->type == 'url') {
			return new RedirectResponse($item->path);
		} else if($item->type == 'text') {
			if($req->query('raw')) {
					return $this->sendFile($item->path, 'text/plain', $item->size, $item->title, strtotime($item->created_at));
			} else if($req->query('dl')) {
					return $this->sendFile($item->path, 'text/plain', $item->size, $item->title, strtotime($item->created_at), 'attachment');
			} else {
				if(substr($item->title, -strlen('.display.html')) === '.display.html') {
					return $this->sendFile($item->path, 'text/html', $item->size, $item->title, strtotime($item->created_at));
				} else if($item->extension == 'md') {
					$parser = new \cebe\markdown\GithubMarkdown();
					$parser->html5 = true;
					$mdHtml = $parser->parse(file_get_contents($this->app->path('/storage').'/uploads'.$item->path));
					$mdHtml = str_replace('<table>', '<table class="ui table">', $mdHtml);
					return viewResponse('markdown', ['mdHtml' => $mdHtml, 'item' => $item]);
				} else {
					return viewResponse('code', ['item' => $item]);
				}
			}
		} else {
			return $this->sendFile($item->path, $item->mime, $item->size, $item->title, strtotime($item->created_at));
		}
	}

	private function sendFile($path, $mime, $size, $fileName, $lastModified, $disposition = 'inline') {
		$disableCache = $this->app->config->disableCacheHeaders;

		$lastModifiedGm = gmdate('r', $lastModified);

		$response = new Response();

		$response = $response
			->withHeader('Content-Type', $mime)
			->withHeader('Content-Disposition', $disposition.'; filename="'.$fileName.'"');

		if($disableCache) {
			$response = $response
				->withHeader('Expires', 'Mon, 01 Jan 2000 00:00:00 GMT')
				->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
				->withHeader('Pragma', 'no-cache');
		}

		if($this->app->config->serveMethod == 'nginx') {
			$response = $response
				->withHeader('Content-Length', $size)
				->withHeader('X-Accel-Redirect', '/protected_uploads'.$path);
		} else {
			$etag = md5($lastModified.$fileName);

			if(!$disableCache) {
				$response = $response
					->withHeader('Cache-Control', 'public, max-age=1800')
					->withHeader('ETag', $etag)
					->withHeader('Last-Modified', $lastModifiedGm);
			}
			
			if (!$disableCache && strtotime($this->app->request->header('If-Modified-Since')) >= $lastModified || $this->app->request->header('If-None-Match') == $etag) {
				$response = $response->withStatus(304);
			} else {
				$response = $response
					->withHeader('Content-Length', $size)
					->withBody(new Stream($this->app->path('/storage').'/uploads'.$path));
			}
		}

		return $response;
	}

	public function handleThumbSlug($slug, Request $res) {
		/** @var Item $item */
		$item = R::findOne('item', 'slug = ?', [$slug]);
		if($item == null) {
			return new JsonResponse(['error' => '404 file not found'], 404);
		}


		if($item->type != 'image') {
			$color = $res->query('dark') ? '#ddd' : '#333';
			return (new TextResponse(FileTypeSvg::get($item, $color)))
					->withHeader('Content-type', 'image/svg+xml');
		} else if($item->mime == 'image/svg+xml') {
			return new RedirectResponse('/' . $item->slug);
		}

		$thumbPath = $item->getFullThumbnailPath(300);
		
		if($res->query('s')
			&& hash_equals(
				hash_hmac('sha256', $item->slug . $res->query('s'), app()->config->loginSecret),
				$res->query('h')
				)) {
			$thumbPath = $item->getFullThumbnailPath($res->query('s'));
		}

		$response = new Response();
		$response = $response
			->withHeader('Content-Type', 'image/jpeg')
			->withHeader('Content-Disposition', 'inline; filename="'.$item->title.'"')
			->withHeader('Cache-Control', 'public, max-age=1800');
		if(false && $this->app->config->serveMethod == 'nginx') {
			$response = $response->withHeader('X-Accel-Redirect', '/protected_thumbs'.$item->path);
		} else {
			$response = $response->withBody(new Stream($thumbPath));
		}

		return $response;
	}

	public function index(Request $res) {
		if($this->app->session->get('user_id') === 1) {
			return new RedirectResponse('/tsa2');
		} else {
			return viewResponse('index');
		}
	}
}
