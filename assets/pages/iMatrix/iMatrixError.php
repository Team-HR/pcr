<!-- <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script> -->

<div id="iMatrixContError" style="min-height:500px">
    <br>
    <br>
    <br>
    <h2 class='ui center aligned icon header'>
        <i class='ui red exclamation triangle icon'></i>
        <div class='content'>
            Rating Scale Matrix Not Found
            <div class='sub header'>You Dont have Rating Matrix Yet. Please Contact OHRMD PMS system for this matter <br>
                <b style="color: blue;">OR try to set the department assigned to during this period down below.</b>
            </div>
        </div>
    </h2>
    <br>

    <form @submit.prevent="setDepartmentOnPeriod" class="ui form" style="width: 700px; margin: auto;">
        <div class="field" style="text-align: center;">
            <h2>January - June, 2022</h2>
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

    </form>

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
                    // console.log(data);
                    // this.departments = JSON.parse(data);
                    if (data) {
                        window.location.href = "?RatingScale"
                    }

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
                this.departments = JSON.parse(data);
            });
        }

    }).mount('#iMatrixContError')
    /* Vue3 End*/
</script>