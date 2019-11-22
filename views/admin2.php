<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<link href="https://fonts.googleapis.com/css?family=Exo:100i,400i|Niramit:400,400i,600" rel="stylesheet">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulmaswatch/0.7.2/superhero/bulmaswatch.min.css" integrity="sha256-PePNPS2+yKY/cZKgZCxHMLY6Syfj9wRqiptx6jU+p+I=" crossorigin="anonymous" />
		<link rel="stylesheet" href="https://cdn.materialdesignicons.com/4.5.95/css/materialdesignicons.min.css" integrity="sha384-K4kKEbDs5+0KuqneFn9bbf36Gbp70oNEX6HB+IwiTCGJaGBTQyjYxFL9Z/ZQcoY5" crossorigin="anonymous">
		
		<meta name="apiKey" content="%API_KEY%">
		<title><?php echo e(app()->config->pageName); ?> Admin</title>

		<style class="text/css">
			#app main {
				margin: 1em;
			}
		</style>
	</head>
	
	<body>
		
		<div id="app" class="page-wrap">
			<header>
				<nav class="navbar" role="navigation" aria-label="main navigation">
					<div class="navbar-brand">
						<a class="navbar-item" href="/tsa">
						<?php echo e(app()->config->pageName); ?> Admin <sup style="color: #888;">v2</sup>
						</a>

						<a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false">
						<span aria-hidden="true"></span>
						<span aria-hidden="true"></span>
						<span aria-hidden="true"></span>
						</a>
					</div>

					<div class="navbar-menu">
						<div class="navbar-start">
							<a class="navbar-item">
								<i class="mdi mdi-view-list"></i> Items
							</a>
						</div>
					</div>

					<div class="navbar-end">
						<div class="navbar-item">
							<div class="buttons">
								<a href="/tsa/logout" class="button is-light">
								Logout
								</a>
							</div>
						</div>
					</div>

				</nav>
			</header>

			<main>
				<router-view></router-view>
			</main>
		</div>
		
		<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.min.js" integrity="sha256-chlNFSVx3TdcQ2Xlw7SvnbLAavAQLO0Y/LBiWX04viY=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/vue-router/3.1.3/vue-router.min.js" integrity="sha256-r/vPIUvTobCpYZsMmvU7HM58cNd4D6/vdICqDFapV7Y=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js" integrity="sha256-4iQZ6BVL4qNKlQ27TExEhBN1HFPvAvAMbFavKKosSWQ=" crossorigin="anonymous"></script>
		<script type="module" src="App/Index.js"></script>
	</body>
	
</html>