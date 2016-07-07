<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.4.0/styles/default.min.css">
		<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.4.0/highlight.min.js"></script>
		<style type="text/css">
		header {
			font-family: monospace;
		}
		</style>
	</head>

	<body>
		<header><?php echo $item->title; ?> [ <a href="?raw=1">Raw</a> | <a href="?dl=1">Download</a> ]</header>
		<pre><code><?php echo htmlentities(file_get_contents(app()->storagePath.'/uploads'.$item->path)); ?></code></pre>

		<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.4.0/highlight.min.js"></script>
		<script>hljs.initHighlightingOnLoad();</script>
	</body>
</html>
