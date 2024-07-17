<div class="container">
    <h1>RSM Status</h1>
    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel"></h5>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                </div>
                <div class="modal-body">
                    <div id="modalCont"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="staticBackdropCloseBtn" data-bs-dismiss="modal" disabled>Close</button>
                </div>
            </div>
        </div>
    </div>
    <table class="table" name='rsmTable' data-target="">
        <thead>
            <tr>
                <th scope="col" colspan="4">
                    Period
                    <form action="" id="rsmStatusPeriodForm">
                        <div class="row">
                            <div class="col">
                                <select class="form-control" name="period">
                                    <option value="January - June">January - June</option>
                                    <option value="July - December">July - December</option>
                                </select>
                            </div>
                            <div class="col">
                                <select class="form-control" name="year">
                                    <?= get_yearOp() ?>
                                </select>
                            </div>
                            <div class="col">
                                <button type="submit" name="button" class="btn btn-primary">Generate</button>
                            </div>
                        </div>
                    </form>
                </th>
            </tr>
            <tr>
                <th scope="col" style="vertical-align: middle;" rowspan="2">Department</th>
                <th scope="col" style="vertical-align: middle;" rowspan="2">Done</th>
                <th scope="col" style="text-align: center">Edit</th>
                <th scope="col" style="vertical-align: middle;" rowspan="2">Delete RSM</th>
            </tr>
            <tr>
                <th style="text-align: center">
                    <div class="d-grid gap-2 d-md-block">
                        <button class="btn btn-primary btn-sm editBtn" type="button" name="enableEdit" data-bs-toggle="modal" data-bs-target="#staticBackdrop" disabled>Enable</button>
                        <button class="btn btn-danger btn-sm editBtn" type="button" name="disableEdit" data-bs-toggle="modal" data-bs-target="#staticBackdrop" disabled>Disabled</button>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody name="tbody">
            <tr>
                <th scope="row" colspan="3">
                    <h1 style="text-align: center;color:#0000002b;">
                        No Period is Selected
                    </h1>
                </th>
            </tr>
        </tbody>
    </table>
</div>


<div id="warningModal" class="modal fade" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" style="margin-top: 100px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Warning</h5>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the RSM of this department in this period?</p>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-target="#warningModal" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="warningModalConfirm">Confirm</button>
            </div>
        </div>
    </div>
</div>
<div id="confirmModal" class="modal fade" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" style="margin-top: 100px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm</h5>
            </div>
            <div class="modal-body">
                <p>The RSM will be deleted permanently and cannot be undone. Please confirm.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-bs-target="#confirmModal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmModalConfirm" data-bs-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>
<div id="deletingModal" class="modal fade" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" style="margin-top: 100px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Deleting...</h5>
            </div>
            <div class="modal-body">
                <p>Deleting RSM. Please wait...</p>
            </div>
        </div>
    </div>
</div>



