export default {
	template: /*html*/`
		<div class="content container">
			<article class="message is-primary">
				<div class="message-header">
					<p>{{ $t('menu.retention' )}}</p>
				</div>
				<div class="message-body">
					{{ $t('retention.status', [dryRun.deletedItems, Math.round(dryRun.deletedSize/1024/1024*100)/100]) }}
				</div>
			</article>
			<hr>
			<p>
				<input type="checkbox" v-model="safteyCheck"> {{ $t('retention.confirm') }}
			</p>
			<button v-if="safteyCheck && run < 2" class="button is-danger" :class="{'is-loading': run==1}" v-on:click="nuke()">{{ $t('retention.run') }}</button>
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
