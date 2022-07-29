<div id="perfratingBody"></div>
<script type="text/javascript">
    $(document).ready(function() {
        showPr("coreFunction", "");
    });


    function reviewFormType() {
        e = event.target;
        formEl = e.form.elements;
        immediateSup = formEl.immediateSup;
        departmentHead = formEl.departmentHead;
        if (e.value == 1) {
            immediateSup.parentElement.classList.remove('disabled');
            departmentHead.parentElement.classList.remove('disabled');
        } else if (e.value == 2 || e.value == 4) {
            immediateSup.parentElement.classList.remove('disabled');
            departmentHead.parentElement.classList.remove('disabled');
        } else if (e.value == 3) {
            immediateSup.parentElement.classList.add('disabled');
            departmentHead.parentElement.classList.add('disabled');
        }
    }
</script>





















<!--  -->