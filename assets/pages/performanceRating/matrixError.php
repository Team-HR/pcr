<!-- <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script> -->

<div id="pMatrixContError" style="min-height:500px">
    <br>
    <br>
    <br>
    <h2 class='ui center aligned icon header'>
        <i class='ui red exclamation triangle icon'></i>
        <div class='content'>
            Rating Scale Matrix Not Found
            <div class='sub header' style="color:black;">You are not tagged to any MFOs in your office's rating scale matrix OR rating scale matrix doesn't exist yet for this period <br><b>{{period}}</b>. <br> Please contact your immediate supervisor or department head. <br>
                <!-- <b style="color: blue;">OR try to set the department assigned to during this period down below.</b> -->
            </div>
        </div>
    </h2>
    <br>
    <!-- 
    <form @submit.prevent="setDepartmentOnPeriod" class="ui form" style="width: 700px; margin: auto;">
        <div class="field" style="text-align: center;">
            <h2>{{period}}</h2>
        </div>
        <div class="two column fields">
            <div class="twelve wide field">
                <label>Select Department for this Period:</label>
                <div id="officeSelection" class="ui fluid search selection dropdown" style="margin-right: 5px;">
                    <input type="hidden" name="department" v-model="department_id">
                    <i class="dropdown icon"></i>
                    <div class="default text">Select Department</div>
                    <div class="menu">
                        <div v-for="department in departments" :key="department.id" class="item" :data-value="department.id">{{department.name}}</div>
                    </div>
                </div>
            </div>
            <div class="four wide field">
                <label for="" style="opacity: 0;">Set</label>
                <button type="submit" class="ui fluid button primary">Set</button>
            </div>
        </div>

    </form> -->

</div>

<script>
    /* Vue3 Start*/
    const {
        createApp
    } = Vue

    createApp({
        data() {
            return {
                test: "test",
                departments: [],
                period: "",
                department_id: null
            }
        },
        methods: {
            setDepartmentOnPeriod() {
                this.department_id = $("#officeSelection").dropdown("get value")
                console.log(this.department_id);
                // set the department
                $.post('?config=iMatrixConfig', {
                    setDepartmentOnPeriod: true,
                    department_id: this.department_id
                }, (data, textStatus, xhr) => {
                    console.log(data);
                    // this.departments = JSON.parse(data);
                    // if (data) {
                    //     window.location.href = "?performanceRating&form"
                    // }

                });
            },
        },
        mounted() {
            $("#officeSelection").dropdown({
                fullTextSearch: true,
                forceSelection: false
            });

            // get list of departments
            $.post('?config=iMatrixConfig', {
                getListOfDepartments: true
            }, (data, textStatus, xhr) => {
                // console.log(data);
                const res = JSON.parse(data);
                this.departments = res.departments;
                this.period = res.period
            });
        }

    }).mount('#pMatrixContError')
    /* Vue3 End*/
</script>