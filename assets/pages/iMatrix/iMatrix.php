<div id="iMatrixCont" style="min-height:500px">

</div>

<script>
	/* Vue3 Start*/
	const {
		createApp
	} = Vue

	createApp({
		data() {
			return {
				isLoading: null
			}
		},
		methods: {

		},
		mounted() {
			$('#appLoader').dimmer({
				closable: false
			}).dimmer('show');
			$.post('?config=iMatrixConfig', {
				view: true
			}, (data, textStatus, xhr) => {
				$("#iMatrixCont").html(data);
				$('#appLoader').dimmer('hide');
			});
		}

	}).mount('#iMatrixCont')
	/* Vue3 End*/
</script>