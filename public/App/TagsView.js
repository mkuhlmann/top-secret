export default {
	template: /*html*/`
		<div class="content container">
			<div class="columns" v-for="tag in tags">

				<div class="field column">
					<label class="label">Name</label>
					<div class="control">
						<input class="input" v-model="tag.name" v-on:change="save(tag)">
					</div>
				</div>
				<div class="field column">
					<label class="label">Farbe</label>
					<div class="control">
						<div class="select">
							<select v-model="tag.color" v-on:change="save(tag)">
								<option v-for="color in colors">{{ color }}</option>
							</select>
						</div>
					</div>
				</div>
				<div class="field column is-2">
					<label class="label">Löschen</label>
					<div class="control">
						<button class="button is-danger" v-on:click="del(tag)">
							<span class="icon"><i class="mdi mdi-delete"></i></span>
						</button>
					</div>
				</div>
				<div class="field column">
					<label class="label">Vorschau</label>
					<div class="control">
						<span class="tag" v-bind:class="tag.color">{{ tag.name }}</span>
					</div>
				</div>

			</div>

			<hr>

			<button class="button is-primary" v-on:click="add()">
				<span class="icon"><i class="mdi mdi-tag-plus"></i></span>
			</button>
			<em>Eingabefeld unfokusieren um Namen zu speichern!</em>
		</div>
	`,

	data() { return {
		colors: ['', 'red', 'orange', 'yellow', 'olive', 'green', 'teal', 'blue', 'violet', 'purple', 'pink', 'brown', 'grey', 'black'],
		tags: []
	} },
	
	created() {
		this.load();
	},
	
	methods: {
		load() {
			app.fetch('/api/v2/tags')
				.then(res => res.json())
				.then(json => {
					this.tags = json;
				});
		},
		save(tag) {
			app.fetch(`/api/v1/tags/${tag.id}`, {
				method: 'PUT',
				body: { tag }
			}).then(_ => {
				this.$buefy.snackbar.open({
					message: `Tag saved.`,
					position: 'is-bottom-right',
					type: 'is-success',
					duration: 2000,
					queue: false
				});
			});
		},
		add() {
			app.fetch('/api/v1/tags', {
				method: 'POST'
			}).then(this.load);
		},
		del(tag) {
			app.fetch(`/api/v1/tags/${tag.id}?_csrf=${app._csrf}`, {
				method: 'DELETE'
			}).then(this.load);
		},
	}

}
