import ItemModal from './ItemModal.js';

export default {
	template: /*html*/`
		<div>
			<item-modal v-if="itemModal" v-bind:item="itemModal" v-on:close="itemModal = null"></item-modal>

			<div style="position: absolute; top: 0; right: 0;"><img v-bind:src="imageThumbPath"></div>

			<table class="table is-fullwidth is-striped">
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
						<td></td>
						<td></td>
						<td></td>

						<td>
							<a v-on:click="itemModal = item"><i class="mdi mdi-information"></i></a>
							<a v-on:click="itemDelete(item)"><i class="mdi mdi-delete"></i></a>
							<a v-on:click="itemUpload(item)" v-if="item.path"><i class="mdi mdi-cloud-upload"></i></a>
						</td>

					</tr>
				</tbody>
			</table>
		</div>
	`,

	components: {
		ItemModal
	},

	data() {
		return {
			itemModal: null,

			items: [],
			itemsTotal: 0,
			imageThumbPath: null
		}
	},

	created() {
		this.loadItems();
	},

	methods: {
		loadItems() {
			let url = '/api/v1/items?_t=' + (Date.now() / 1000 | 0);

			app.fetch(url)
				.then(res => res.json())
				.then(json => {
					this.items = json.items;
					this.itemsTotal = json.total;
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