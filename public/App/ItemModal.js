export default {
	template: /*html*/`
		<div>

			<div class="modal is-active">
				<div class="modal-background"></div>
				<div class="modal-card">
					<header class="modal-card-head">
						<p class="modal-card-title">{{ item.slug }}</p>
						<button class="delete" aria-label="close" v-on:click="$emit('close')"></button>
					</header>
					<section class="modal-card-body">
						<div class="columns">
						
							<div class="column is-4">
								<div class="ui medium image">
									<img :src="'/thumb/'+item.slug">
								</div>
							</div>

							<div class="column">
								<table class="table is-striped">
									<tr><th>Dateiname</th><td>{{ item.name }}</td></tr>
									<tr>
										<th>Slug</th>
										<td>
											<b-field>
												<b-input type="text" v-model="newSlug"></b-input>
												<p class="control">
													<b-button type="is-success" icon-right="content-save" v-on:click="saveSlug"></b-button>
												</p>
											</b-field>
										</td>
									</tr>
									<tr><th>Mime</th><td>{{ item.mime }} (.{{ item.extension }})</td></tr>

									<tr><th>{{ $t('general.size') }}</th><td>{{ item.size | humanFileSize }}</td></tr>

									<tr><th>Letzer Aufruf</th><td>{{ item.last_hit_at }}</td></tr>
									<tr><th>Hits</th><td>{{ item.clicks }}</td></tr>

									<tr><th>Erstellt</th><td>{{ item.created_at }}</td></tr>
								</table>
							</div>


						</div>
					</section>
					<footer class="modal-card-foot">
						<button class="button" v-on:click="$emit('close')">Close</button>
					</footer>
				</div>
			</div>
		</div>
	`,
	
	props: ['item'],

	data() {
		return {
			newSlug: ''
		}
	},

	created() {
		this.newSlug = this.item.slug;

	},

	methods: {

		saveSlug() {
			let clone = { ...this.item };
			clone.slug = this.newSlug;

			app.fetch(`/api/v1/item/${this.item.slug}`, {
				method: 'PUT',
				body: { item: clone }
			}).then(res => res.json())
			.then(json => {
				this.newSlug = this.item.slug = json.slug;
				this.$buefy.snackbar.open({
					message: `Saved.`,
					position: 'is-bottom-right',
					type: 'is-success',
					duration: 1500,
					queue: false
				});
			});
		}
	}


}
