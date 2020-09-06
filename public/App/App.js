
export default {
	template: /*html*/`
<div id="app">
<header>
	<nav class="navbar is-dark" role="navigation" aria-label="main navigation">
		<div class="navbar-brand">
			<a class="navbar-item has-text-primary" href="/tsa2">
				Admin <sup style="color: #888;">v2</sup>
			</a>

			<a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" v-on:click="navbarToggle()"  v-bind:class="{ 'is-active': navbarOpen }">
				<span aria-hidden="true"></span>
				<span aria-hidden="true"></span>
				<span aria-hidden="true"></span>
			</a>
		</div>

		<div class="navbar-menu" v-bind:class="{ 'is-active': navbarOpen }">
			<div class="navbar-start" v-on:click="navbarNavigate()">
				<router-link to="/items" class="navbar-item" active-class="is-active">
					<span class="icon is-medium"><i class="mdi mdi-view-list"></i></span>
					<span>{{ $t('menu.uploads') }}</span>
				</router-link>
				<router-link to="/tags" class="navbar-item" active-class="is-active">
					<span class="icon is-medium"><i class="mdi mdi-tag-multiple"></i></span>
					<span>{{ $t('menu.tags') }}</span>
				</router-link>
				<router-link to="/retention" class="navbar-item" active-class="is-active">
					<span class="icon is-medium"><i class="mdi mdi-history"></i></span>
					<span>{{ $t('menu.retention') }}</span>
				</router-link>
				<router-link to="/config" class="navbar-item" active-class="is-active">
					<span class="icon is-medium"><i class="mdi mdi-settings"></i></span>
					<span>{{ $t('menu.settings') }}</span>
				</router-link>
			</div>

			
			<div class="navbar-end">
				<a href="/tsa/logout" class="navbar-item">
					<span class="icon is-medium"><i class="mdi mdi-logout"></i></span> 
					<span>{{ $t('menu.logout') }}</span>
				</a>
			</div>

		</div>

	</nav>
</header>

<main>
	<router-view></router-view>
</main>
</div>
`,
	data() { return {
		navbarOpen: false
	} },

	methods: {
		navbarToggle() {
			this.navbarOpen = !this.navbarOpen;
		},

		navbarNavigate() {
			if(this.navbarOpen) this.navbarOpen = false;
		}
	},

	created() {
		
	}
}
