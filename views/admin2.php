<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<link href="https://unpkg.com/buefy@0.8.13/dist/buefy.min.css" rel="stylesheet" integrity="sha256-wCIznvmMv8MeshDLmooVTAYXRcX77UMaIp8gyyYMPQ8=" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdn.materialdesignicons.com/4.5.95/css/materialdesignicons.min.css" integrity="sha384-K4kKEbDs5+0KuqneFn9bbf36Gbp70oNEX6HB+IwiTCGJaGBTQyjYxFL9Z/ZQcoY5" crossorigin="anonymous">
		
		<meta name="key" content="%API_KEY%">
		<meta name="csrf" content="<?php echo app()->session->token(); ?>">
		<meta name="baseUrl" content="<?php echo e(app()->config->baseUrl); ?>">
		<meta name="uploadMaxFilesize" content="<?php echo e(ini_get('upload_max_filesize')); ?>">
		<title><?php echo e(app()->config->pageName); ?> Admin</title>

		<link href="https://fonts.googleapis.com/css?family=Quicksand:400,600,700&display=swap" rel="stylesheet">
		<link href="/App/Index.css" rel="stylesheet">
	</head>
	
	<body>
		<div id="app"></div>
		
		<?php if(app()->config->environment == 'development'): ?>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.js" integrity="sha256-ufGElb3TnOtzl5E4c/qQnZFGP+FYEZj5kbSEdJNrw0A=" crossorigin="anonymous"></script>
		<?php else: ?>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.min.js" integrity="sha256-chlNFSVx3TdcQ2Xlw7SvnbLAavAQLO0Y/LBiWX04viY=" crossorigin="anonymous"></script>
		<?php endif; ?>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/vue-i18n/8.17.4/vue-i18n.min.js" integrity="sha256-nNm7R+HTeuAIJA3rTufGjoe3r6y6iIYEu4QWm1y+yNY=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/vue-router/3.1.3/vue-router.min.js" integrity="sha256-r/vPIUvTobCpYZsMmvU7HM58cNd4D6/vdICqDFapV7Y=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js" integrity="sha256-4iQZ6BVL4qNKlQ27TExEhBN1HFPvAvAMbFavKKosSWQ=" crossorigin="anonymous"></script>
		<script src="https://unpkg.com/buefy@0.8.13/dist/buefy.min.js" integrity="sha256-4b3psIbMGiTsSFoLwsUdnfJ+NxNf6C/wLsXgzRQ87d8=" crossorigin="anonymous"></script>
		<script type="module" src="/App/Index.js"></script>
	</body>
	
</html>
