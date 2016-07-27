<?php

namespace TopSecret;

class Helper {
	static function isWin() {
		return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
	}

	public static function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[mt_rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	public static function resizeImage($srcPath, $dstPath, $maxSize = 1000, $jpegQuality = 80) {
		if (app()->config->imageLibrary != 'imagemagick') {
			self::resizeImageGd($srcPath, $dstPath, $maxSize, $jpegQuality);
			return;
		}
		if($maxSize == null || $maxSize == 100000) {
			shell_exec('convert '.escapeshellarg($srcPath).' -quality '.(int)$jpegQuality.' '.escapeshellarg($dstPath));
		} else {
			shell_exec('convert '.escapeshellarg($srcPath).' -resize "'.(int)$maxSize.'>" -quality '.(int)$jpegQuality.' '.escapeshellarg($dstPath));
		}
	}

	public static function resizeImageGd($srcPath, $dstPath, $maxSize = 1000, $jpegQuality = 80) {
		$extension = pathinfo($srcPath, PATHINFO_EXTENSION);
		if($extension == 'jpg' || $extension == 'jpeg') {
	 		$originalImage = imagecreatefromjpeg($srcPath);
		} else if ($extension == 'png') {
			$originalImage = imagecreatefrompng($srcPath);
		} else {
			return;
		}

 		list($originalWidth, $originalHeight) = getimagesize($srcPath);
 		$ratio = $originalWidth / $originalHeight;

 		$targetWidth = $targetHeight = min($maxSize, max($originalWidth, $originalHeight));
		if($targetWidth == $maxSize) {
			if ($ratio < 1) {
	 			$targetWidth = $targetHeight * $ratio;
	 		} else {
	 			$targetHeight = $targetWidth / $ratio;
	 		}

	 		$srcWidth = $originalWidth;
	 		$srcHeight = $originalHeight;
	 		$srcX = $srcY = 0;

	 		$targetImage = imagecreatetruecolor($targetWidth, $targetHeight);
	 		imagecopyresampled($targetImage, $originalImage, 0, 0, $srcX, $srcY, $targetWidth, $targetHeight, $srcWidth, $srcHeight);

	 		imagejpeg($targetImage, $dstPath, $jpegQuality);
	 		imagedestroy($targetImage);
	 		$targetImage = null;
		} else {
			imagejpeg($originalImage, $dstPath, $jpegQuality);
		}
		imagedestroy($originalImage);
		$originalImage = null;
	}

	public static function normalizeSlug($text) {
		$text = preg_replace('~[^_\pL\d]+~u', '-', $text);
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
		$text = preg_replace('~[^-\w]+~', '', $text);
		$text = trim($text, '-');
		$text = preg_replace('~-+~', '-', $text);
		$text = strtolower($text);
		if (empty($text)) {
			return 'n-a';
		}

		$slug = $text;
		$i = 1;
		while(\R::count('item', 'slug = ?', [$slug]) > 0) {
			$slug = $text.'-'.$i++;
		}

		return $slug;
	}

	public static function generateSlug($length = null, $prepend = '') {
		if($length == null) {
			$length = app()->config->slugLength;
		}
		if(!empty($prepend) && app()->req->query('prependSlug') != null) {
			$prepend = app()->req->query('prependSlug');
		}
		return self::normalizeSlug($prepend.self::generateRandomString($length));
	}

	public static function buildQuery($pieces) {
		$sql = '';
		$glue = NULL;
		$params = [];
		foreach( $pieces as $piece ) {
			$n = count( $piece );
			switch( $n ) {
				case 1:
				$sql .= " {$piece[0]} ";
				break;
				case 2:
				$glue = NULL;
				if (!is_null($piece[0])) {
					$params[] = $piece[0];
					$sql .= " {$piece[1]} ";
				}
				break;
				case 3:
				$glue = ( is_null( $glue ) ) ? $piece[1] : $glue;
				if (!is_null($piece[0])) {
					$params[] = $piece[0];
					$sql .= " {$glue} {$piece[2]} ";
					$glue = NULL;
				}
				break;
			}
		}
		return [$sql, $params];
	}
}
