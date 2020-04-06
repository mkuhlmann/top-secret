export default {
	template: /*html*/`
		<div class="content container">
			<article class="message is-primary">
				<div class="message-header">
					<p>Aufbewahrung</p>
				</div>
				<div class="message-body">
					Es stehen {{ dryRun.deletedItems }} Items ({{ Math.round(dryRun.deletedSize/1024/1024*100)/100 }} MiB) zum Löschen an. Du kannst die Richtlinie unter Einstellungen ändern.               
				</div>
			</article>
			<hr>
			<p>
				<input type="checkbox" v-model="safteyCheck"> Löschung bestätigen
			</p>
			<button v-if="safteyCheck && run < 2" class="button is-danger" :class="{'is-loading': run==1}" v-on:click="nuke()">Jetzt ausführen!</button>
		</div>
	`,

	data() { return {
		dryRun: {},
		run: 0,
		safteyCheck: false
	} },
	
	created() {
		this.load();
	},
	
	methods: {
		load() {
			fetch('/tsa/retentionDryRun')
				.then(res => res.json())
				.then(res => {
					this.dryRun = res;
				});
		},
		
		nuke() {
			this.run = 1;
			app.fetch('/tsa/retentionRun', {
				method: 'POST'
			})
				.then(res => res.json())
				.then(res => {
					this.run = 2;
				});
		}
	}

}
