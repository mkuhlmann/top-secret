<!DOCTYPE html>
<html>
	<head>
		<link href="https://fonts.googleapis.com/css?family=Sigmar+One" rel="stylesheet" type="text/css">
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
				font-family: 'Sigmar One', sans-serif;
				opacity: 0.7;
			}
		</style>
	</head>

	<body>
		<div class="wrap">
			<h1><?php echo app()->config->pageName; ?></h1>
		</div>

		<form method="post" action="/l" id="pf">
			<input type="hidden" name="p" id="p">
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
		</script>
	</body>
</html>
