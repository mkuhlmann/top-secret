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
						
							<div class="column">
								<div class="ui medium image">
									<img :src="'/thumb/'+item.slug">
								</div>
							</div>

							<div class="column">
								<table class="table is-striped">
									<tr><th>Dateiname</th><td>{{ item.name }}</td></tr>
									<tr><th>Mime</th><td>{{ item.mime }} (.{{ item.extension }})</td></tr>

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
		}
	},

	created() {
		console.log(this.item);
	},

	methods: {
	}


}