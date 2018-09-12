<!DOCTYPE html>
<html>
	<head>
		<meta property="og:image" content="<?php echo app()->config->baseUrl . '/thumb/' . $item->slug ?>" />
		<meta property="og:image:width" content="<?php echo $thumbSize[0]; ?>" />
		<meta property="og:image:height" content="<?php echo $thumbSize[1]; ?>" />
		<meta property="og:description" content="<?php echo e(app()->config->pageName); ?>" />
		<meta property="og:title" content="<?php echo e($item->title); ?>" />
		<title><?php echo e($item->title); ?></title>
	</head>

	<body>
		You should not see this.
	</body>
</html>
