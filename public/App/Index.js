
import i18nMessages from './I18nMessages.js';

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

const i18n = new VueI18n({
	locale: (navigator.language || navigator.userLanguage).split('-')[0],
	fallbackLocale: 'en',
	messages: i18nMessages
});

const router = new VueRouter({
	//mode: 'history',
	routes: [
		{ path: '/', redirect: '/items' },
		{ path: '/items/:q?', component: () => import('./ItemView.js') },
		{ path: '/config', component: () => import('./ConfigView.js') },
		{ path: '/retention', component: () => import('./RetentionView.js') },
		{ path: '/tags', component: () => import('./TagsView.js') }
	]
});

Vue.filter('formatUnix', t => moment.unix(t).format('DD.MM.YYYY HH:mm:SS'));
Vue.prototype.app = app;

const vueApp = new Vue({
	i18n,
	router,
	
	template: '<App />',
	components: { App: () => import('./App.js') }
}).$mount('#app');

function humanFileSize(bytes, si=false, dp=1) {
	const thresh = si ? 1000 : 1024;
  
	if (Math.abs(bytes) < thresh) {
	  return bytes + ' B';
	}
  
	const units = si 
	  ? ['kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'] 
	  : ['KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];
	let u = -1;
	const r = 10**dp;
  
	do {
	  bytes /= thresh;
	  ++u;
	} while (Math.round(Math.abs(bytes) * r) / r >= thresh && u < units.length - 1);
  
  
	return bytes.toFixed(dp) + ' ' + units[u];
  }

Vue.filter('humanFileSize', s => humanFileSize(s)); 
