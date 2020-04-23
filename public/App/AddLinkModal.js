export default {
	template: /*html*/`
		<div>
			<div class="modal is-active">
				<div class="modal-background"></div>
				<div class="modal-card">
					<header class="modal-card-head">
						<p class="modal-card-title">Link hinzufügen</p>
						<button class="delete" aria-label="close" v-on:click="$emit('close')"></button>
					</header>
					<section class="modal-card-body">
						<div v-if="error" class="notification is-danger">
							{{ error }}
						</div>
						<div class="field">
							<label class="label">Uri</label>
							<div class="control">
								<input class="input" v-model="url" type="text" placeholder="https://...">
							</div>
						</div>
						<div class="field">
							<label class="label">Slug</label>
							<div class="control">
								<input class="input" v-model="slug" type="text" placeholder="abc">
							</div>
							<p class="help">optional</p>
						</div>
					</section>
					<footer class="modal-card-foot">
						<button class="button is-primary" :class="{'is-loading': loading}" v-on:click="save()">Speichern</button>
						<button class="button is-secondary" v-on:click="$emit('close')">Schließen</button>
					</footer>
				</div>
			</div>
		</div>
	`,
	
	props: ['item'],

	data() {
		return {
			url: '',
			slug: '',
			loading: false,
			error: null
		}
	},

	created() {
		console.log(this.item);
	},

	methods: {
		save() {
			this.loading = true;
			let body = { url: this.url };
			if(this.slug.length > 0) body.slug = this.slug;

			app.fetch('/api/v1/link', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify(body)
			})
				.then(r => r.json())
				.then(r => {
					this.loading = false;
					if(!r.error) {
						this.$emit('close');
					} else {
						this.error = r.error;
					}
				});
		}
	}


}
