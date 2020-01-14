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
		<meta name="baseUrl" content="<?php echo e(app()->config->baseUrl); ?>">
		<title><?php echo e(app()->config->pageName); ?> Admin</title>

		<link href="https://fonts.googleapis.com/css?family=Quicksand:400,600,700&display=swap" rel="stylesheet">
		<link href="/App/Index.css" rel="stylesheet">
	</head>
	
	<body>
		
		<div id="app" class="page-wrap">
			<header>
				<nav class="navbar is-dark" role="navigation" aria-label="main navigation">
					<div class="navbar-brand">
						<a class="navbar-item has-text-primary" href="/tsa2">
							<?php echo e(app()->config->pageName); ?> Admin <sup style="color: #888;">v2</sup>
						</a>

						<a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" v-on:click="navbarToggle()"  v-bind:class="{ 'is-active': navbarOpen }">
							<span aria-hidden="true"></span>
							<span aria-hidden="true"></span>
							<span aria-hidden="true"></span>
						</a>
					</div>

					<div class="navbar-menu" v-bind:class="{ 'is-active': navbarOpen }">
						<div class="navbar-start" v-on:click="navbarNavigate()">
							<router-link to="/items" class="navbar-item" active-class="is-active">
								<span class="icon is-medium"><i class="mdi mdi-view-list"></i></span>
								<span>Hochlads</span>
							</router-link>
							<router-link to="/tags" class="navbar-item" active-class="is-active">
								<span class="icon is-medium"><i class="mdi mdi-tag-multiple"></i></span>
								<span>Tags</span>
							</router-link>
							<router-link to="/retention" class="navbar-item" active-class="is-active">
								<span class="icon is-medium"><i class="mdi mdi-history"></i></span>
								<span>Aufbewahrung</span>
							</router-link>
							<router-link to="/config" class="navbar-item" active-class="is-active">
								<span class="icon is-medium"><i class="mdi mdi-settings"></i></span>
								<span>Einstellungen</span>
							</router-link>
						</div>

						
						<div class="navbar-end">
							<a href="/tsa/logout" class="navbar-item">
								<span class="icon is-medium"><i class="mdi mdi-logout"></i></span> 
								<span>Logout</span>
							</a>
						</div>

					</div>

				</nav>
			</header>

			<main>
				<router-view></router-view>
			</main>
		</div>
		
		<?php if(app()->config->environment == 'development'): ?>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.js" integrity="sha256-ufGElb3TnOtzl5E4c/qQnZFGP+FYEZj5kbSEdJNrw0A=" crossorigin="anonymous"></script>
		<?php else: ?>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.min.js" integrity="sha256-chlNFSVx3TdcQ2Xlw7SvnbLAavAQLO0Y/LBiWX04viY=" crossorigin="anonymous"></script>
		<?php endif; ?>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/vue-router/3.1.3/vue-router.min.js" integrity="sha256-r/vPIUvTobCpYZsMmvU7HM58cNd4D6/vdICqDFapV7Y=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js" integrity="sha256-4iQZ6BVL4qNKlQ27TExEhBN1HFPvAvAMbFavKKosSWQ=" crossorigin="anonymous"></script>
		<script type="module" src="/App/Index.js"></script>
	</body>
	
</html>