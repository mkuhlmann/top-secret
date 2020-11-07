<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/buefy/0.9.4/buefy.min.css" integrity="sha512-kYGHZRStwK4F8bgVhj/J5IEWmEjLbQ7re6mQiYx/LH5pfl8bDQ3g5SaExM/6z59mASfENR8xwVhywnm8ulVvew==" crossorigin="anonymous" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/5.8.55/css/materialdesignicons.min.css" integrity="sha512-x96qcyADhiw/CZY7QLOo7dB8i/REOEHZDhNfoDuJlyQ+yZzhdy91eAa4EkO7g3egt8obvLeJPoUKEKu5C5JYjA==" crossorigin="anonymous" />
		
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
		<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/buefy/0.9.4/buefy.min.js" integrity="sha512-ejJ4Nw/I2BSgEeJoP85Joh0nfMyXT+US/UlnDmp7GC20yrUDhoerj7RnOGOQK0Ke3vPhYJy0eicEvCaVldktXA==" crossorigin="anonymous"></script>
		<script type="module" src="/App/Index.js"></script>
	</body>
	
</html>
