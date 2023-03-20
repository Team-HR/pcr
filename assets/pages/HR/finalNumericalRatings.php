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
							<template v-for="dept in departments" :key="dept.department_id">
								<div class="item" :data-value="dept.department_id">{{dept.department}}</div>
							</template>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- <h1>{{}}</h1> -->
		<table class="ui selectable table compact celled structured" style="width: 820px; margin:auto;">
			<tr>
				<th>Name</th>
				<th>Employment Status</th>
				<!-- <th>Date Accomplished</th> -->
				<th width='150'>Final Numerical Rating</th>
				<th width='150'>Final Adjectival Rating</th>
			</tr>
			<tr v-if="items && items.length < 1">
				<td colspan="4" style="text-align: center;"> No Records Found </td>
			</tr>
			<tr v-else-if="!items && !department_id">
				<td colspan="4" style="text-align: center;"> Please select the Period and Department </td>
			</tr>
			<tr v-else-if="!items && department_id">
				<td colspan="4" style="text-align: center;"> Loading... </td>
			</tr>
			<tr v-for="item in items" :key="item.id">
				<td>{{item.full_name}}</td>
				<td>{{item.employmentStatus}}</td>
				<!-- <td>{{item.dateAccomplished}}</td> -->
				<td>{{item.final_numerical_rating}}</td>
				<td>{{item.adjectival}}</td>
			</tr>
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
				departments: [],
				periods: [],
				isLoading: null,
				period_id: null,
				department_id: null,
				items: null
			}
		},
		methods: {
			getItems() {
				this.items = null
				$.post('?config=FinalNumericalRatings', {
					view: true,
					period_id: this.period_id,
					department_id: this.department_id
				}, (data, textStatus, xhr) => {
					// console.log(data);
					this.items = JSON.parse(data)
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
		}

	}).mount('#finalNumericalRatingsApp')
	/* Vue3 End*/
</script>