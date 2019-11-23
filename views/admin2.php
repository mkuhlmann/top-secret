<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css" integrity="sha256-D9M5yrVDqFlla7nlELDaYZIpXfFWDytQtiV+TaH6F1I=" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdn.materialdesignicons.com/4.5.95/css/materialdesignicons.min.css" integrity="sha384-K4kKEbDs5+0KuqneFn9bbf36Gbp70oNEX6HB+IwiTCGJaGBTQyjYxFL9Z/ZQcoY5" crossorigin="anonymous">
		
		<meta name="key" content="%API_KEY%">
		<meta name="csrf" content="<?php echo app()->session->token(); ?>">
		<title><?php echo e(app()->config->pageName); ?> Admin</title>

		<link href="https://fonts.googleapis.com/css?family=Quicksand:400,600,700&display=swap" rel="stylesheet">
		<style class="text/css">
			body {
				font-family: 'Quicksand', sans-serif;
			}

			#app main {
				margin: 1em;
			}

			.tiles__item {
				width: 300px;
				height: 200px;
				background: #eee;
				display: inline-block;
				margin-right: 1em;
				margin-bottom: 1em;
				border-radius: 0.5em;
				background-size: cover;
				background-repeat: no-repeat;
			}

			.tiles__item__toolbar {
				padding: 0.5em;
				background: rgba(30, 30, 30, 0.7);
				border-top-left-radius: 0.5em;
				border-top-right-radius: 0.5em;
				color: #eee;
			}


			.loader-wrapper {
				position: absolute;
				top: 0;
				left: 0;
				height: 100%;
				width: 100%;
				background: #fff;
				opacity: 0;
				z-index: -1;
				transition: opacity .3s;
				display: flex;
				justify-content: center;
				align-items: center;
				border-radius: 6px;

				
			}

			.loader {
				height: 80px;
				width: 80px;
			}

			.loader-wrapper.is-active {
				opacity: 1;
				z-index: 1;
			}

		</style>
	</head>
	
	<body>
		
		<div id="app" class="page-wrap">
			<header>
				<nav class="navbar is-dark" role="navigation" aria-label="main navigation">
					<div class="navbar-brand">
						<a class="navbar-item has-text-primary" href="/tsa2">
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
							<router-link to="/items" class="navbar-item" active-class="is-active">
								<span class="icon is-medium"><i class="mdi mdi-view-list"></i></span> Hochlads
							</router-link>
							<router-link to="/tags" class="navbar-item" active-class="is-active">
								<span class="icon is-medium"><i class="mdi mdi-tag-multiple"></i></span> Tags
							</router-link>
							<router-link to="/retention" class="navbar-item" active-class="is-active">
								<span class="icon is-medium"><i class="mdi mdi-history"></i></span> Aufbewahrung
							</router-link>
							<router-link to="/config" class="navbar-item" active-class="is-active">
								<span class="icon is-medium"><i class="mdi mdi-settings"></i></span> Einstellungen
							</router-link>
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
		<script type="module" src="/App/Index.js"></script>
	</body>
	
</html>