<!-- js / javascript -->
<script defer>
    (function() {
        'use strict';



        document.addEventListener('click', function(event) {
            var trgt = event.target;
            if (trgt.name == "nodatSaveRsm") {
                nodatSaveRsm(trgt);
            } else if (trgt.name == "editdatRsm") {
                editdatRsm(trgt);
            } else if (trgt.name == "enableEdit") {
                enableEdit(trgt, 1);
            } else if (trgt.name == "disableEdit") {
                enableEdit(trgt, 0)
            } else if (trgt.name == "deleteRsm") {
                deleteRsm(trgt)
            }
        });



        function deleteRsm(trgt) {


            // var warningModal = new bootstrap.Modal($("#warningModal"))
            // var confirmModal = new bootstrap.Modal($("#confirmModal"))
            // var deletingModal = new bootstrap.Modal(document.getElementById("deletingModal"))



            var dataTarget = trgt.getAttribute('data-target').split("||");
            const period_id = dataTarget[0];
            const department_id = dataTarget[1];


            // warningModal.show()
            // const warningConfirm = document.getElementById("warningModalConfirm");
            // warningConfirm.addEventListener('click', (event) => {
            //     warningModal.hide()
            //     warningModal.destroy()
            //     confirmModal.show()
            // }, {
            //     once: true
            // })


            // const confirmModalConfirm = document.getElementById("confirmModalConfirm");
            // confirmModalConfirm.addEventListener('click', (event) => {
            //     confirmModal.hide()
            //     confirmModal.destroy()
            //     deletingModal.show()
            // }, {
            //     once: true
            // })



            if (confirm("Are you sure you want to delete RSM for the selected department and period?")) {
                console.log("proceed confirmation!");
                if (confirm("Warning: The RSM will be deleted permanently and cannot be undone. Please confirm.")) {
                    // deletingModal.show()
                    // setTimeout(() => {
                    //     deletingModal.hide()
                    // }, 1000);
                    $.post("?config=rsmStatus", {
                            deleteRsm: true,
                            period_id: period_id,
                            department_id: department_id,
                        }, (data, textStatus, jqXHR) => {
                            console.log(data);
                            if (data.length > 0) {
                                alert("RSM deleted successfully!");
                                // deletingModal.hide()
                            } else {
                                alert("No RSM found hence deleted none!");
                                // deletingModal.hide()
                            }
                        },
                        "json"
                    );
                } else {
                    console.log("canceled!");
                }
            } else {
                console.log("canceled!");
            }





        }

        function _(el) {
            return document.getElementById(el);
        }

        function c(el) {
            return document.getElementsByClassName(el);
        }

        function enableEdit(el, edStat) {
            _("staticBackdropLabel").innerHTML = "RSM Edit";
            var addel = document.createElement("div");
            addel.innerHTML = "Calculating.....";
            _("modalCont").innerHTML = "";
            _("modalCont").appendChild(addel);
            var dataTarget = el.getAttribute('data-target').split("||");
            var fd = new FormData();
            fd.append('period', dataTarget[0]);
            fd.append('year', dataTarget[1]);
            fd.append('enableAllRsm', true);
            var xml = new XMLHttpRequest();
            xml.open("POST", "?config=rsmStatus", false);
            xml.onprogress = function() {
                addel.innerHTML = "Calculating.....";
                _("modalCont").appendChild(addel);
            }
            xml.onload = function() {
                var s = document.createElement("span");
                var addDone = document.createTextNode("Done!");
                s.style.color = "green";
                s.appendChild(addDone);
                addel.appendChild(s);
                var ar = JSON.parse(xml.responseText);
                var progress = document.createElement("div");
                progress.setAttribute('id', 'progressRsm');
                _('modalCont').appendChild(progress);
                var logs = document.createElement("div");
                logs.setAttribute('id', 'logsRsm');
                _('modalCont').appendChild(logs);
                setTimeout(() => {
                    editAllWith(ar, edStat, 0);
                }, 500);
            }
            console.log(xml);
            xml.send(fd);
        }

        function doneEl() {
            var s = document.createElement("span");
            var addDone = document.createTextNode(" - Done!");
            s.style.color = "green";
            s.appendChild(addDone);
            return s;
        }

        function editAllWith(ar, editStatus, index) {
            var countlength = ar['with'].length + ar['without'].length;
            // add the progress made;
            _('progressRsm').innerHTML = index + 1 + " of " + countlength;
            var apLogs = document.createElement("div");
            if (ar['with'].length > 0 && index != ar['with'].length) {
                apLogs.innerHTML = ar['with'][index][1];
                _('logsRsm').appendChild(apLogs);
                var fd = new FormData();
                fd.append('dataid', ar['with'][index][0]);
                fd.append('edit', editStatus);
                fd.append('editAllWith', true);
                var xml = new XMLHttpRequest();
                xml.open("POST", "?config=rsmStatus", false);
                xml.onload = function() {
                    try {
                        if (xml.responseText == '1') {
                            apLogs.appendChild(doneEl());
                            var i = index + 1;
                            setTimeout(() => {
                                editAllWith(ar, editStatus, i);
                            }, 500);
                        }
                    } catch (e) {
                        alert(e);
                    }
                }
                xml.send(fd);
            } else {
                editAllWithout(ar, editStatus, 0);
            }
        }

        function editAllWithout(ar, editStatus, index) {
            var countlength = ar['with'].length + ar['without'].length;
            // add the progress made;
            _('progressRsm').innerHTML = index + ar['with'].length + " of " + countlength;
            var apLogs = document.createElement("div");
            if (ar['without'].length > 0 && index != ar['without'].length) {
                apLogs.innerHTML = ar['without'][index][2];
                _('logsRsm').appendChild(apLogs);

                // [period,department_id,department]

                var fd = new FormData();
                fd.append('period', ar['without'][index][0]);
                fd.append('department', ar['without'][index][1]);
                fd.append('edit', editStatus);
                fd.append('editAllWithout', true);
                var xml = new XMLHttpRequest();
                xml.open("POST", "?config=rsmStatus", false);
                xml.onload = function() {
                    try {
                        if (xml.responseText == '1') {
                            apLogs.appendChild(doneEl());
                            var i = index + 1;
                            setTimeout(() => {
                                editAllWithout(ar, editStatus, i);
                            }, 500);
                        }
                    } catch (e) {
                        alert(e);
                    }
                }
                xml.send(fd);
            } else {
                _('progressRsm').appendChild(doneEl());
                _("staticBackdropCloseBtn").disabled = false;
                tableRefresh();
            }
        }

        function tableRefresh() {
            var formEls = document.forms.rsmStatusPeriodForm.elements;
            var xml = new XMLHttpRequest();
            var fd = new FormData();
            // button 1st action
            formEls.button.disabled = true;
            formEls.button.innerHTML = "Generating";
            // push data to variable fd 
            fd.append('rsmGetTableData', true);
            fd.append('period', formEls.period.value);
            fd.append('year', formEls.year.value);
            xml.onload = function() {
                document.getElementsByTagName("table").rsmTable.children.tbody.innerHTML = xml.responseText;
                formEls.button.disabled = false;
                formEls.button.innerHTML = "Generate";
                var editBtn = document.getElementsByClassName('editBtn');
                var countBtn = 0;
                while (countBtn < editBtn.length) {
                    editBtn[countBtn].disabled = false;
                    editBtn[countBtn].setAttribute('data-target', formEls.period.value + "||" + formEls.year.value);
                    countBtn++;
                }
            }
            xml.open('POST', '?config=rsmStatus', false);
            xml.send(fd);
        }

        function nodatSaveRsm(el) {
            el.disabled = true;
            let a = el.getAttribute('data-element').split("||");
            var fd = new FormData();
            fd.append('period', a[0]);
            fd.append('department', a[1]);
            fd.append('rsmAdd', true);
            var xml = new XMLHttpRequest();
            xml.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var new_row = document.createElement("tr");
                    new_row.innerHTML = xml.responseText;
                    var old_row = el.parentNode.parentNode;
                    old_row.parentNode.replaceChild(new_row, old_row);
                }
            }
            xml.open("POST", "?config=rsmStatus", false);
            xml.send(fd);
        }

        function editdatRsm(el) {
            el.disabled = true;
            let a = el.getAttribute('data-element');
            var edit = 0;
            if (el.checked) {
                edit = 1;
            }
            var fd = new FormData();
            fd.append('rsmAdd', true);
            fd.append('dat', a);
            fd.append('edit', edit);
            var xml = new XMLHttpRequest();
            xml.onload = function() {
                // console.log(xml.responseText);
                var new_row = document.createElement("tr");
                new_row.innerHTML = xml.responseText;
                var old_row = el.parentNode.parentNode;
                old_row.parentNode.replaceChild(new_row, old_row);

            }
            xml.open("POST", "?config=rsmStatus", false);
            xml.send(fd);
        }

        // form function 
        var periodForm = document.getElementsByTagName("form").rsmStatusPeriodForm;
        periodForm.addEventListener('submit', function() {
            event.preventDefault();
            var formEls = this.elements;
            var xml = new XMLHttpRequest();
            var fd = new FormData();
            // button 1st action
            formEls.button.disabled = true;
            formEls.button.innerHTML = "Generating";
            // push data to variable fd 
            fd.append('rsmGetTableData', true);
            fd.append('period', formEls.period.value);
            fd.append('year', formEls.year.value);
            xml.onload = function() {
                document.getElementsByTagName("table").rsmTable.children.tbody.innerHTML = xml.responseText;
                formEls.button.disabled = false;
                formEls.button.innerHTML = "Generate";
                var editBtn = document.getElementsByClassName('editBtn');
                var countBtn = 0;
                while (countBtn < editBtn.length) {
                    editBtn[countBtn].disabled = false;
                    editBtn[countBtn].setAttribute('data-target', formEls.period.value + "||" + formEls.year.value);
                    countBtn++;
                }
            }
            xml.open('POST', '?config=rsmStatus', false);
            xml.send(fd);
        });
    }());
</script>



<!-- php -->
<?php
function get_yearOp()
{
    $past = date("Y") - 10;
    $present = date("Y") + 1;
    $op = "";
    while ($present >= $past) {
        $op .= "<option>$present</option>";
        $present--;
    }
    return $op;
}
?>


<style>
    .rsm.delete.button {
        border: solid grey 1px;
        background-color: white;
        border-radius: 3px;
        font-size: 12px;
    }
</style>