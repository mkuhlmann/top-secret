<template id="tpl-settings">
	<div class="ui container">
		<div class="ui active centered inline loader"  v-if="loading"></div>
		<div class="ui form" v-if="!loading">
			<div class="two fields">
				<div class="field">
					<label>Seiten URL</label>
					<input type="text" v-model="config.baseUrl">
				</div>
				<div class="field">
					<label>Seiten Name</label>
					<input type="text" v-model="config.pageName">
				</div>
			</div>
			<div class="field">
				<label>Admin Password</label>
				<input type="text" placeholder="***">
			</div>
			<div class="three fields">
				<div class="field">
					<label>Methode für Dateibereitstellung</label>
					<select v-model="config.serveMethod">
						<option>php</option>
						<option>nginx</option>
					</select>
				</div>
				<div class="field">
					<label>Bibliothek für Bildmanipulationen</label>
					<select v-model="config.imageLibrary">
						<option>gd</option>
						<option>imagemagick</option>
					</select>
				</div>
				<div class="field">
					<label>Berechtiung für neue Ordner</label>
					<input type="text" v-model="config.defaultChmod">
				</div>
			</div>
			<div class="field">
				<button class="ui primary button">Speichern</button>
			</div>
		</div>
	</div>
</template>
<!------------------------------------------>
<script type="text/javascript">
app.SettingsCtrl = Vue.extend({
	template: '#tpl-settings',
	data: function() { return {
		loading: true,
		config: {}
	} },
	created: function() {
		this.$http.get('/tsa/getConfig').then(function(response) {
			this.config = response.data;
			this.loading = false;
		});
	},
	methods: {
	}
});
Vue.component('settings-ctrl', app.SettingsCtrl);
</script>
