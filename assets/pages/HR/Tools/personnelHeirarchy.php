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
                    <label> Select Department Head:</label>

                    <select name="departmentHeadDropdown" id="departmentHeadDropdown" v-model="departmentHead_id" :disabled='isLoading' class="ui dropdown search">
                        <option value="">Select Department Head</option>
                        <!-- <option value="all">All Departments</option> -->
                        <option v-for="item, i in departmentHeads" :key="i" :value="item.employee_id">{{item.name}}</option>
                    </select>

                </div>
            </div>
        </div>
        <br>
        <br>
        <h1 style="text-align: center; margin: 0">{{selectedDepartmentHead}}</h1>
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

                departmentHeads: [],
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
                departmentHead_id: null,
                items: null,
            }
        },
        computed: {
            selectedDepartmentHead() {
                // if (this.departmentHead_id == 'all') return "All DEPARTMENTS"
                for (let index = 0; index < this.departmentHeads.length; index++) {
                    const element = this.departmentHeads[index];
                    if (element.employee_id == this.departmentHead_id) {
                        return element.name
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
                        departmentHead_id: this.departmentHead_id
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

            getDepartmentHeadItems() {
                // assets/pages/HR/finalNumericalRatingsConfig.php
                $.post('?config=FinalNumericalRatings', {
                    getDepartmentHeadItems: true,
                }, (data, textStatus, xhr) => {
                    this.departmentHeads = JSON.parse(data)
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

            this.getDepartmentHeadItems()

            $("#periodMonthDropdown").dropdown({
                forceSelection: false,
                fullTextSearch: true,
                onChange: (value, text, $choice) => {
                    this.selected_period_month = value;
                    if (this.selected_period_month && this.selected_period_year && this.departmentHead_id) {
                        this.getItems()
                        // console.log(this.selected_period_month + " " + this.departmentHead_id);
                    }
                }
            });

            $("#periodYearDropdown").dropdown({
                forceSelection: false,
                fullTextSearch: true,
                onChange: (value, text, $choice) => {
                    this.selected_period_year = value;
                    if (this.selected_period_month && this.selected_period_year && this.departmentHead_id) {
                        this.getItems()
                        // console.log(this.selected_period_year + " " + this.departmentHead_id);
                    }
                }
            })

            $("#departmentHeadDropdown").dropdown({
                forceSelection: false,
                fullTextSearch: true,
                onChange: (value, text, $choice) => {
                    // console.log(value);
                    this.departmentHead_id = value;
                    if (this.selected_period_month && this.selected_period_year && this.departmentHead_id) {
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