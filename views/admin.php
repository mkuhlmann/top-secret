<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.1/semantic.min.css" />
	<style type="text/css">
	body { top: 0; left: 0; }
	a { cursor: pointer; }
	#app { padding: 10px 20px; }
	.pointer {
		cursor: pointer;
	}
	.opacity-hover {
		opacity: 0.7 !important;
	}
	.opacity-hover:hover {
		opacity: 1 !important;
	}
	.slug {
		font-family: monospace;
	}
	.slug input {
		border: 0;
		padding: 0;
		margin: 0;
		width: auto;
	}
	</style>
	<title><?php echo e(app()->config->pageName); ?> Admin</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8">
</head>
<body>
	<div class="ui page main-wrap" id="app">
		<div id="loader" v-if="loading">
			<div class="ui active inverted dimmer">
				<div class="ui text large loader"><?php echo e(app()->config->pageName); ?> wird geladen ...</div>
			</div>
		</div>
		<div class="ui secondary pointing menu">
			<a class="item" v-on:click="switchMenu(null)" v-bind:class="{ active: menu == null }">
				<i class="list icon"></i> Start
			</a>
			<a class="item" v-on:click="switchMenu('tags')" v-bind:class="{ active: menu == 'tags' }">
				<i class="flag icon"></i> Tags
			</a>
			<a class="item" v-on:click="switchMenu('settings')" v-bind:class="{ active: menu == 'settings' }">
				<i class="wrench icon"></i> Einstellungen
			</a>
			<a class="item" v-on:click="switchMenu('retention')" v-bind:class="{ active: menu == 'retention' }">
				<i class="history icon"></i> Aufbewahrung
			</a>
			<a class="item" v-on:click="switchMenu('howto')" v-bind:class="{ active: menu == 'howto' }">
				<i class="help circle icon"></i> Anleitung
			</a>
			<div class="right menu">
				<span class="item">
					{{ Math.round(stats.total_size/1024/1024*100)/100  }} MiB / {{ stats.total_count }} Dateien
				</span>
				<a class="item" href="/tsa/logout">
					<i class="sign out icon"></i> Logout
				</a>
			</div>
		</div>
		<items-ctrl v-if="menu == null"></items-ctrl>
		<retention-ctrl v-if="menu == 'retention'"></retention-ctrl>
		<settings-ctrl v-if="menu == 'settings'"></settings-ctrl>
		<tags-ctrl v-if="menu == 'tags'"></tags-ctrl>
		<div class="ui container" v-show="menu == 'howto'">
			<div class="ui grid">
				<div class="eight wide column">
					<h2>ShareX</h2>
					<div class="ui form">
						<div class="field">
							<label>Hochlade URL</label>
							<input type="text" value="<?php echo app()->config->baseUrl . '/api/v1/upload?key='.app()->config->apiKey; ?>">
						</div>
						<div class="field">
							<label>JSON Ergebnis</label>
							<input type="text" value="<?php echo app()->config->baseUrl . '/$json:slug$.$json.extension$'; ?>">
						</div>
					</div>

					<img v-bind:src="'https://top-secret.xyz/j2oMS6.png'">
				</div>
				<div class="six wide column">
					<h2>Tasker</h2>
					<p>
						<ul>
							<li><a href="https://play.google.com/store/apps/details?id=com.joaomgcd.autoshare" target="_blank" rel="nofollow">AutoShare</a> installieren und neues AutoShare-Command namens "top-secret" hinzufügen.</li>
							<li>Lade das <a href="/tsa/Tasker.prf.xml">Tasker Profil</a> herunter.</li>
							<li>In Tasker importieren</li>
							<li>Die Pushbullet Aufgabe neu konfigurieren.</li>
							<li>???</li>
							<li>Profit.</li>
						</ul>
					</p>
				</div>
			</div>

		</div>
	</div>

	<script type="text/javascript">
		var app = {
			_csrf: '<?php echo app()->session->token(); ?>'
		};
	</script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.1.10/vue.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue-resource/1.2.0/vue-resource.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue-router/2.2.0/vue-router.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.7/semantic.min.js"></script>

	<!-- vue.js templates here -->
	<?php $files = glob(dirname(__FILE__).'/admin/*');
	foreach($files as $file) {
		include $file; // full path
	} ?>

	<script type="text/javascript">
	new Vue({
		el: '#app',
		data: function() { return {
			menu: null,
			loading: false,
			stats: {}
		} },
		created: function() {
			this.$http.get('/api/v1/stats').then(function(response) {
				this.stats = response.data;
			});
		},
		methods: {
			switchMenu: function(key) {
				this.menu = key;
			}
		}
	});
</script>
</body>
</html>
