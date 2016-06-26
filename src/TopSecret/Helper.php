<?php

namespace TopSecret;

class Helper {

	static function resizeImage($srcPath, $dstPath, $maxSize = 1000, $jpegQuality = 80) {
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			self::resizeImageGd($srcPath, $dstPath, $maxSize, $jpegQuality);
			return;
		}
		shell_exec('convert '.escapeshellarg($srcPath).' -resize "'.(int)$maxSize.'>" -quality '.(int)$jpegQuality.' '.escapeshellarg($dstPath));
	}

	static function resizeImageGd($srcPath, $dstPath, $maxSize = 1000, $jpegQuality = 80) {
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
 		imagedestroy($originalImage);
 		$targetImage = null;
 		$originalImage = null;
	}
}
