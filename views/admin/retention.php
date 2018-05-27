<template id="tpl-retention">
	<div class="ui container">
		<div class="ui active centered inline loader" v-if="run == 1"></div>
		<div v-show="run == 2">
			<strong>Löschen erfolgreich.</strong>
		</div>
		<div v-if="run == 0">
			<div>Es stehen {{ dryRun.deletedItems }} Items zum Löschen an. Du kannst die Policy unter Einstellungen ändern.</div>

			<hr>
			<div>
				<input type="checkbox" v-model="safteyCheck"> Ich möchte löschen.

			<div>
			<button class="ui danger button" v-if="safteyCheck" v-on:click="nuke()">Jetzt ausführen!</button>
		</div>
	</div>
</template>
<!------------------------------------------>
<script type="text/javascript">
app.RetentionCtrl = Vue.extend({
	template: '#tpl-retention',
	data: function() { return {
		dryRun: {},
		run: 0,
		safteyCheck: false
	} },
	created: function() {
		this.load();
	},
	methods: {
		load: function() {
			this.$http.get('/tsa/retentionDryRun').then(function(response) {
				this.dryRun = response.data;
			});
		},
		nuke: function() {
			this.run = 1;
			this.$http.post('/tsa/retentionRun', {_csrf: app._csrf}).then(function(response) {
				this.run = 2;
			});
		}
	}
});
Vue.component('retention-ctrl', app.RetentionCtrl);
</script>
