<template>
	<div class="chart-actions-list">
		<div class="chart-actions-list_header">
			Параметры
		</div>
		<div class="chart-actions-list_dropdown">
			<div class="chart-actions-list_dropdown-item" @click="onClick('collapse.posts')">
				<span>Отображать все должности сотрудников</span>
				<Toggle
					:value="expandPosts"
					@click="onClick('posts')"
					checked-bg="#3bc8f5"
					unchecked-color="#000000"
					width="45"
					/>
			</div>
			<div class="chart-actions-list_dropdown-item" @click="onClick('collapse.functions')">
				<span>Отображать все функции</span>
				<Toggle
					:value="expandFunctions"
					@click="onClick('functions')"
					checked-bg="#3bc8f5"
					unchecked-color="#000000"
					width="45"
				/>
			</div>
		</div>
	</div>
</template>

<script>
import Toggle from "./Toggle.vue";
export default {
	name: 'ActionList',
	components: {Toggle},
	data() {
		return {
			expandPosts: true,
			expandFunctions: true
		}
	},
	methods: {
		onClick(target) {
			switch(target) {
				case 'posts':
					this.expandPosts = !this.expandPosts;
					if(!this.expandPosts) {
						this.$root.$emit('collapse.posts');
					} else {
						this.$root.$emit('expand.posts');
					}
					break;
				case 'functions':
					this.expandFunctions = !this.expandFunctions;
					if(!this.expandFunctions) {
						this.$root.$emit('collapse.functions');
					} else {
						this.$root.$emit('expand.functions');
					}
					break;
			}
		}
	}
}
</script>

<style lang="scss" scoped >
.chart-actions-list {
	position: relative;
	height: 30px;
	margin-right: 15px;

	&_header {
		position: relative;
		display: inline-block;
		height: 30px;
		font: 12px/30px "OpenSans-Semibold", "Helvetica Neue", Arial, Helvetica, sans-serif;
		color: #545c6a;
		outline: 0;
		text-decoration: none;
		-webkit-transition: color .3s ease;
		transition: color .3s ease;
		vertical-align: middle;
		box-sizing: border-box;
		cursor: pointer;
		-khtml-user-drag: none;
		-webkit-user-drag: none;
		text-transform: none;
		-webkit-tap-highlight-color: rgba(0, 0, 0, 0);
		padding-right: 15px;

		&::after {
			position: absolute;
			top: 15px;
			right: 0;
			content: "";
			display: inline-block;
			width: 0;
			height: 0;
			border-style: solid;
			border-width: 4px 4px 0 4px;
			border-color: #717a84 transparent transparent transparent;
			-webkit-transition: border-color .3s ease;
			transition: border-color .3s ease;
		}
	}

	&_dropdown {
		display: none;
		width: 320px;
		position: absolute;
		right: 0px;
		background-color: #fff;
		-webkit-box-shadow: 0 7px 21px rgba(83, 92, 105, 0.12), 0 -1px 6px 0 rgba(83, 92, 105, 0.06);
		box-shadow: 0 7px 21px rgba(83, 92, 105, 0.12), 0 -1px 6px 0 rgba(83, 92, 105, 0.06);
		padding: 10px 0;
		font: 13px "Helvetica Neue",Helvetica,Arial,sans-serif;
		-webkit-box-sizing: border-box;
		box-sizing: border-box;
		/*display: -webkit-box;
		display: -ms-flexbox;
		display: flex;*/
		-webkit-box-orient: vertical;
		-webkit-box-direction: normal;
		-ms-flex-direction: column;
		flex-direction: column;
		-webkit-box-pack: stretch;
		-ms-flex-pack: stretch;
		justify-content: stretch;

		&-item {
			height: 30px;
			font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
			font-size: 12px;
			color: #525c68;
			background-color: transparent;
			display: -webkit-box;
			display: -ms-flexbox;
			display: flex;
			-webkit-box-align: center;
			-ms-flex-align: center;
			align-items: center;
			cursor: pointer;
			position: relative;
			text-decoration: none;
			outline: 0;
			white-space: nowrap;
			padding: 0 10px;
			justify-content: space-between;

			/*&:hover {
				background-color: #f5f5f6;
			}*/
		}
	}

	&:hover {

		.chart-actions-list_dropdown {
			display: flex;
		}
	}
}
</style>