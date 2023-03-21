<div id='finalNumericalRatingsApp' class="ui segment" style="margin-left: 25px; margin-right: 25px;">
	<h1 class="ui header block">HRMO Dashboard | Final Numerical Ratings</h1>
	<div class="ui fluid basic segment">
		<!-- <li v-for="item in items" :key="item.id">{{item}}</li> -->
		<div class="ui form" style="width: 820px; margin:auto; margin-bottom: 20px;">
			<div class="fields">
				<div class="field" style="width: 220px;">
					<label> Select Period:</label>
					<div id="periodDropdown" class="ui fluid search selection dropdown">
						<input type="hidden" name="period">
						<i class="dropdown icon"></i>
						<div class="default text">Select Period</div>
						<div class="menu">
							<template v-for="period in periods" :key="period.period_id">
								<div class="item" :data-value="period.period_id">{{period.period}}</div>
							</template>
						</div>
					</div>
				</div>
				<div class="field" style="width: 600px;">
					<label> Select Department:</label>
					<div id="departmentDropdown" class="ui fluid search selection dropdown">
						<input type="hidden" name="department">
						<i class="dropdown icon"></i>
						<div class="default text">Select Department</div>
						<div class="menu">
							<div class="item" data-value="all">All</div>
							<template v-for="dept in departments" :key="dept.department_id">
								<div class="item" :data-value="dept.department_id">{{dept.department}}</div>
							</template>
						</div>
					</div>
				</div>
			</div>
		</div>





		<div style="padding: 20px; position: relative; width:1000px; margin: auto; background-color:azure" :style="chartHeight">
			<canvas id="myChart"></canvas>
		</div>
		<br>
		<br>
		<h1 style="text-align: center; margin: 0">{{selectedDepartment}}</h1>
		<h3 style="text-align: center; margin: 0">{{selectedPeriod}}</h1>

			<table class="ui selectable table compact celled structured" style="width: 820px; margin:auto;">
				<tr>
					<th>No.</th>
					<th>Name</th>
					<th>Employment Status</th>
					<!-- <th>Date Accomplished</th> -->
					<th width='150'>Final Numerical Rating</th>
					<th width='150'>Final Adjectival Rating</th>
				</tr>
				<tr v-if="items && items.length < 1">
					<td colspan="4" style="text-align: center;"> No Records Found </td>
				</tr>
				<tr v-else-if="(!items && !department_id) || (!items && !period_id)">
					<td colspan="4" style="text-align: center;"> Please select the Period and Department </td>
				</tr>
				<tr v-else-if="!items && department_id && period_id">
					<td colspan="4" style="text-align: center;"> Loading... Please wait... </td>
				</tr>
				<tr v-for="item,no in items" :key="item.id">
					<td>{{no+1}}</td>
					<td>{{item.full_name}}</td>
					<td>{{item.employmentStatus}}</td>
					<td>{{item.final_numerical_rating}}</td>
					<td>{{item.adjectival}}</td>
				</tr>
			</table>
	</div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
	/* Vue3 Start*/
	const {
		createApp
	} = Vue

	createApp({
		data() {
			return {
				departments: [],
				periods: [],
				isLoading: null,
				period_id: null,
				department_id: '',
				items: null,
				chart: null,
				chart_data: null,
				chartHeight: "height: 200px;"
			}
		},
		computed: {
			selectedDepartment() {
				if (this.department_id) return "ALL DEPARTMENTS"
				for (let index = 0; index < this.departments.length; index++) {
					const element = this.departments[index];
					if (element.department_id == this.department_id) {
						return element.department
						break;
					}
				}
			},
			selectedPeriod() {
				for (let index = 0; index < this.periods.length; index++) {
					const element = this.periods[index];
					if (element.period_id == this.period_id) {
						return element.period
						break;
					}
				}
			},
		},
		methods: {

			getItems() {
				this.items = null
				if (this.chart) {
					this.chart.destroy()
				}
				$.post('?config=FinalNumericalRatings', {
					view: true,
					period_id: this.period_id,
					department_id: this.department_id
				}, (data, textStatus, xhr) => {
					const res = JSON.parse(data)
					this.items = res.table_data
					this.chart_data = res.chart_data


					let h = 100;
					let count = this.chart_data.labels.length;
					if (count > 1) {
						h = h * count
					} else {
						h = 200
					}
					this.chartHeight = "height:" + h + "px;";

					const ctx = document.getElementById('myChart');
					Chart.defaults.font.size = 18;
					this.chart = new Chart(ctx, {
						type: 'bar',
						data: res.chart_data,
						options: {
							responsive: true,
							maintainAspectRatio: false,
							indexAxis: 'y',
							scales: {
								x: {
									// stacked: true,
								},
								y: {
									// stacked: true
								}
							},
							plugins: {
								title: {
									display: true,
									text: 'Percentage of personnel per measure in the department/s'
								},
								tooltip: {
									callbacks: {
										// title: (context) => {
										// 	return context.datasetIndex;
										// },
										label: (context) => {
											// let sum = 0;

											// tooltipItems.forEach(function(tooltipItem) {
											// 	sum += tooltipItem.parsed.y;
											// });
											return " " + context.formattedValue + "% - " + context.dataset.label;
										}
									}
								}
							}
						}
					});

					console.log(res.chart_data);
					// $("#iMatrixCont").html(data);
					// $('#appLoader').dimmer('hide');
				});
			},
			getDepartmentItems() {
				$.post('?config=FinalNumericalRatings', {
					getDepartmentItems: true,
				}, (data, textStatus, xhr) => {
					this.departments = JSON.parse(data)
				});
			},
			// getPeriodItems
			getPeriodItems() {
				$.post('?config=FinalNumericalRatings', {
					getPeriodItems: true,
				}, (data, textStatus, xhr) => {
					this.periods = JSON.parse(data)
				});
			},
		},
		mounted() {
			// this.getItems()
			// $('#appLoader').dimmer({
			// 	closable: false
			// }).dimmer('show');
			this.getDepartmentItems()
			this.getPeriodItems()

			// periodDropdown
			$("#periodDropdown").dropdown({
				forceSelection: false,
				fullTextSearch: true,
				onChange: (value, text, $choice) => {
					this.period_id = value;
					if (this.period_id && this.department_id) {
						this.getItems()
						console.log(this.period_id + " " + this.department_id);
					}
				}
			});

			$("#departmentDropdown").dropdown({
				forceSelection: false,
				fullTextSearch: true,
				onChange: (value, text, $choice) => {
					// console.log(value);
					this.department_id = value;
					if (this.period_id && this.department_id) {
						this.getItems()
						console.log(this.period_id + " " + this.department_id);
					}
				}
			});

			// chart start


		}

	}).mount('#finalNumericalRatingsApp')
	/* Vue3 End*/
</script>