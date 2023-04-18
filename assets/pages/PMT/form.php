<div id="showFormsApp">
	<div class="ui basic segment center aligned" style="margin-left: 200px; margin-right: 200px;">
		<!-- <h1 class="ui header" style="margin-bottom: 0px; " v-if="period && year">{{department}}</h1> -->
		<h2 style="margin-top: 0px;">{{ period + " " + year }}</h2>
	</div>

	<div class="ui basic segment" style="margin-left: 200px; margin-right: 200px;">
		<div v-for="item,i in items" :key="i">
			{{ item }}
			<hr>
		</div>
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
				id: new URL(window.location.href).searchParams.get("id"),
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
					initLoadForm: true,
					id: this.id,
				}, (data, textStatus, xhr) => {
					const res = JSON.parse(data)
					// console.log("initLoad: ", res);
					this.period = res.period
					this.year = res.year
					this.items = res.data
					console.log(res);
				});
			}


		},
		mounted() {
			this.initLoad()
		}

	}).mount('#showFormsApp')
	/* Vue3 End*/
</script>