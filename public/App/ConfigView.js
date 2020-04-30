
export default {
	template: /*html*/`
		<div class="container content">
			<h1>Einstellungen</h1>
			
			<div class="loader-wrapper is-active" v-if="loading">
				<div class="loader is-loading"></div>
			</div>

			<div v-if="!loading">

			<h2>Auth</h2>

			<div class="columns">
				<div class="field column">
					<label class="label">Api Key</label>
					<div class="control">
						{{ config.apiKey }}
						<button class="button is-primary is-small" @click="regenerateApiKey">Regenerate</button>
					</div>
				</div>
				<div class="field column">
					<label class="label">New password</label>
					<div class="control">
						<input class="input" type="password" v-model="config._password">
					</div>
				</div>
				<div class="field column">
					<label class="label">Repeat new password</label>
					<div class="control">
						<input class="input" type="password" v-model="config._passwordRepeat">
					</div>
				</div>
			</div>

			<h2>Allgemein</h2>

			
			<div class="columns">
				<div class="field column">
					<label class="label">Seiten URL</label>
					<div class="control">
						<input class="input" type="text" v-model="config.baseUrl">
					</div>
				</div>
				<div class="field column">
					<label class="label">Seiten Name</label>
					<div class="control">
						<input class="input" type="text" v-model="config.pageName">
					</div>
				</div>
			</div>


			<div class="columns">
				<div class="field column">
					<label class="label">Methode für Dateibereitstellung</label>
					<div class="control has-icons-left">
						<div class="select is-fullwidth">
							<select v-model="config.serveMethod">
								<option>php</option>
								<option>nginx</option>
							</select>
						</div>
						<div class="icon is-medium is-left">
							<i class="mdi mdi-server"></i>
						</div>
					</div>
				</div>
				<div class="field column">
					<label class="label">Bibliothek für Bildmanipulationen</label>
					<div class="control  has-icons-left">
						<div class="select is-fullwidth">
							<select v-model="config.imageLibrary">
								<option>gd</option>
								<option>imagemagick</option>
							</select>
						</div>
						<div class="icon is-medium is-left">
							<i class="mdi mdi-image-edit"></i>
						</div>
					</div>
				</div>
				<div class="field column">
					<label class="label">Berechtigung für neue Ordner</label>
					<div class="control">
						<input class="input" type="number" v-model="config.defaultChmod">
					</div>
				</div>
			</div>

			<div class="columns">
				<div class="field column is-two-thirds">
					<label class="label">Slug Charset</label>
					<div class="control">
						<input class="input" type="text" v-model="config.slugCharset">
					</div>
				</div>
				<div class="field column">
					<label class="label">Slug Länge</label>
					<div class="control">
						<input class="input" type="number" v-model="config.slugLength">
					</div>
				</div>
				<div class="field column">
					<label class="label">Kombinationen</label>
					<div class="control">
						<input class="input" type="number" disabled :value="Math.pow(config.slugCharset.length, config.slugLength )">
					</div>
				</div>
			</div>
				
			<h2>Tracking</h2>
			<div class="columns">
				<div class="field column">
					<label class="label">Dateizugriffe (Hits) zählen wenn eingeloggt</label>
					<div class="control has-icons-left">
						<div class="select is-fullwidth">
							<select v-model="config.countHitIfLoggedIn">
								<option value="true">Ja</option>
								<option value="false">Nein</option>
							</select>
						</div>
						<div class="icon is-medium is-left">
							<i class="mdi mdi-target-account"></i>
						</div>
					</div>
				</div>
				<div class="field column">
					<label class="label">Rich Preview (Beta)</label>
					<div class="control  has-icons-left">
						<div class="select is-fullwidth">
							<select v-model="config.richPreview">
								<option value="true">Ja</option>
								<option value="false">Nein</option>
							</select>
						</div>
						<div class="icon is-medium is-left">
							<i class="mdi mdi-file-find"></i>
						</div>
					</div>
				</div>
			</div>

			<div class="columns">
				<div class="field column is-narrow">
					<label class="label">Nutze Piwik</label>
					<div class="control has-icons-left">
						<div class="select is-fullwidth">
							<select v-model="config.piwikEnableTracking">
								<option value="true">Ja</option>
								<option value="false">Nein</option>
							</select>
						</div>
						<div class="icon is-medium is-left">
							<i class="mdi mdi-google-analytics"></i>
						</div>
					</div>
				</div>
				<div class="field column">
					<label class="label">Piwik Endpoint</label>
					<div class="control">
						<input class="input" type="text" v-model="config.piwikUrl">
					</div>
				</div>
				<div class="field column is-narrow">
					<label class="label">Piwik Idsite</label>
					<div class="control">
						<input class="input" type="number" v-model="config.piwikIdSite">
					</div>
				</div>
				<div class="field column">
					<label class="label">Piwik Auth Token</label>
					<div class="control">
						<input class="input" type="text" v-model="config.piwikAuthToken">
					</div>
				</div>
			</div>
			
			<h2>Aufbewahrung</h2>
			<div class="columns">
				<div class="field column is-narrow">
					<label class="label">Maximales Alter ohne Ansicht (Tage)</label>
					<div class="control">
						<input class="input" type="number" v-model="config.retentionDays">
					</div>
				</div>
				<div class="field column is-narrow">
					<label class="label">Nur ungetaggte</label>
					<div class="control">
						<div class="select is-fullwidth">
							<select v-model="config.retentionOnlyUntagged">
								<option value="true">Ja</option>
								<option value="false">Nein</option>
							</select>
						</div>
					</div>
				</div>
			</div>

			<hr>

			<div class="field">
				<div class="control">
					<a href="/tsa/sharexPreset" class="button is-secondary is-pulled-right">Download ShareX preset</a>
					<button class="button is-primary" v-on:click="save()">Speichern</button>
				</div>
			</div>

			</div>
		</div>
	`,

	data() {
		return {
			loading: true,
			config: {}
		}
	},

	created() {
		this.load();
	},

	methods: {
		load() {
			app.fetch('/tsa/getConfig')
				.then(r => r.json())
				.then(r => {
					this.config = r;
					this.loading = false;
				});
		},

		regenerateApiKey() {
			app.fetch('/tsa/regenerateApiKey', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({
					_csrf: app.csrf
				})
			})
				.then(r => r.json())
				.then(r => {
					this.config.apiKey = r.apiKey;
				});
		},

		save() {
			if(this.config._password && this.config._password.length > 0) {
				let error = null;

				if(this.config._password.length < 5) {
					error = `Password is too short.`;
				} else if(this.config._password != this.config._passwordRepeat) {
					error = `Passwords don't match!`;
				} else 

				if(error) {
					this.$buefy.snackbar.open({
						message: error,
						position: 'is-bottom',
						type: 'is-error',
						duration: 2000,
						queue: false
					});
					return;
				}
			}

			this.loading = true;
			app.fetch('/tsa/saveConfig', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({
					config: this.config,
					_csrf: app.csrf
				})
			}).then(r => {
				this.load();
			});
		}
	}


}
