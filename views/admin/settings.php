<template id="tpl-settings">
	<div class="ui container">
		<div class="ui active centered inline loader" v-if="loading"></div>
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
			<div class="ui divider"></div>
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
					<label>Berechtigung für neue Ordner</label>
					<input type="text" v-model="config.defaultChmod">
				</div>
			</div>
			<div class="three fields">
				<div class="ten wide field">
					<label>Slug Charset</label>
					<input type="text" v-model="config.slugCharset">
				</div>
				<div class="two wide field">
					<label>Slug Länge</label>
					<input type="number" v-model="config.slugLength">
				</div>
				<div class="four wide field">
					<label>Kombinationen</label>
					<input type="number" disabled :value="Math.pow(config.slugCharset.length, config.slugLength )">
				</div>
			</div>
			<div class="ui divider"></div>
			<div class="three fields">
				<div class="field">
					<label>Dateizugriffe (Hits) zählen wenn eingeloggt</label>
					<select v-model="config.countHitIfLoggedIn">
						<option value="true">Ja</option>
						<option value="false">Nein</option>
					</select>
				</div>
			</div>

			<div class="fields">
				<div class="two wide field">
					<label>Nutze Piwik</label>
					<select v-model="config.piwikEnableTracking">
						<option value="true">Ja</option>
						<option value="false">Nein</option>
					</select>
				</div>
				<div class="six wide field">
					<label>Piwik Endpoint</label>
					<input type="text" v-model="config.piwikUrl">
				</div>
				<div class="two wide field">
					<label>Piwik Idsite</label>
					<input type="number" v-model="config.piwikIdSite">
				</div>
				<div class="six wide field">
					<label>Piwik Auth Token</label>
					<input type="text" v-model="config.piwikAuthToken">
				</div>
			</div>

			<div class="field">
				<button class="ui primary button" v-on:click="save()">Speichern</button>
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
		this.load();
	},
	methods: {
		load: function() {
			this.$http.get('/tsa/getConfig').then(function(response) {
				this.config = response.data;
				this.loading = false;
			});
		},
		save: function() {
			this.loading = true;
			this.$http.post('/tsa/saveConfig', {_csrf: app._csrf, config: this.config}).then(function(response) {
				this.load();
			});
		}
	}
});
Vue.component('settings-ctrl', app.SettingsCtrl);
</script>
