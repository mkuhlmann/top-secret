export default {
	template: /*html*/`
        <div class="content">
            <article class="message is-primary">
                <div class="message-header">
                    <p>Aufbewahrung</p>
                </div>
                <div class="message-body">
                    Es stehen {{ dryRun.deletedItems }} Items ({{ Math.round(dryRun.deletedSize/1024/1024*100)/100 }} MiB) zum Löschen an. Du kannst die Richtlinie unter Einstellungen ändern.               
                </div>
            </article>
            <div>
                <input type="checkbox" v-model="safteyCheck"> Ich möchte löschen.
            <div>
             <button class="ui danger button" v-if="safteyCheck" v-on:click="nuke()">Jetzt ausführen!</button>
        </div>
	`,

	data() { return {
		dryRun: {},
		run: 0,
		safteyCheck: false
    } },
    
	created() {
		this.load();
    },
    
	methods: {
		load() {
            fetch('/tsa/retentionDryRun')
                .then(res => res.json())
                .then(res => {
				    this.dryRun = res;
			    });
        },
        
		nuke() {
			this.run = 1;
		    fetch('/tsa/retentionRun', {_csrf: app._csrf}).then(res => res.json())
            .then(res => {
                this.run = 2;
            });
		}
	}

}