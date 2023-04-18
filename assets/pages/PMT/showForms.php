<div id="showFormsApp">
	<div class="ui basic segment center aligned" style="margin-left: 200px; margin-right: 200px;">
		<h1 class="ui header" style="margin-bottom: 0px; " v-if="period && year">{{department}}</h1>
		<h2 style="margin-top: 0px;">{{ period + " " + year }}</h2>
	</div>

	<div class="ui basic segment" style="margin-left: 200px; margin-right: 200px; height: 720px; /* overflow-y:scroll; */">
		<table class="ui mini structured celled table">
			<thead>
				<tr>
					<th>Form Type</th>
					<th>Name</th>
					<th>Submitted</th>
					<th>Approved</th>
					<th>Certified</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<template v-for="item,i in items" :key="i">
					<tr>
						<td width="25">{{ item.formType }}</td>
						<td>{{ item.name }}</td>
						<td width="25">{{ item.date_submitted }}
						<td width="100">{{ item.date_approved}}</td>
						<td width="100">{{ item.date_certified }}</td>
						<td width="25">
							<a class="ui small primary button" :href="`?showForm&id=${item.id}`">Open</button>
						</td>
					</tr>
				</template>
			</tbody>
		</table>
	</div>

</div>


<script>
	/* Vue3 Start*/
	const {
		createApp
	} = Vue

	createApp({
		data() {
			return {
				period: "",
				year: "",
				department: "",
				period_id: new URL(window.location.href).searchParams.get("period_id"),
				department_id: new URL(window.location.href).searchParams.get("department_id"),
				items: null
			}
		},
		watch: {

		},
		computed: {

		},
		methods: {

			initLoad(department_id) {
				console.log(department_id);
				$.post('?config=PMT', {
					initLoad: true,
					department_id: this.department_id,
					period_id: this.period_id
				}, (data, textStatus, xhr) => {
					const res = JSON.parse(data)
					// console.log("initLoad: ", res);
					this.period = res.period
					this.year = res.year
					this.department = res.department
					this.items = res.data
				});
			}


		},
		mounted() {
			this.initLoad()
		}

	}).mount('#showFormsApp')
	/* Vue3 End*/
</script>