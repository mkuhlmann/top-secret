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
				<td>
					<span class="slug"><?php echo app()->config->baseUrl; ?>/<input type="text" v-on:keyup="itemSlugKeyPress(item, $event)" v-model="item.slug"></span>
					 <i v-show="item.modified && item.slug.length > 0" v-on:click="itemSlugSave(item)" class="save icon opacity-hover pointer"></i>
					 <i v-show="item.modified" v-on:click="itemSlugCancel(item)" class="cancel icon opacity-hover pointer"></i>
				 </td>
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
			for(var i = 0; i < this.items.length; i++) {
				this.items[i].oldSlug = this.items[i].slug;
			}
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
		},
		itemSlugKeyPress: function(item, e) {
			if(!item.modified) {
				this.$set('items['+this.items.indexOf(item)+'].modified', true);
			}
			var validChars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_-'.split('');
			if(validChars.indexOf(e.key) === -1) {
				e.preventDefault();
				return false;
			}
		},
		itemSlugCancel: function(item) {
			item.slug = item.oldSlug;
			item.modified = false;
		},
		itemSlugSave: function(item) {
			var sendItem = Object.assign({}, item);
			item.modified = false;
			item.slug = 'speichere ...';

			this.$http.post('/api/v1/item/'+item.oldSlug+'/update', {'_csrf': app._csrf, 'item': sendItem }).then(function(response) {
				this.$set('items['+this.items.indexOf(item)+']', Object.assign(response.data, {oldSlug: response.data.slug}));
			});
		}
	}
});
Vue.component('index-ctrl', app.IndexCtrl);
</script>
