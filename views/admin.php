<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.1/semantic.min.css" />
	<style type="text/css">
	body { top: 0; left: 0; }
	a { cursor: pointer; }
	</style>
	<title>TopSecret Admin</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8">
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.25/vue.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue-resource/0.9.1/vue-resource.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue-router/0.7.13/vue-router.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
</head>
<body>
	<div class="ui container" id="app">
		<router-view></router-view>
	</div>
	<!-- vue.js templates here -->
	<?php $files = glob(dirname(__FILE__).'/admin/*.{html,php}', GLOB_BRACE);
	foreach($files as $file) {
		include $file; // full path
	} ?>

	<script type="text/javascript">
	var app = {};
	app.IndexCtrl = Vue.extend({
		template: '#tpl-index',
		data: _ => { return {
			items: [],
			imageThumbPath: null
		} },
		created: function() {
			this.$http.get('/api/v1/items').then(function(response) {
				this.items = response.data;
			});
		},
		methods: {
			imageMouseOver: function(item) {
				this.imageThumbPath = '/thumb/'+item.slug;
			},
			imageMouseLeave: function() {
				this.imageThumbPath = null;
			},
			itemDelete: function(item) {
				this.$http.post('/api/v1/item/'+item.slug+'/delete').then(function(repsonse) {

				});
				this.items.splice(this.items.indexOf(item), 1);
			}
		}
	});

	app.Root = Vue.extend({
		data: function() {
			return {
			}
		},
		created: function() {}
	});

	app.router = new VueRouter({
		linkActiveClass: 'active'
	});
	app.router.map({
		'/': { component: app.IndexCtrl }
	});
	window.onload = function() {
		app.router.start(app.Root, '#app');
	}
	</script>
</body>
</html>
