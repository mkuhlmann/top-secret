<template id="tpl-items">
	<div>
		<div class="ui dimmer modals page" style="opacity: 1; display: block;" v-if="itemForModal">
			<div class="ui modal" style="top: 10%; display: block;">
					<i class="close icon" v-on:click="itemModal(null)"></i>
					<div class="header">
						{{ itemForModal.title }}
					</div>
					<div class="content" :class="{ image: itemForModal.type == 'image' }">
						<div class="ui medium image" v-if="itemForModal.type == 'image'">
							<img :src="'/thumb/'+itemForModal.slug">
						</div>
						<div class="description">
							<table class="ui definition table">
								<tr><td>Dateiname</td><td>{{ itemForModal.name }}</td></tr>
								<tr><td>Mime</td><td>{{ itemForModal.mime }} (.{{ itemForModal.extension }})</td></tr>

								<tr><td>Letzer Aufruf</td><td>{{ itemForModal.last_hit_at }}</td></tr>
								<tr><td>Hits</td><td>{{ itemForModal.clicks }}</td></tr>

								<tr><td>Erstellt</td><td>{{ itemForModal.created_at }}</td></tr>
							</table>

						</div>
					</div>
					<div class="actions">
						<div class="ui primary right labeled icon button" v-on:click="itemModal(null)">
							Schließen
							<i class="checkmark icon"></i>
						</div>
					</div>
			</div>
		</div>


		<div class="ui dimmer modals page" style="opacity: 1; display: block;" v-if="showItemAddUrl">
			<div class="ui modal" style="top: 10%; display: block;">
					<i class="close icon" v-on:click="showItemAddUrl = false"></i>
					<div class="header">
						URL kürzen
					</div>
					<div class="content ui form">
						<div class="field">
							<input type="text" placeholder="URL ..." v-model="itemToUpload.url" />
						</div>

					</div>
					<div class="actions">
						<div class="ui green left labeled icon button" v-on:click="itemAddUrl()">
							Kürzen
							<i class="checkmark icon"></i>
						</div>
					</div>
			</div>
		</div>


		<p></p>
		<div class="ui floating labeled icon dropdown indexctrlonload button">
			<input type="hidden" v-on:change="updateFilter()" id="filter-type">
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
				<div class="item" data-value="url">Links</div>
			</div>
		</div>

		<div class="ui floating dropdown labeled multiple indexctrlonload icon button">
			<input type="hidden" v-on:change="updateFilter()" id="filter-tags">
			<i class="filter icon"></i>
			<span class="text">Tags</span>
			<div class="menu">
				<div class="ui icon search input">
					<i class="search icon"></i>
					<input type="text" placeholder="Search tags...">
				</div>
				<div class="divider"></div>
				<div class="header">
					<i class="tags icon"></i>
					Tag Label
				</div>
				<div class="scrolling menu">
					<div class="item" :data-value="tag.id" v-for="tag in tags">
						<div class="ui circular empty label" :class="tag.color"></div>
						{{ tag.name }}
					</div>
				</div>
			</div>
		</div>

		<div class="ui left icon input">
			<input type="text" placeholder="Search..." v-model="filters.q">
			<i class="search icon"></i>
		</div>

		<div style="float: right">
			<div class="ui primary buttons ">
				<button class="ui labeled icon button" v-on:click="itemUpload({ slug: null })">
					<i class="upload icon"></i> Hochladen
				</button>
				<div class="ui floating dropdown icon button indexctrlonload">
					<i class="dropdown icon"></i>
					<div class="menu">
						<div class="item" v-on:click="showItemAddUrl = true"><i class="bookmark icon"></i> URL kürzen</div>
					</div>
				</div>
			</div>
		</div>


		<div class="ui active centered inline loader" v-if="loading" style="margin-top: 1em;"></div>
		<table class="ui table" v-if="loading == false">
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
							<a v-on:mouseleave="imageMouseLeave" v-on:mouseover="imageMouseOver(item)" v-bind:href="'/' + item.slug + '/' + item.title">{{ item.title }}</a>
						</span>
						<span v-if="item.type == 'text' || item.type == 'binary'">
							<a v-bind:href="'/' + item.slug + '/' + item.title">{{ item.title }}</a>
						</span>
						<span v-if="item.type == 'url'">
							<a v-bind:href="item.path">{{ item.title }}<span v-if="item.type == 'url'">[...]</span></a>
						</span>
					</td>
					<td class="slug">
						<span v-on:click="itemSlugEdit(item)" v-show="item.modified != true"><?php echo app()->config->baseUrl; ?>/{{ item.slug }}</span>
						<span v-show="item.modified == true">
							<?php echo app()->config->baseUrl; ?>/<input type="text" :id="'ise' + item.id" v-on:keyup="itemSlugKeyPress(item, $event)" v-model="item.slug">
						 	<i v-show="item.slug.length > 0" v-on:click="itemSlugSave(item)" class="save icon opacity-hover pointer"></i>
						 	<i v-on:click="itemSlugCancel(item)" class="cancel icon opacity-hover pointer"></i>
						</span>
					</td>
					<td>
						<span :class="tag.color" class="ui horizontal label" v-for="tag in item._tags">{{ tag.name }} <i style="margin: 0 0 0 0.5em; cursor: pointer;" v-on:click="itemTagRemove(item, tag)" class="remove icon"></i></span>

						<a v-on:click="itemChooseTags(item)"><i class="plus icon"></i></a>

						<div class="ui multiple tag dropdown" :id="'item-tag-' + item.id">
							<input type="hidden" v-model="item.tags" v-on:change="itemUpdate(item, 'tag-chooser')">
							<div class="menu" v-if="itemForTagChooser == item.id">
								<div class="ui icon search input">
									<i class="search icon"></i>
									<input type="text" placeholder="Search tags...">
								</div>
								<div class="divider"></div>
								<div class="header">
									<i class="tags icon"></i>
									Tag Label
								</div>
								<div class="scrolling menu">
									<div class="item" :data-value="tag.id" v-for="tag in tags">
										<div class="ui circular empty label" :class="tag.color"></div>
										{{ tag.name }}
									</div>
								</div>
							</div>
						</div>
					</td>
					<td>{{ item.clicks || 0 }}</td>
					<td>{{ item.type }}</td>
					<td>{{ item.created_at }}</td>
					<td>
						<a v-if="item.type != 'url'" v-on:click="itemModal(item)"><i class="info icon"></i></a>
						<a v-on:click="itemDelete(item)"><i class="trash icon"></i></a>
						<a v-on:click="itemUpload(item)" v-if="item.path"><i class="cloud upload icon"></i></a>
					</td>
				</tr>
				<tr v-if="items.length == 0">
					<td colspan="7"><center><em>Keine Dateien gefunden :(</em></center></td>
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
	</div>
</template>
<!------------------------------------------>
<script type="text/javascript">
app.ItemsCtrl = Vue.extend({
	template: '#tpl-items',
	data: _ => { return {
		filters: { type: '', tags: '', q: '' },
		tags: null,
		items: [],
		imageThumbPath: null,
		itemToUpload: {slug: null, url: null},
		itemForModal: null,
		itemForTagChooser: null,
		showItemAddUrl: false,
		refreshCount: 0,
		loading: true
	} },
	beforeDestroy: function() {
		$('.indexctrlonload').dropdown('destroy');
	},
	created: function() {
		this.$http.get('/api/v1/tags').then(function(response) {
			this.tags = response.data;
			$('.indexctrlonload').dropdown();
			this.loadItems();
		});
		this.$watch('filters', function() {
			this.loadItems();
		}, { deep: true });
	},
	methods: {
		updateFilter: function() {
			this.filters.type = $('#filter-type').val();
			this.filters.tags = $('#filter-tags').val();
		},
		loadTags: function() {
			this.$http.get('/api/v1/tags').then(function(response) {
				this.tags = response.data;
				$('.indexctrlonload').dropdown();
			});
		},
		loadItems: function() {
			this.loading = true;
			$('.ui.tag.dropdown').dropdown('destroy');
			var url = '/api/v1/items?_t=' + (Date.now() / 1000 | 0);
			if(this.filters.type != '') {
				url += '&type='+this.filters.type;
			}
			if(this.filters.tags !== '') {
				url += '&tags='+this.filters.tags;
			}
			if(this.filters.q !== '') {
				url += '&q='+escape(this.filters.q);
			}
			this.$http.get(url, {
				before: function(request) {
			      if (this.previousRequest) {
			        this.previousRequest.abort();
			      }
			      this.previousRequest = request;
			    }
			}).then(function(response) {
			 	var items = response.data.items;
				for(var i = 0; i < items.length; i++) {
					items[i].modified = false;
					items[i]._tags = this.itemGetTags(items[i]);
				}
				this.refreshCount++;
				var currentRefresh = this.refreshCount;
				window.setTimeout(_ => {
					if(this.refreshCount == currentRefresh) {
						this.items = items;
						this.loading = false;
					}
				}, 200);
			}, function(response) {

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
			this.$http.delete('/api/v1/item/'+item.slug+'?_csrf='+app._csrf).then(function(repsonse) {
				this.items.splice(this.items.indexOf(item), 1);
			});
		},
		itemAddUrl: function() {
			this.$http.post('/api/v1/link', {_csrf: app._csrf, url: this.itemToUpload.url}, { emulateJSON: true }).then(function(response) {
				this.showItemAddUrl = false;
				this.loadItems();
			});
		},
		itemUpload: function(item) {
			this.itemToUpload = item;
			document.getElementById('itemUploadInput').click();
		},
		itemModal: function(item) {
			this.itemForModal = item;
		},
		itemUploadDo: function() {
			var self = this;
			var frm = document.getElementById('itemUploadForm');
			var frmData = new FormData(frm);
			var oReq = new XMLHttpRequest();
			oReq.open('POST', '/api/v1/upload', true);
			oReq.onload = function() {
				if (oReq.status == 200) {
					self.loadItems();
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
		itemUpdate: function(item, origin) {
			// Vuejs 2 fix
			if(origin == 'tag-chooser')	item.tags = $('#item-tag-'+item.id+' > input').val();
			item._tags = this.itemGetTags(item);
			var sendItem = Object.assign({}, item);
			this.$http.put('/api/v1/item/'+item.slug, {'_csrf': app._csrf, 'item': sendItem }).then();
		},
		itemSlugSave: function(item) {
			var sendItem = Object.assign({}, item);
			item.modified = false;
			item.slug = 'speichere ...';

			this.$http.put('/api/v1/item/'+item.oldSlug, {'_csrf': app._csrf, 'item': sendItem }).then(function(response) {
				this.$set('items['+this.items.indexOf(item)+']', Object.assign(response.data, {modified: false}));
			});
		},
		itemGetTags: function(item) {
			if(item.tags == null) return {};
			var tags = {};
			var self = this;
			item.tags.split(',').forEach(function(tag_id) {
				if(self.tags[tag_id]) {
					tags[self.tags[tag_id].id] = self.tags[tag_id];
				}
			});
			return tags;
		},
		itemTagRemove: function(item, tag) {
			delete item._tags[tag.id];
			item.tags = Object.keys(item._tags).join(',');
			this.itemUpdate(item);
		},
		itemChooseTags: function(item) {
			if(this.itemForTagChooser != null) {
				$('#item-tag-'+this.itemForTagChooser).dropdown('destroy');
			}
			this.itemForTagChooser = item.id;

			this.$nextTick(function() {
				window.setTimeout(function() {
					$('#item-tag-'+item.id).dropdown('toggle');
				}, 200);
			});
		}
	}
});
Vue.component('items-ctrl', app.ItemsCtrl);
</script>
