<?php declare(strict_types=1);

namespace TopSecret\Model;

use FilesystemIterator;
use GlobIterator;
use RedBeanPHP\SimpleModel;
use RedBeanPHP\R;


class Item extends SimpleModel  {

	public static function generateSlug(string $preferredSlug) : string {
		if(!$preferredSlug) {
			$preferredSlug = \TopSecret\Helper::generateSlug();
		}

		$slug = $preferredSlug;
		while(self::slugExists($slug)) {
			$slug = $preferredSlug . '-' . \TopSecret\Helper::generateRandomString(2);
		}

		return $slug;
	}
	
	public static function slugExists($slug) : bool {
		return R::count('item', 'slug = ?', [$slug]) > 0;
	}

	public function getPath() : string {
		return $this->path;
	}

	public function getFullPath() : string {
		return app()->path('/storage/uploads'. $this->path);
	}

	public function getResolution(int $maxSize = null) : array {
		if($this->type != 'image') {
			return [0, 0];
		}

		if(!$this->imageWidth || !$this->imageHeight) {
			list($this->imageWidth, $this->imageHeight) = getimagesize($this->getFullPath());
			R::store($this);
		}

		$width = $this->imageWidth;
		$height = $this->imageHeight;

		if($maxSize != null) {
			$ratio = $width / $height;

			$targetWidth = $targetHeight = min($maxSize, max($width, $height));
			if($targetWidth == $maxSize) {
				if ($ratio < 1) {
					$targetWidth = $targetHeight * $ratio;
				} else {
					$targetHeight = $targetWidth / $ratio;
				}
				return [$targetWidth, $targetHeight];
			}
		}

		return [$width, $height];
	}

	public function getWidth() {
		return $this->getResolution()[0];
	}

	public function getHeight() {
		return $this->getResolution()[1];
	}

	/**
	 * Will get automagically called by RedBeanPHP
	 */
	public function delete() {
		if(isset($this->path) && file_exists($this->getFullPath())) {
			unlink($this->getFullPath());

			// delete all generated thumbs
			$iterator = new GlobIterator(app()->path('/storage/thumb/') . "/{$this->slug}-*.*", FilesystemIterator::KEY_AS_PATHNAME);			
			/** @var \SplFileInfo $file */
			foreach($iterator as $file) {
				unlink($file->getPathname());
			}
		}
	}

	public function getFullThumbnailPath(int $maxSize = null) : string {
		if(min($maxSize, max($this->getResolution())) != $maxSize) {
			return $this->getFullPath();
		}

		$path = app()->path("/storage/thumb/{$this->slug}-s{$maxSize}.jpg");

		if(!file_exists($path)) {
			\TopSecret\Helper::resizeImage($this->getFullPath(), $path, $maxSize);
		}
		return $path;
	}
}
