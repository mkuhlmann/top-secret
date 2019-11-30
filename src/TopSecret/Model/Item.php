<?php declare(strict_types=1);

namespace TopSecret\Model;

use RedBeanPHP\SimpleModel;
use RedBeanPHP\R;


class Item extends SimpleModel  {
	
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

		if(!$this->width || !$this->height) {
			list($this->width, $this->height) = getimagesize($this->getFullPath());
		}

		$width = $this->width;
		$height = $this->height;

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

	public function delete() {
		
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