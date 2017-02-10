<template id="tpl-tags">
	<div class="ui container">
		<div class="ui form">
			<div class="four fields" v-for="tag in tags">
				<div class="field">
					<label>Name</label>
					<input type="text" v-model="tag.name" v-on:focusout="save(tag)">
				</div>
				<div class="field">
					<label>Farbe</label>
					<select v-model="tag.color" v-on:change="save(tag)">
						<option v-for="color in colors">{{ color }}</option>
					</select>
				</div>
				<div class="field">
					<label>Löschen / Vorschau</label>
					<button class="ui icon small button" style="margin-right: 1.5em;" v-on:click="deleteTag(tag)">
						<i class="trash icon"></i>
					</button>
					<a :class="tag.color" class="ui tag label">{{ tag.name }}</a>
				</div>
			</div>
			<button class="ui icon primary button" v-on:click="add()">
				<i class="add icon"></i>
			</button>
			<em>Eingabefeld unfokusieren um Namen zu speichern!</em>
		</div>
	</div>
</template>
<!------------------------------------------>
<script type="text/javascript">
app.TagsCtrl = Vue.extend({
	template: '#tpl-tags',
	data: function() { return {
		colors: ['', 'red', 'orange', 'yellow', 'olive', 'green', 'teal', 'blue', 'violet', 'purple', 'pink', 'brown', 'grey', 'black'],
		tags: {}
	} },
	created: function() {
		this.load();
	},
	methods: {
		save: function(tag) {
			this.$http.put('/api/v1/tags/'+tag.id, {_csrf: app._csrf, tag: tag}).then(function(response) {
				this.tags[response.data.id] = response.data;
			});
		},
		add: function() {
			this.$http.post('/api/v1/tags', {_csrf: app._csrf}).then(function(response) {
				this.load();
			});
		},
		deleteTag: function(tag) {
			this.$http.delete('/api/v1/tags/'+tag.id+'?_csrf='+app._csrf).then(function(response) {
				this.load();
			});
		},
		load: function() {
			this.$http.get('/api/v1/tags').then(function(response) {
				this.tags = response.data;
			});
		}
	}
});
Vue.component('tags-ctrl', app.TagsCtrl);
</script>
