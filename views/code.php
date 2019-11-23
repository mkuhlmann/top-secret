<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.10/styles/default.min.css" integrity="sha256-zcunqSn1llgADaIPFyzrQ8USIjX2VpuxHzUwYisOwo8=" crossorigin="anonymous" />
		<style type="text/css">
		header {
			font-family: monospace;
		}
		</style>
		<title><?php echo e($item->title); ?></title>
	</head>

	<body>
		<header><?php echo e($item->title); ?> [ <a href="?raw=1">Raw</a> | <a href="?dl=1">Download</a> ]</header>
		<pre><code><?php echo e(file_get_contents(app()->path('/storage/uploads'.$item->path))); ?></code></pre>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.10/highlight.min.js" integrity="sha256-1zu+3BnLYV9LdiY85uXMzii3bdrkelyp37e0ZyTAQh0=" crossorigin="anonymous"></script>
		<script>hljs.initHighlightingOnLoad();</script>
	</body>
</html>
