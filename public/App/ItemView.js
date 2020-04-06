import ItemModal from './ItemModal.js';

export default {
	template: /*html*/`
		<div>
			<item-modal v-if="itemModal" v-bind:item="itemModal" v-on:close="itemModal = null"></item-modal>
			<add-link-modal v-if="addLinkModal" v-on:close="addLinkModal=false"></add-link-modal>

			<div style="position: absolute; top: 0; right: 0; z-index: 1000;"><img v-bind:src="imageThumbPath"></div>

			<div class="columns">
				<div class="column">
					<div class="field">
						<div class="control has-icons-left">
							<input class="input" type="text" placeholder="Suchen ...">
							<span class="icon is-medium is-left">
								<i class="mdi mdi-magnify"></i>
							</span>
						</div>
					</div>
				</div>
				<div class="column is-narrow">
					
					<div class="buttons has-addons">
						<button class="button" :class="{ 'is-active is-info': q.dm == 't' }" v-on:click="q.dm = 't'">
							<span class="icon is-medium"><i class="mdi mdi-view-list"></i></span>
						</button>
						<button class="button" :class="{ 'is-active is-info': q.dm == 'g' }" v-on:click="q.dm = 'g'">
							<span class="icon is-medium"><i class="mdi mdi-image-multiple"></i></span>
						</button>
					</div>
				</div>
				<div class="column is-narrow">
					<div class="field has-addons">
						<div class="control">
							<button class="button is-primary" v-on:click="itemUpload({ slug: null })">
								<span class="icon is-medium"><i class="mdi mdi-cloud-upload"></i></span>
								<span>Hochladen</span>
							</button>
						</div>
						<div class="control">
							<button class="button is-secondary" v-on:click="addLinkModal = true">
								<span class="icon is-medium"><i class="mdi mdi-link-plus"></i></span>
								<span>Link</span>
							</button>
						</div>
					</div>
				</div>
			</div>

			<div class="loader-wrapper is-active" v-if="loading">
				<div class="loader is-loading"></div>
            </div>
			
			<div class="table-container">
				<table v-if="q.dm == 't'" class="table is-fullwidth is-striped">
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
							<td>{{ app.baseUrl + '/' + item.slug }}</td>
							<td></td>
							<td>{{ item.clicks || 0 }}</td>
							<td>{{ item.type }}</td>
							<td>{{ item.created_at }}</td>

							<td>
								<a v-on:click="itemModal = item"><i class="mdi mdi-information"></i></a>
								<a v-on:click="itemDelete(item)"><i class="mdi mdi-delete"></i></a>
								<a v-on:click="itemUpload(item)" v-if="item.path"><i class="mdi mdi-cloud-upload"></i></a>
							</td>

						</tr>
					</tbody>
				</table>
			</div>
			
			<div v-if="q.dm == 'g'">
				<div class="tiles">
					<div v-for="item in items" class="tiles__item" v-bind:style="'background-image: url(/thumb/'+ item.slug">
						<div class="tiles__item__toolbar">
						
							<span class="tiles__item__buttons">
								<a v-on:click="itemModal = item"><i class="mdi mdi-information"></i></a>
								<a v-on:click="itemDelete(item)"><i class="mdi mdi-delete"></i></a>
								<a v-on:click="itemUpload(item)" v-if="item.path"><i class="mdi mdi-cloud-upload"></i></a>
							</span>
							<a :href="app.baseUrl + '/' + item.slug">
								<span v-if="item.title.length < 64">{{ item.title }}</span>
								<span v-else>{{ item.title.substring(0, 32) + '...' }}</span>
							</a>
						</div>
					</div>
				</div>
			</div>

			<nav class="pagination" role="navigation" aria-label="pagination">
				<ul class="pagination-list">
					<li v-for="n in Math.ceil(itemsTotal/q.l)" >
						<a class="pagination-link" :class="{'is-current': n == q.p}" v-on:click="q.p = n; loadItems()">{{ n }}</a>
					</li>
				</ul>
			</nav>

			<form style="display:none;" id="itemUploadForm">
				<input type="hidden" name="_csrf" v-bind:value="app.csrf">
				<input type="hidden" name="overwriteSlug" v-model="itemToUpload.slug">
				<input type="file" v-on:change="itemUploadDo" name="file" id="itemUploadInput">
				<input type="submit">
			</form>
		</div>
	`,

	components: {
		ItemModal,
		AddLinkModal: () => import('./AddLinkModal.js')
	},

	data() {
		return {
			itemModal: null,
			addLinkModal: false,

			items: [],
			itemsTotal: 0,
			imageThumbPath: null,
			itemToUpload: {slug: null, url: null},

			q: { 
				dm: 'g',
				p: 1, // page
				l: 25, // limit
				s: '', // search
			},
			loading: true
		}
	},

	created() {
		if(typeof this.$route.params.q !== 'undefined') {
			try {
				this.q = JSON.parse(atob(this.$route.params.q));
			} catch {}
		}
		this.loadItems();

	},

	methods: {
		loadItems() {
			this.loading = true;

			let url = '/api/v1/items?_t=' + (Date.now() / 1000 | 0);
			
			url += `&page=${this.q.p}&limit=${this.q.l}`;

			app.fetch(url)
				.then(res => res.json())
				.then(json => {
					let q = btoa(JSON.stringify(this.q));
					if(q != this.$route.params.q)
						this.$router.push('/items/' + q);
					this.items = json.items;
					this.itemsTotal = json.total;
					this.loading = false;
				});
		},

		itemDelete(item) {
			item.title = 'wird gelöscht ...';
			app.fetch('/api/v1/item/'+item.slug, {
				method: 'DELETE'
			}).then(r => {
				this.items.splice(this.items.indexOf(item), 1);
			});
		},

		itemUpload(item) {
			this.itemToUpload = item;
			document.getElementById('itemUploadInput').click();
		},

		itemUploadDo() {
			var self = this;
			var frm = document.getElementById('itemUploadForm');
			var frmData = new FormData(frm);
			var oReq = new XMLHttpRequest();
			oReq.open('POST', '/api/v1/upload', true);

			

			oReq.onload = _ => {
				if (oReq.status == 200) {
					self.loadItems();
				} else {
					this.$buefy.snackbar.open({
						message: `Upload failed. Please check max size (${app.uploadMaxFilesize})`,
						position: 'is-bottom-right',
						type: 'is-danger',
						duration: 5000,
						queue: false
					});
				}
			};

			oReq.send(frmData);
		},

		imageMouseOver(item) {
			this.imageThumbPath = '/thumb/'+item.slug;
		},

		imageMouseLeave() {
			this.imageThumbPath = null;
		}
	}


}
