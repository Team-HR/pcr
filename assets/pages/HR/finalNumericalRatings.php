<div id='finalNumericalRatingsApp' class="ui segment" style="margin-left: 25px; margin-right: 25px;">
	<h1 class="ui header block">HRMO Dashboard | Final Numerical Ratings</h1>
	<div class="ui fluid basic segment">
		<!-- <li v-for="item in items" :key="item.id">{{item}}</li> -->
		<div class="ui form" style="width: 820px; margin:auto; margin-bottom: 20px;">
			<div class="fields">
				<div class="field" style="width: 220px;">
					<label>Period:</label>
					<select name="periodMonthDropdown" id="periodMonthDropdown" v-model="selected_period_month" :disabled='isLoading'>
						<option value="">Select Period</option>
						<option v-for="month, i in period_months" :key="i" :value="month">{{month}}</option>
					</select>
				</div>
				<div class="field" style="width: 220px;">
					<label>Year:</label>
					<select name="periodYearDropdown" id="periodYearDropdown" v-model="selected_period_year" :disabled='isLoading'>
						<option value="">Select Year</option>
						<option v-for="year, i in period_years" :key="i" :value="year">{{year}}</option>
					</select>
				</div>
				<div class="field" style="width: 600px;">
					<label> Select Department:</label>

					<select name="departmentDropdown" id="departmentDropdown" v-model="department_id" :disabled='isLoading'>
						<option value="">Select Department</option>
						<option value="all">All Departments</option>
						<option v-for="item, i in departments" :key="i" :value="item.department_id">{{item.department}}</option>
					</select>

					<!-- 
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
					</div> -->


				</div>
			</div>
		</div>






		<div class="ui basic fluid segment center aligned">
			<button class="ui primary button" @click="fetchFinalRating()" :disabled="!selected_period_month || !selected_period_year || !department_id || isLoading">Fetch Final Ratings</button>
		</div>


		<div style="padding: 20px; position: relative; width:1000px; margin: auto; background-color:azure" :style="chartHeight">
			<!-- <div :style="isLoading ? 'display: none;': ''"> -->
			<canvas id="myChart"></canvas>
			<!-- </div> -->
			<!-- <div :style="isLoading ? '': 'display: none;'" class="ui basic segment fluid center aligned">
				<h1 v-else class="">Loading... Please wait...</h1>
			</div> -->
		</div>
		<br>
		<br>
		<h1 style="text-align: center; margin: 0">{{selectedDepartment}}</h1>
		<h3 style="text-align: center; margin: 0">{{selectedPeriod}}</h1>
			<table class="ui selectable table compact collapsing celled structured" style="margin:auto;">
				<tr>
					<th>No.</th>
					<th>ID</th>
					<th>Name</th>
					<th>Employment Status</th>
					<th>Department</th>
					<!-- <th>Date Accomplished</th> -->
					<th width='150'>Final Numerical Rating</th>
					<th width='150'>Final Adjectival Rating</th>
				</tr>

				<tr v-if="items && items.length < 1">
					<td colspan="7" style="text-align: center;"> No Records Found </td>
				</tr>
				<tr v-else-if="(!items && !department_id) || (!items && !period_id)">
					<td colspan="7" style="text-align: center;"> Please select the Period and Department </td>
				</tr>
				<tr v-else-if="!items && department_id && period_id">
					<td colspan="7" style="text-align: center;"> Loading... Please wait... </td>
				</tr>
				<tr v-if="!isLoading" v-for="item,no in items" :key="item.id" :style="!item.final_numerical_rating? 'background: grey' : ''">
					<td>{{no+1}}</td>
					<td>{{item.employees_id}}</td>
					<td>{{item.full_name}}</td>
					<td>{{item.employmentStatus}}</td>
					<td>{{item.department_alias}}</td>
					<td>{{item.final_numerical_rating}}</td>
					<td>{{item.adjectival}}</td>
				</tr>
				<tr v-else>
					<td colspan="5" style="text-align: center;"> Loading... Please wait... </td>
				</tr>
			</table>
	</div>
</div>


