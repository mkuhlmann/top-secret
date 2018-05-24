<template id="tpl-retention">
	<div class="ui container">
		<div v-if="run == null">
			<div>Es stehen {{ dryRun.deletedItems }} Items zum Löschen an. Du kannst die Policy unter Einstellungen ändern.</div>

			<hr>
			<div>
				<input type="checkbox" v-model="safteyCheck"> Ich möchte löschen.

			<div>
			<button class="ui danger button" v-if="safteyCheck" v-on:click="nuke()">Jetzt ausführen!</button>
		</div>
		<div v-if="run != null">
			Löschen erfolgreich.
		</div>
	</div>
</template>
<!------------------------------------------>
<script type="text/javascript">
app.RetentionCtrl = Vue.extend({
	template: '#tpl-retention',
	data: function() { return {
		dryRun: {},
		run: null,
		safteyCheck: false
	} },
	created: function() {
		this.load();
	},
	methods: {
		load: function() {
			this.$http.get('/api/v1/retention/dry-run').then(function(response) {
				this.dryRun = response.data;
			});
		},
		nuke: function() {
			this.$http.get('/api/v1/retention/run').then(function(response) {
				this.run = response.data;
			});
		}
	}
});
Vue.component('retention-ctrl', app.RetentionCtrl);
</script>
