<template id="tpl-index">
	<table class="ui table">
		<thead>
			<tr>
				<th>Datei</th>
				<th>Link</th>
				<th>Hits</th>
				<th>Typ</th>
				<th>Hochgeladen</th>
			</tr>
		</thead>

		<tbody>
			<tr v-for="item in items">
				<td>
					<span v-if="item.type == 'image'">
						<a v-on:mouseleave="imageMouseLeave" v-on:mouseover="imageMouseOver(item)" href="{{ item.path }}">{{ item.title }}</a>
					</span>
					<span v-if="item.type == 'binary' || item.type == 'url'">
						<a href="{{ item.path }}">{{ item.title }} [...]</a><span v-if="item.type == 'url'"></span>
					</span>
				</td>
				<td><span style="font-family: monospace;"><?php echo app()->config->baseUrl; ?>/{{item.slug}}</span></td>
				<td>{{ item.clicks || 0 }}</td>
				<td>{{ item.type }}</td>
				<td>{{ item.created_at }} <a v-on:click="itemDelete(item)">D</a></td>
			</tr>
		</tbody>
	</table>
	<div style="position: fixed; top: 0; right: 0;"><img v-bind:src="imageThumbPath"></div>
</template>
