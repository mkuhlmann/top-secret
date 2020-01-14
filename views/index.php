<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="https://fonts.googleapis.com/css?family=Sintony&display=swap" rel="stylesheet" type="text/css">
		<title><?php echo app()->config->pageName; ?></title>
		<style type="text/css">
			body {
				top: 0;
				left: 0;
			}
			.wrap {
				position: absolute;
				width: 100%;
				text-align: center;
				left: 0;
				top: calc(50% - 7.5em);
			}
			h1 {
				font-size: 5em;
				font-weight: 400;
				font-family: 'Sintony', sans-serif;
				opacity: 0.7;
			}

			#p {
				width: 100%;
				font-size: 3em;
			}
		</style>
	</head>

	<body>
		<div class="wrap">
			<h1><?php echo e(app()->config->pageName); ?></h1>
		</div>

		<form method="post" action="/l" id="pf">
			<input type="hidden" name="p" id="p" placeholder="knock knock">
		</form>
		<script type="text/javascript">
			var p = '';
			document.addEventListener('keydown', function(e) {
				if(e.key.length == 1) p += e.key;
				if(e.key == 'Enter' && p.length > 5) {
					document.getElementById('p').value = p;
					document.getElementById('pf').submit();
				}
			}, false);
			if(/Mobile|iPhone|iPad|iPod|Android/i.test(navigator.userAgent)) {
				document.querySelector('#p').type = 'password';
			}
		</script>
	</body>
</html>
