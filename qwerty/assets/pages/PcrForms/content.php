<style>
    tr,
    td {
        text-align: center;
    }
</style>

<div id="pcrFormsApplet" class="ui container" style="margin-top: 15px;">


    <div class="p-3 d-flex justify-content-center">
        <h3>PCR FORMS MANAGEMENT</h3>
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
                    <option value="2023" seelc>2023</option>
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
                <td>TYPE</td>
                <td>STATUS</td>
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
                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#convertFormDialog" @click="convertForm(item)">
                            {{getFormType(item.formType)}}
                        </button>
                    </td>
                    <td>
                        <div v-if="item.submitted">LOCKED</div>
                        <div v-else>UNLOCKED</div>
                    </td>
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


    <div class="modal" tabindex="-1" id="convertFormDialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Convert Form Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- <p>Modal body text goes here.</p> -->
                    <p>Name: {{fileToConvert.name}}</p>
                    <p>Form Type: {{getFormType(fileToConvert.formType)}}</p>
                    <label>Convert to:</label>
                    <select class="form-select" aria-label="formtype-sel" v-model="selFormType">
                        <option selected value="" disabled>Convert file to...</option>
                        <option value="1">IPCR</option>
                        <option value="2">SPCR</option>
                        <option value="3">DPCR</option>
                        <option value="4">Division SPCR</option>
                        <option value="5">NGA (IPCR)</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" @click="confirmConvert()">Convert</button>
                </div>
            </div>
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
                selPeriod: "",
                selYear: "",
                periods: [],
                departments: [],
                selDepartment: "",
                items: [],
                fileToConvert: {},
                selFormType: ""
            }
        },
        methods: {
            convertForm(item) {
                // console.log("Convert: ", item);
                this.fileToConvert = item;
                const convertFormDialog = document.getElementById("convertFormDialog");
                // // convertFormDialog('show');
            },
            confirmConvert() {
                console.log("Confirm Convert: ", [
                    this.fileToConvert,
                    this.selFormType
                ]);

                $.post("?config=pcrForms", {
                    convertForm: true,
                    fileToConvert: this.fileToConvert,
                    selFormType: this.selFormType,
                }).then(res => {
                    // console.log('convertForm: ', res);
                    this.getForms()
                });

            },
            getFormType(formType) {
                if (formType == '1') {
                    return "IPCR"
                } else if (formType == '2') {
                    return "SPCR"
                } else if (formType == '3') {
                    return "DPCR"
                } else if (formType == '4') {
                    return "DIVISION SPCR"
                } else if (formType == '5') {
                    return "NGA"
                }
            },

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
                // console.log(this.selDepartment);
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
            // this.getForms()
        }

    }).mount('#pcrFormsApplet')
    /* Vue3 End*/
</script>