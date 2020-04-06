
const app = window.app = {
	key: document.querySelector('meta[name="key"]').content,
	csrf: document.querySelector('meta[name="csrf"]').content,
	baseUrl: document.querySelector('meta[name="baseUrl"]').content,
	uploadMaxFilesize: document.querySelector('meta[name="uploadMaxFilesize"]').content,

    fetch(input, init) {
        init = init || {};        
			init.headers = init.headers || {};
			init.headers['Authorization'] = 'Bearer ' + app.key;
			
		if(init.method && init.method.toLowerCase() != 'get') {
			if(!init.body || typeof init.body == 'object') {
				init.body = init.body || {};
				init.body._csrf = app.csrf;
			}

			if(!init.headers['Content-Type']) {
				init.headers['Content-Type'] = 'application/json';
			}

			if(init.headers['Content-Type'] == 'application/json' && typeof init.body == 'object') {
				init.body = JSON.stringify(init.body);
			}
		}

		
		return fetch(input, init);
	}
};

const router = new VueRouter({
	//mode: 'history',
	routes: [
		{ path: '/', redirect: '/items' },
		{ path: '/items/:q?', component: () => import('./ItemView.js') },
		{ path: '/config', component: () => import('./ConfigView.js') },
		{ path: '/retention', component: () => import('./RetentionView.js') }
	]
});

Vue.filter('formatUnix', t => moment.unix(t).format('DD.MM.YYYY HH:mm:SS'));
Vue.prototype.app = app;

const vueApp = new Vue({
	el: '#app',
	router,
	
	data: {
		navbarOpen: false
	},

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
});
