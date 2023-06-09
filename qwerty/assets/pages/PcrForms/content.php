<div id="pcrFormsApplet" class="ui container" style="margin-top: 15px;">


    <div class="p-3 d-flex justify-content-center">
        <h3>LOCK/UNLOCK PCR FORMS</h3>
    </div>
    <form @submit.prevent="getForms()">
        <div class="row">
            <div class="col-6">
                <label class="form-label">Select Period:</label> <br>
                <select v-model="selPeriod" class="form-control">
                    <option value="" disabled>Select Period</option>
                    <option value="January - June">January - June</option>
                    <option value="July - December">July - December</option>
                </select>
            </div>
            <div class="col-6">
                <label class="form-label">Select Year:</label> <br>
                <select v-model="selYear" style="margin-left: 5px; margin-right: 5px;" class="form-control">
                    <option value="" disabled>Select Year</option>
                    <option value="2023">2023</option>
                    <option value="2022">2022</option>
                    <option value="2021">2021</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <label class="form-label">Select Department:</label> <br>
                <select class="form-control" v-model="selDepartment" style="margin-left: 5px; margin-right: 5px;">
                    <option value="" disabled>Select Department</option>
                    <option value="">All</option>
                    <option v-for="department, d in departments" :key="d" :value="department">{{department.department}}</option>
                </select>
            </div>
        </div>

        <div class="p-3 d-flex justify-content-center"> <button style="margin-top: 5px;" type="submit" :disabled="!selPeriod || !selYear" class="btn btn-primary">Get Personnel</button></div>


    </form>


    <table class="table table-hover">
        <thead>
            <tr>
                <td>ID</td>
                <td>USERNAME</td>
                <td>LOCK/UNLOCK</td>
                <td>NAME</td>
                <td>DEPARTMENT</td>
                <td>SUBMITTED</td>
                <td>PANELAPPROVED DATE</td>
                <td>DATE ACCOMPLISHED</td>
            </tr>
        </thead>
        <tbody>
            <template v-for="item,i in items" :key="i">
                <tr>
                    <td>{{item.employees_id}}</td>
                    <td>{{item.username}}</td>
                    <td>
                        <button style="background: #ffb8b8; width: 80px; box-shadow: none; border: 0px; padding: 2px;" v-if="item.submitted || item.panelApproved" @click="unlockForm(item)" class="btn">UNLOCK</button>
                        <button style="background: yellow; width: 80px; box-shadow: none; border: 0px; padding: 2px;" v-else @click="lockForm(item)" class="btn">LOCK</button>
                    </td>
                    <td>{{item.name}}</td>
                    <td>{{item.department}}</td>
                    <td>{{item.submitted}}</td>
                    <td>{{item.panelApproved}}</td>
                    <td>{{item.dateAccomplished}}</td>
                </tr>
            </template>
        </tbody>
    </table>

</div>

<script>
    /* Vue3 Start*/
    const {
        createApp
    } = Vue

    createApp({
        data() {
            return {
                selPeriod: "",
                selYear: "",
                periods: [],
                departments: [],
                selDepartment: "",
                items: []
            }
        },
        watch: {

        },
        computed: {

        },
        methods: {
            getPeriods() {
                $.post("?config=pcrForms", {
                    getPeriods: true
                }).then(res => {
                    this.periods = JSON.parse(res)
                });
            },

            getDepartments() {
                $.post("?config=pcrForms", {
                    getDepartments: true
                }).then(res => {
                    this.departments = JSON.parse(res)
                });
            },

            getForms() {
                $.post("?config=pcrForms", {
                    getForms: true,
                    selPeriod: this.selPeriod,
                    selYear: this.selYear,
                    selDepartment: this.selDepartment
                }).then(res => {
                    // console.log(res);
                    this.items = JSON.parse(res)
                });
                // console.log(this.selPeriod + " " +
                // this.selYear + " " + this.selDepartment);
            },


            lockForm(item) {
                const performanceReviewStatus_id = item.performanceReviewStatus_id
                // console.log('lockForm:', performanceReviewStatus_id);
                $.post("?config=pcrForms", {
                    lockForm: true,
                    performanceReviewStatus_id: performanceReviewStatus_id,
                }).then(res => {
                    // console.log('lockForm:', res);
                    this.getForms()
                });
            },


            unlockForm(item) {
                const performanceReviewStatus_id = item.performanceReviewStatus_id
                // console.log('unlockForm:', performanceReviewStatus_id);
                $.post("?config=pcrForms", {
                    unlockForm: true,
                    performanceReviewStatus_id: performanceReviewStatus_id,
                }).then(res => {
                    // console.log('unlockForm:', res);
                    this.getForms()
                });
            }


        },

        mounted() {
            this.getDepartments()
        }

    }).mount('#pcrFormsApplet')
    /* Vue3 End*/
</script>