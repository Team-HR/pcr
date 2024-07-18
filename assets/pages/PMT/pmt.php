<div id="pmtIndexApp">
	<div class="ui containger center aligned" style="margin: 20px;">
		<h1 class="ui header center aligned" v-if="period && year">{{period}} {{year}}</h1>
		<div class="ui form" style="width: 40%; margin: auto;">
			<div class="ui two column fields">
				<div class="field">
					<select name="period" id="selectPeriod" class="ui dropdown" v-model="period">
						<option value="" selected disabled>Select Period</option>
						<option value="January - June">January - June</option>
						<option value="July - December">July - December</option>
					</select>
				</div>
				<div class="field">
					<select name="year" id="selectYear" class="ui dropdown" v-model="year">
						<option value="" selected disabled>Select Year</option>
						<option value="2024"> 2024</option>
						<option value="2023"> 2023</option>
						<option value="2022">2022</option>
						<option value="2021">2021</option>
					</select>
				</div>
				<!-- <div class="field">
					<button type="submit" class="ui fluid primary button">Get Departments</button>
				</div> -->
			</div>
		</div>
		<!-- cards -->
		<div v-if="!departments" class="ui container center aligned">
			<i style="font-size: 20px; color: grey;">Please select a period and year</i>
		</div>
		<div v-else-if="departments.length < 1" class="ui container center aligned">
			<i style="font-size: 20px; color: red;">No assigned departments</i>
		</div>
		<div v-else class="ui centered link cards">
			<template v-for="department,i in departments" :key="i">
				<div class="card">
					<div class="image">
						<img src="assets/img/folder.jpg" style="width:50%;margin:auto">
					</div>
					<div class="content">
						<div class="header">{{department.department}}</div>
						<div class="meta">
							<a>Name of Department</a>
						</div>
					</div>
					<div class="extra">
						<!-- <a class="fluid ui primary button" @click.prevent="getEmployees(department.department_id)">Open</a> -->
						<a class="fluid ui primary button" :href=`?showForms&period_id=${period_id}&department_id=${department.department_id}`>Open</a>
						<br>
						<a class="fluid ui primary button" :href=`?showRsmView&period=${period_id}&department=${department.department_id}`>Show RSM</a>
					</div>
				</div>
			</template>
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
				period_id: null,
				departments: null
			}
		},
		watch: {
			period(newValue, oldValue) {
				if (this.period && this.year) {
					this.getDepartments()
				}
			},
			year(newValue, oldValue) {
				if (this.period && this.year) {
					this.getDepartments()
				}
			}
		},
		computed: {

		},
		methods: {

			initSession() {
				// get session data for pmt
				$.post('?config=PMT', {
					initSession: true,
				}, (data, textStatus, xhr) => {
					const res = JSON.parse(data)
					const sel = res ? res : {}
					if (sel.period && sel.year && sel.period_id) {
						this.period = sel.period
						this.year = sel.year
						this.period_id = sel.period_id
					}
				});
			},

			getDepartments() {
				$.post('?config=PMT', {
					getDepartments: true,
				}, (data, textStatus, xhr) => {
					this.departments = JSON.parse(data)
					this.getPeriodId()
				});
			},

			getPeriodId() {
				$.post('?config=PMT', {
					getPeriodId: true,
					period: this.period,
					year: this.year,
				}, (data, textStatus, xhr) => {
					this.period_id = JSON.parse(data)
				});
			},

			getEmployees(department_id) {
				console.log(department_id);
				$.post('?config=PMT', {
					getEmployees: true,
					departmentId: department_id,
					period: this.period_id
				}, (data, textStatus, xhr) => {
					const res = JSON.parse(data)
					console.log("employees: ", res);
				});
			}


		},
		mounted() {
			this.initSession();
		}

	}).mount('#pmtIndexApp')
	/* Vue3 End*/
</script>