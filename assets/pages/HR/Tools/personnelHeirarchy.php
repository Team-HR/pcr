<div id='finalNumericalRatingsApp' class="ui segment" style="margin-left: 25px; margin-right: 25px;">
    <h1 class="ui header block">Peer Rating Tools | Personnel Heirarchy</h1>
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
                        <!-- <option value="all">All Departments</option> -->
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
        <br>
        <br>
        <h1 style="text-align: center; margin: 0">{{selectedDepartment}}</h1>
        <h3 style="text-align: center; margin: 0">{{selectedPeriod}}</h1>

            <table class="ui compact mini table">
                <thead>
                    <tr>
                        <td></td>
                        <td colspan="4"></td>
                    </tr>
                </thead>
                <tbody v-html="items"></tbody>
            </table>
    </div>
</div>

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
                    $.post('?config=personnelHeirarchy', {
                        getPersonnelHeirarchy: true,
                        selected_period_month: this.selected_period_month,
                        selected_period_year: this.selected_period_year,
                        department_id: this.department_id
                    }, (data, textStatus, xhr) => {
                        resolve(JSON.parse(data))
                    });
                });
            },


            async getItems() {
                const res = await this.fetchData()
                this.items = res;
                // console.log(this.items);
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