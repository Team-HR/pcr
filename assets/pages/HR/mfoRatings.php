<div id='mfoRatingsApp' class="ui segment" style="margin-left: 25px; margin-right: 25px;">
	<h1 class="ui header block">HRMO Dashboard | MFO Ratings</h1>
	<div class="ui fluid basic segment">

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

		<template v-if="(!htmlTable && !department_id) || (!htmlTable && !period_id)">
			<div class="ui segment fluid" style="width: 1100px; margin:auto; text-align: center;">Please select a Period and Department</div>
		</template>
		<template v-else-if="!htmlTable && department_id && period_id">
			<div class="ui segment fluid" style="width: 1100px; margin:auto; text-align: center;">Loading... Please wait...</div>
		</template>
		<template v-else>
			<div v-html="htmlTable"></div>
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
				departments: [],
				periods: [],
				isLoading: null,
				period_id: null,
				department_id: null,
				items: null,
				htmlTable: null
			}
		},
		methods: {

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

			getHtmlTable() {
				this.htmlTable = null
				$.post('?config=FinalNumericalRatings', {
					viewMfos: true,
					period_id: this.period_id,
					department_id: this.department_id
				}, (data, textStatus, xhr) => {
					this.htmlTable = data
				});
			}
		},
		mounted() {

			this.getDepartmentItems()
			this.getPeriodItems()

			// periodDropdown
			$("#periodDropdown").dropdown({
				forceSelection: false,
				fullTextSearch: true,
				onChange: (value, text, $choice) => {
					this.period_id = value;
					if (this.period_id && this.department_id) {
						this.getHtmlTable()
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
						this.getHtmlTable()
						console.log(this.period_id + " " + this.department_id);
					}
				}
			});
		}

	}).mount('#mfoRatingsApp')
	/* Vue3 End*/
</script>