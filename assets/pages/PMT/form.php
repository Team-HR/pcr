<div id="showFormsApp">
	<div class="ui basic segment center aligned" style="margin-left: 200px; margin-right: 200px;">
		<h1 class="ui header" style="margin-bottom: 0px; " v-if="period && year">{{ file_status.department }}</h1>
		<h2 style="margin-top: 0px;">{{ period + " " + year }}</h2>
	</div>

	<div class="ui segment" style="margin-left: 20px; margin-right: 20px;">
		<!-- 
		<p>
			I, <b>{{ file_status.name }}</b> , _______________________ of the <b>{{ file_status.department }}</b> commit to deliver and agree to be rated on the attainment of the following targets in accordance with the indicated measures for the period {{ period + " " + year }}.
		</p> -->

		<table style="width: 100%; _background-color:antiquewhite; border: 1px solid grey; border-collapse:collapse;">
			<tr>
				<td colspan="4" style="text-align: center;">
					<h4>{{ file_status.form_type }}</h4>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					I, <b>{{ file_status.name }}</b> , _______________________ of the <b>{{ file_status.department }}</b> commit to deliver and agree to be rated on the attainment of the following targets in accordance with the indicated measures for the period {{ period + " " + year }}.
				</td>
			</tr>
			<tr>
				<td colspan="3"></td>
				<td style="text-align: center; width: 400px;">
					<u><b>{{file_status.name}}</b></u> <br>
					Ratee
				</td>
			</tr>
			<tr style="background-color: #00ffdc14;">
				<td style="border: 1px solid grey;">
					<span style="font-size: 9px;">Received by:</span><br>
					<div class="ui fluid container center aligned">
						<u><b>{{file_status.name_supervisor}}</b></u> <br>
						<span>Immediate Superior</span>
					</div>
				</td>
				<td style="border: 1px solid grey;">
					<span style="font-size: 9px;">Noted by:</span><br>
					<div class="ui fluid container center aligned">
						<u><b>{{file_status.name_department_head}}</b></u> <br>
						<span>Department Head</span>
					</div>
				</td>
				<td style="border: 1px solid grey;">
					<span style="font-size: 9px;">Approved by:</span><br>
					<div class="ui fluid container center aligned">
						<u><b>{{file_status.name_head_of_agency}}</b></u> <br>
						<span>Head of Agency</span>
					</div>
				</td>
				<td style="border: 1px solid grey;">
					<span style="font-size: 9px;">Date:</span><br>
					<div class="ui fluid container center aligned">
						<u><b>{{ file_status.dateAccomplished }}</b></u> <br>
						<span style="opacity: 0;">(Date Accomplished)</span>
					</div>
				</td>
			</tr>
		</table>
		<br>

		<table class="ui structured celled table">
			<tr>
				<td rowspan="2">MFO / PAP</td>
				<td rowspan="2">Success Indicator</td>
				<td rowspan="2">Actual Accomplishment</td>
				<td colspan="4">Rating Matrix</td>
				<td rowspan="2">Remarks</td>
				<td rowspan="2">Options</td>
			</tr>
			<tr>
				<td>Q</td>
				<td>E</td>
				<td>T</td>
				<td>A</td>
			</tr>
			<template v-for="item,i in items" :key="i">
				<!-- if no success indicators -->
				<tr v-if="item.colspan == 'all'">
					<td colspan="9">
						<div :style="getMargin(item.level)">{{ item.cf_count }} {{ item.cf_title }}</div>
					</td>
				</tr>
				<!-- else if has success indicators -->
				<tr v-else-if="item.colspan == 0">
					<td :rowspan="item.rowspan">
						<div :style="getMargin(item.level)">{{ item.cf_count }} {{ item.cf_title }}</div>
					</td>
					<td>
						{{item.mi_succIn}}
					</td>
					<td>{{item.actualAcc}}</td>
					<td>{{item.q}}</td>
					<td>{{item.e}}</td>
					<td>{{item.t}}</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr v-else-if="!item.cf_title">
					<td>
						{{item.mi_succIn}}
					</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</template>
		</table>

		<template v-for="item,i in items" :key="i">
			<div>{{item}}</div>
			<hr>
		</template>

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
				file_status: {},
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
			getMargin(level) {
				const margin = level * 30;
				return `margin-left:${margin}px;`
			},
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
					this.file_status = res.file_status
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