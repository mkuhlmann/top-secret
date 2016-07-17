<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.1/semantic.min.css" />
	<style type="text/css">
	body { top: 0; left: 0; }
	a { cursor: pointer; }
	#app { padding: 40px; }
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
	<title><?php echo app()->config->pageName; ?> Admin</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8">
</head>
<body>
	<div class="ui page main-wrap" id="app">
		<div id="loader" v-if="loading">
			<div class="ui active inverted dimmer">
				<div class="ui text large loader"><?php echo app()->config->pageName; ?> wird geladen ...</div>
			</div>
		</div>
		<div class="ui top attached menu">
			<a class="ui item" href="/tsa/logout">
				<i class="sign out icon"></i> Logout
			</a>
			<a class="ui item" v-on:click="switchMenu('settings')">
				<i class="wrench icon"></i> Einstellungen
			</a>
			<a class="ui item" v-on:click="switchMenu('howto')">
				<i class="help circle icon"></i> Anleitung
			</a>
			<div class="right menu">
				<div class="ui right aligned category search item">
					<div class="ui transparent icon input">
						<input class="prompt" type="text" placeholder="Suche ...">
						<i class="search link icon"></i>
					</div>
					<div class="results"></div>
				</div>
			</div>
		</div>
		<div class="ui bottom attached segment hidden" v-show="menu != null">
			<div v-if="menu == 'settings'"><settings-ctrl></settings-ctrl></div>
			<div class="ui container" v-if="menu == 'howto'">
				<div class="ui grid">
					<div class="six wide column">
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

						<img v-bind:src="'https://top-secret.xyz/j2oMS6.png'" />
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
		<index-ctrl></index-ctrl>
	</div>

	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.25/vue.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue-resource/0.9.1/vue-resource.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue-router/0.7.13/vue-router.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.1/semantic.min.js"></script>
	<script type="text/javascript">
		$('.ui.dropdown').dropdown();
		var app = {
			_csrf: '<?php echo app()->session->token(); ?>'
		};

		app.Root = Vue.extend({
			data: function() { return {
				menu: null,
				loading: false
			} },
			created: function() {},
			methods: {
				switchMenu: function(key) {
					this.menu = (this.menu == key) ? null : key;
				}
			}
		});

		window.onload = function() {
			new app.Root({ el: '#app' });
		}
	</script>
	<!-- vue.js templates here -->
	<?php $files = glob(dirname(__FILE__).'/admin/*.{html,php}', GLOB_BRACE);
	foreach($files as $file) {
		include $file; // full path
	} ?>
</body>
</html>
