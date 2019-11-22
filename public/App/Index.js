import ItemView from './ItemView.js';

const app = window.app = {
    apiKey: document.querySelector('meta[name="apiKey"]').content,

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
		{ path: '/', component: ItemView },
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