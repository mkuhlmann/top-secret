
const app = window.app = {
	key: document.querySelector('meta[name="key"]').content,
	csrf: document.querySelector('meta[name="csrf"]').content,

    fetch(input, init) {
        init = init || {};        
			init.headers = init.headers || {};
            init.headers['Authorization'] = 'Bearer ' + app.apiKey;
		return fetch(input, init);
	}
};

const router = new VueRouter({
	//mode: 'history',
	routes: [
		{ path: '/', redirect: '/items' },
		{ path: '/items/:q?', component: () => import('./ItemView.js') },
		{ path: '/config', component: () => import('./SettingsView.js') },
		{ path: '/retention', component: () => import('./RetentionView.js') }
	]
});

Vue.filter('formatUnix', t => moment.unix(t).format('DD.MM.YYYY HH:mm:SS'));

const vueApp = new Vue({
	el: '#app',
	router,
	
	data: {
        
	},
	
	created() {
		
	}
});