<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
<script>
	/* Vue3 Start*/
	const {
		createApp,
		ref
	} = Vue

	createApp({
		data() {
			return {

				departments: [],
				period_months: [
					"January - June",
					"July - December"
				],
				period_years: [],
				selected_period_year: null,
				selected_period_month: null,
				periods: [],
				isLoading: null,
				period_id: null,
				department_id: null,
				items: null,
				chart: ref(null),
				chart_data: null,
				chartHeight: "height: 200px;"
			}
		},
		computed: {
			selectedDepartment() {
				if (this.department_id == 'all') return "All DEPARTMENTS"
				for (let index = 0; index < this.departments.length; index++) {
					const element = this.departments[index];
					if (element.department_id == this.department_id) {
						return element.department
						break;
					}
				}
			},
			selectedPeriod() {
				if (this.selected_period_month && this.selected_period_year) {
					return this.selected_period_month + ", " + this.selected_period_year
				}
			},
		},
		watch: {
			isLoading(val) {
				if (val) {
					$('#appLoader').dimmer({
						closable: false
					}).dimmer('show');
					// console.log("is loading...");
				} else {
					$('#appLoader').dimmer('hide');
					// console.log("is loaded...");
				}
			}
		},
		methods: {

			fetchData() {
				return new Promise((resolve) => {
					$.post('?config=FinalNumericalRatings', {
						view: true,
						selected_period_month: this.selected_period_month,
						selected_period_year: this.selected_period_year,
						department_id: this.department_id
					}, (data, textStatus, xhr) => {
						resolve(JSON.parse(data))
					});
				});
			},


			async getItems() {
				this.isLoading = true;
				const res = JSON.parse(JSON.stringify(await this.fetchData()))
				console.log(res);
				this.items = res.table_data

				if (!res.chart_data) return null;

				let h = 100;
				let count = res.chart_data.labels.length;
				if (count > 1) {
					h = h * count
				} else {
					h = 200
				}
				this.chartHeight = "height:" + h + "px;";


				if (this.chart) {
					this.chart.destroy()
				}

				// else {

				const ctx = document.getElementById('myChart');
				// Chart.defaults.font.size = 18;
				this.chart = new Chart(ctx, {
					type: 'bar',
					data: res.chart_data,
					options: {
						animation: false,
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
							afterDraw: (chart, easing) => {
								// This callback will be called after the chart is drawn
								console.log('Chart has been rendered');
								// Your additional actions after rendering go here
							},
							title: {
								display: true,
								text: 'Performance Measure vs Percentage of Personnel in a Department'
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

				// }

				this.isLoading = false;

			},

			getDepartmentItems() {
				// assets/pages/HR/finalNumericalRatingsConfig.php
				$.post('?config=FinalNumericalRatings', {
					getDepartmentItems: true,
				}, (data, textStatus, xhr) => {
					this.departments = JSON.parse(data)
				});
			},

			fetchPeriodYears() {
				// assets/pages/HR/finalNumericalRatingsConfig.php
				return new Promise(resolve => {
					$.post('?config=FinalNumericalRatings', {
						getPeriodYears: true,
					}, (data, textStatus, xhr) => {
						resolve(JSON.parse(data))
						// console.log(this.period_years);
					});
				})
			},

			fetchFinalRating() {
				this.isLoading = true
				// simulate fetch
				// setTimeout(() => {
				// 	this.isLoading = false
				// }, 2000);
				$.post('?config=FinalNumericalRatings', {
					fetchFinalRating: true,
					selected_period_month: this.selected_period_month,
					selected_period_year: this.selected_period_year,
					department_id: this.department_id
				}, (data, textStatus, xhr) => {
					this.getItems()
					this.isLoading = false
				});
			}

		},
		mounted() {
			this.fetchPeriodYears().then(data => {
				this.period_years = data;
			})

			this.getDepartmentItems()

			$("#periodMonthDropdown").dropdown({
				forceSelection: false,
				fullTextSearch: true,
				onChange: (value, text, $choice) => {
					this.selected_period_month = value;
					if (this.selected_period_month && this.selected_period_year && this.department_id) {
						this.getItems()
						// console.log(this.selected_period_month + " " + this.department_id);
					}
				}
			});

			$("#periodYearDropdown").dropdown({
				forceSelection: false,
				fullTextSearch: true,
				onChange: (value, text, $choice) => {
					this.selected_period_year = value;
					if (this.selected_period_month && this.selected_period_year && this.department_id) {
						this.getItems()
						// console.log(this.selected_period_year + " " + this.department_id);
					}
				}
			})

			$("#departmentDropdown").dropdown({
				forceSelection: false,
				fullTextSearch: true,
				onChange: (value, text, $choice) => {
					// console.log(value);
					this.department_id = value;
					if (this.selected_period_month && this.selected_period_year && this.department_id) {
						this.getItems()
					}
				}
			});

			// chart start

			$('#appLoaderMsg').html("Consolidating data... Please wait.... This may take 5-10 minutes, please DO NOT CLOSE this page. ");

		}

	}).mount('#finalNumericalRatingsApp')
	/* Vue3 End*/
</script>