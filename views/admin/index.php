<template id="tpl-index">
	<table class="ui table">
		<thead>
			<tr>
				<th>Datei</th>
				<th>Link</th>
				<th>Hits</th>
				<th>Typ</th>
				<th>Hochgeladen</th>
			</tr>
		</thead>

		<tbody>
			<tr v-for="item in items">
				<td>
					<span v-if="item.type == 'image'">
						<a v-on:mouseleave="imageMouseLeave" v-on:mouseover="imageMouseOver(item)" href="/{{ item.slug }}/{{ item.title }}">{{ item.title }}</a>
					</span>
					<span v-if="item.type == 'text' || item.type == 'binary'">
						<a href="/{{ item.slug }}/{{ item.title }}">{{ item.title }}</a>
					</span>
					<span v-if="item.type == 'url'">
						<a href="{{ item.path }}">{{ item.title }}<span v-if="item.type == 'url'">[...]</span></a>
					</span>
				</td>
				<td><span style="font-family: monospace;"><?php echo app()->config->baseUrl; ?>/{{item.slug}}</span></td>
				<td>{{ item.clicks || 0 }}</td>
				<td>{{ item.type }}</td>
				<td>{{ item.created_at }} <a v-on:click="itemDelete(item)">D</a></td>
			</tr>
		</tbody>
	</table>
	<div style="position: fixed; top: 0; right: 0;"><img v-bind:src="imageThumbPath"></div>
</template>
<!------------------------------------------>
<script type="text/javascript">
app.IndexCtrl = Vue.extend({
	template: '#tpl-index',
	data: _ => { return {
		items: [],
		imageThumbPath: null
	} },
	created: function() {
		this.$http.get('/api/v1/items').then(function(response) {
			this.items = response.data;
		});
	},
	methods: {
		imageMouseOver: function(item) {
			this.imageThumbPath = '/thumb/'+item.slug;
		},
		imageMouseLeave: function() {
			this.imageThumbPath = null;
		},
		itemDelete: function(item) {
			this.$http.post('/api/v1/item/'+item.slug+'/delete', {'_csrf': app._csrf}).then(function(repsonse) {

			});
			this.items.splice(this.items.indexOf(item), 1);
		}
	}
});
Vue.component('index-ctrl', app.IndexCtrl);
</script>
