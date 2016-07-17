<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.1/semantic.min.css" />
		<style type="text/css">
			body {
				padding: 1em;
			}
			header {
				font-family: monospace;
			}
			main img {
				max-width: 100%;
			}
		</style>
	</head>

	<body>
		<div class="ui container">
			<header><?php echo $item->title; ?> [ <a href="?raw=1">Raw</a> | <a href="?dl=1">Download</a> ]</header>
			<main>
				<?php echo $mdHtml; ?>
			</main>
		</div>
	</body>
</html>
