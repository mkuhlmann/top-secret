<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.1/semantic.min.css" />
		<style type="text/css">
			body {
				margin: 1em;
			}
			header {
				font-family: monospace;
			}
		</style>
	</head>

	<body>
		<header><?php echo $item->title; ?> [ <a href="?raw=1">Raw</a> | <a href="?dl=1">Download</a> ]</header>
		<?php echo $mdHtml; ?>
	</body>
</html>
