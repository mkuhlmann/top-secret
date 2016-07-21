<template id="tpl-index">
	<p></p>
	<div class="ui floating labeled icon dropdown onload button">
		<input type="hidden" v-model="filters.type">
		<i class="filter icon"></i>
		<span class="text">Dateityp</span>
		<div class="menu">
			<div class="header">
				<i class="tags icon"></i>
				Filter nach Dateityp
			</div>
			<div class="divider"></div>
			<div class="item" data-value="">Alle Dateitypen</div>
			<div class="item" data-value="text">Text</div>
			<div class="item" data-value="image">Bilder</div>
			<div class="item" data-value="binary">Binärdateien</div>
		</div>
	</div>
	<table class="ui table">
		<thead>
			<tr>
				<th>Datei</th>
				<th>Link</th>
				<th>Tags</th>
				<th>Hits</th>
				<th>Typ</th>
				<th>Hochgeladen</th>
				<th></th>
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
				<td class="slug">
					<span v-on:click="itemSlugEdit(item)" v-show="item.modified != true"><?php echo app()->config->baseUrl; ?>/{{ item.slug }}</span>
					<span v-show="item.modified == true">
						<?php echo app()->config->baseUrl; ?>/<input type="text" v-bind:id="'ise' + item.id" v-on:keyup="itemSlugKeyPress(item, $event)" v-model="item.slug">
					 	<i v-show="item.slug.length > 0" v-on:click="itemSlugSave(item)" class="save icon opacity-hover pointer"></i>
					 	<i v-on:click="itemSlugCancel(item)" class="cancel icon opacity-hover pointer"></i>
					</span>
				</td>
				<td><a v-on:click="itemEditTags(item)"><i class="pencil icon"></i></a> <a class="ui tag label" v-for="tag in item.tags">{{ tag }}</a></td>
				<td>{{ item.clicks || 0 }}</td>
				<td>{{ item.type }}</td>
				<td>{{ item.created_at }}</td>
				<td><a v-on:click="itemDelete(item)"><i class="trash icon"></i></a> <a v-on:click="itemUpload(item)" v-if="item.path"><i class="cloud upload icon"></i></a></td>
			</tr>
		</tbody>
	</table>
	<form style="display:none;" id="itemUploadForm">
		<input type="hidden" name="_csrf" value="<?php echo app()->session->token(); ?>">
		<input type="hidden" name="overwriteSlug" v-model="itemToUpload.slug">
		<input type="file" v-on:change="itemUploadDo" name="file" id="itemUploadInput">
		<input type="submit">
	</form>
	<div style="position: fixed; top: 0; right: 0;"><img v-bind:src="imageThumbPath"></div>
</template>
<!------------------------------------------>
<script type="text/javascript">
app.IndexCtrl = Vue.extend({
	template: '#tpl-index',
	data: _ => { return {
		filters: {type: '' },
		items: [],
		imageThumbPath: null,
		itemToUpload: {slug:''}
	} },
	created: function() {
		this.loadItems();
		this.$watch('filters', function() {
			this.loadItems();
		}, {deep: true});
	},
	methods: {
		loadItems: function() {
			var url = '/api/v1/items?_t=' + (Date.now() / 1000 | 0);
			if(this.filters.type != '') {
				url += '&type='+this.filters.type;
			}
			this.$http.get(url).then(function(response) {
			 	var items = response.data;
				for(var i = 0; i < items.length; i++) {
					items[i].modified = false;
					items[i].tags = items[i].tags && items[i].tags.split(',');
				}
				this.items = items;
			});
		},
		imageMouseOver: function(item) {
			this.imageThumbPath = '/thumb/'+item.slug;
		},
		imageMouseLeave: function() {
			this.imageThumbPath = null;
		},
		itemDelete: function(item) {
			item.title = 'wird gelöscht ...';
			this.$http.post('/api/v1/item/'+item.slug+'/delete', {'_csrf': app._csrf}).then(function(repsonse) {
				this.items.splice(this.items.indexOf(item), 1);
			});
		},
		itemUpload: function(item) {
			this.itemToUpload = item;
			document.getElementById('itemUploadInput').click();
		},
		itemUploadDo: function() {
			var self = this;
			var frm = document.getElementById('itemUploadForm');
			var frmData = new FormData(frm);
			var oReq = new XMLHttpRequest();
			oReq.open('POST', '/api/v1/upload', true);
			oReq.onload = function() {
				if (oReq.status == 200) {
					self.$set('items['+self.items.indexOf(self.itemToUpload)+']', JSON.parse(oReq.responseText).item);
				} else {
					alert('failed');
				}
			};

			oReq.send(frmData);
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
		itemSlugEdit: function(item) {
			item.modified = true;
			item.oldSlug = item.slug;
			window.setTimeout(function() {
				document.getElementById('ise'+item.id).focus();
			}, 50);
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
				this.$set('items['+this.items.indexOf(item)+']', Object.assign(response.data, {modified: false}));
			});
		}
	}
});
Vue.component('index-ctrl', app.IndexCtrl);
</script>
