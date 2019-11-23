import ItemModal from './ItemModal.js';

export default {
	template: /*html*/`
		<div>
			<item-modal v-if="itemModal" v-bind:item="itemModal" v-on:close="itemModal = null"></item-modal>

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
				<div class="column  is-narrow">
					<div class="field has-addons">
						<div class="control">
							<button class="button is-primary">
								<span class="icon is-medium"><i class="mdi mdi-cloud-upload"></i></span>
								Hochladen
							</button>
						</div>
						<div class="control">
							<button class="button is-secondary">
								<span class="icon is-medium"><i class="mdi mdi-link-plus"></i></span>
								Link
							</button>
						</div>
					</div>
				</div>
			</div>

			<div class="loader-wrapper is-active" v-if="loading">
				<div class="loader is-loading"></div>
            </div>
			
			<table v-if="displayMode == 'table'" class="table is-fullwidth is-striped">
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
						<td></td>
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

			<div v-if="displayMode == 'gallery'">
				<div v-for="item in items" class="tiles__item" v-bind:style="'background-image: url(/thumb/'+ item.slug">
					<div class="tiles__item__toolbar">
						<span v-if="item.title.length < 32">{{ item.title }}</span>
						<span v-else>{{ item.title.substring(0, 32) + '...' }}</span>
						<div class="gallery__image__buttons">
							<a v-on:click="itemModal = item"><i class="mdi mdi-information"></i></a>
							<a v-on:click="itemDelete(item)"><i class="mdi mdi-delete"></i></a>
							<a v-on:click="itemUpload(item)" v-if="item.path"><i class="mdi mdi-cloud-upload"></i></a>
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
		</div>
	`,

	components: {
		ItemModal
	},

	data() {
		return {
			displayMode: 'gallery',
			itemModal: null,

			items: [],
			itemsTotal: 0,
			imageThumbPath: null,

			q: { 
				p: 1, // page
				l: 2, // limit
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

		imageMouseOver(item) {
			this.imageThumbPath = '/thumb/'+item.slug;
		},

		imageMouseLeave() {
			this.imageThumbPath = null;
		}
	}


}