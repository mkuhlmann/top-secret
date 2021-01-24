<!DOCTYPE html>
<html>
	<head>
		<meta property="og:image" content="<?php echo app()->config->baseUrl . '/thumb/' . $item->slug ?>?s=1200&h=<?php echo hash_hmac('sha256', $item->slug . '1200', app()->config->loginSecret); ?>" />
		<meta property="og:image:width" content="<?php echo round($thumbSize[0]); ?>" />
		<meta property="og:image:height" content="<?php echo round($thumbSize[1]); ?>" />
		<meta property="og:description" content="<?php echo e(app()->config->pageName); ?>" />
		<meta property="og:title" content="<?php echo e($item->title); ?>" />
		<title><?php echo e($item->title); ?></title>
	</head>

	<body>
		<h2>OpenGraph</h2>
	</body>
</html>
