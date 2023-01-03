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
                <th scope="col" colspan="3">
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
                                <?=get_yearOp()?>
                            </select>
                        </div>
                        <div class="col">
                            <button type="submit" name="button" class="btn btn-primary">Generate</button>
                        </div>
                    </div>                    
                    </form>
                </th>
            </tr>
            <tr >
                <th scope="col" style="vertical-align: middle;" rowspan="2">Department</th>
                <th scope="col" style="vertical-align: middle;" rowspan="2">Done</th>
                <th scope="col" style="text-align: center">Edit</th>
            </tr>
            <tr>
                <th style="text-align: center">
                    <div class="d-grid gap-2 d-md-block">
                        <button class="btn btn-primary btn-sm editBtn" type="button" name="enableEdit"  data-bs-toggle="modal" data-bs-target="#staticBackdrop"  disabled>Enable</button>
                        <button class="btn btn-danger btn-sm editBtn" type="button" name="disableEdit"  data-bs-toggle="modal" data-bs-target="#staticBackdrop"  disabled>Disabled</button>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody name="tbody" >
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
<!-- js / javascript -->
<script defer>
    (function(){
        'use strict';
        document.addEventListener('click',function(event){
            var trgt = event.target;
            if(trgt.name=="nodatSaveRsm"){
                nodatSaveRsm(trgt);
            }else if(trgt.name=="editdatRsm"){
                editdatRsm(trgt);                
            }else if(trgt.name=="enableEdit"){
                enableEdit(trgt,1);
            }else if(trgt.name=="disableEdit"){
                enableEdit(trgt,0)
            }
        });

        function _(el){
            return document.getElementById(el);
        }
        function c(el){
            return document.getElementsByClassName(el);
        }

        function enableEdit(el,edStat){
            _("staticBackdropLabel").innerHTML = "RSM Edit";
            var addel = document.createElement("div");
            addel.innerHTML = "Calculating.....";
            _("modalCont").innerHTML = "";
            _("modalCont").appendChild(addel);
            var dataTarget = el.getAttribute('data-target').split("||");
            var fd = new FormData();
                fd.append('period',dataTarget[0]);
                fd.append('year',dataTarget[1]);
                fd.append('enableAllRsm',true);
            var xml = new XMLHttpRequest();
                xml.open("POST","?config=rsmStatus",false);
                xml.onprogress =  function(){
                    addel.innerHTML = "Calculating.....";
                    _("modalCont").appendChild(addel);
                }
                xml.onload = function(){
                    var s = document.createElement("span");
                    var addDone = document.createTextNode("Done!");
                    s.style.color = "green";
                    s.appendChild(addDone);
                    addel.appendChild(s);
                    var ar = JSON.parse(xml.responseText);
                    var progress = document.createElement("div");
                        progress.setAttribute('id','progressRsm');
                        _('modalCont').appendChild(progress);
                    var logs = document.createElement("div");
                        logs.setAttribute('id','logsRsm');
                        _('modalCont').appendChild(logs);
                    setTimeout(() => {
                        editAllWith(ar,edStat,0);
                    }, 500);    
                }
                console.log(xml);
                xml.send(fd);
        }
        function doneEl(){
            var s = document.createElement("span");
                    var addDone = document.createTextNode(" - Done!");
                    s.style.color = "green";
                    s.appendChild(addDone);
            return s;
        }
        function editAllWith(ar,editStatus,index){
            var countlength = ar['with'].length+ar['without'].length;
            // add the progress made;
                _('progressRsm').innerHTML = index+1+" of "+ countlength;
            var apLogs = document.createElement("div");    
            if(ar['with'].length>0&&index!=ar['with'].length){
                apLogs.innerHTML = ar['with'][index][1];
                _('logsRsm').appendChild(apLogs);
                var fd = new FormData();
                    fd.append('dataid',ar['with'][index][0]);
                    fd.append('edit',editStatus);
                    fd.append('editAllWith',true);
                var xml = new XMLHttpRequest();
                    xml.open("POST","?config=rsmStatus",false);
                    xml.onload = function(){
                        try{
                            if(xml.responseText=='1'){
                                apLogs.appendChild(doneEl());
                                var i = index+1;
                                setTimeout(() => {
                                    editAllWith(ar,editStatus,i);
                                }, 500);    
                            }
                        }catch(e){
                            alert(e);
                        }
                    }
                    xml.send(fd);
                }else{
                    editAllWithout(ar,editStatus,0);
                }
        }
        function editAllWithout(ar,editStatus,index){
            var countlength = ar['with'].length+ar['without'].length;
            // add the progress made;
                _('progressRsm').innerHTML = index+ar['with'].length+" of "+ countlength;
            var apLogs = document.createElement("div");    
            if(ar['without'].length>0&&index!=ar['without'].length){
                apLogs.innerHTML = ar['without'][index][2];
                _('logsRsm').appendChild(apLogs);

                // [period,department_id,department]

                var fd = new FormData();
                    fd.append('period',ar['without'][index][0]);
                    fd.append('department',ar['without'][index][1]);
                    fd.append('edit',editStatus);
                    fd.append('editAllWithout',true);
                var xml = new XMLHttpRequest();
                    xml.open("POST","?config=rsmStatus",false);
                    xml.onload = function(){
                        try{
                            if(xml.responseText=='1'){
                                apLogs.appendChild(doneEl());
                                var i = index+1;
                                setTimeout(() => {
                                    editAllWithout(ar,editStatus,i);
                                }, 500);    
                            }
                        }catch(e){
                            alert(e);
                        }
                    }
                    xml.send(fd);
                }else{
                    _('progressRsm').appendChild(doneEl());
                    _("staticBackdropCloseBtn").disabled = false;
                    tableRefresh();
                }
        }
        function tableRefresh(){
            var formEls = document.forms.rsmStatusPeriodForm.elements;
            var xml = new XMLHttpRequest();
            var fd = new FormData();
            // button 1st action
                formEls.button.disabled =true;
                formEls.button.innerHTML = "Generating";
            // push data to variable fd 
                fd.append('rsmGetTableData',true);
                fd.append('period',formEls.period.value);
                fd.append('year', formEls.year.value);
            xml.onload = function(){
                document.getElementsByTagName("table").rsmTable.children.tbody.innerHTML = xml.responseText;
                formEls.button.disabled =false;
                formEls.button.innerHTML = "Generate";
                var editBtn = document.getElementsByClassName('editBtn');
                var countBtn = 0;
                while(countBtn<editBtn.length){
                    editBtn[countBtn].disabled=false;
                    editBtn[countBtn].setAttribute('data-target',formEls.period.value+"||"+formEls.year.value);
                    countBtn++;
                }
            }
            xml.open('POST','?config=rsmStatus',false);
            xml.send(fd);
        }

        function nodatSaveRsm(el){
            el.disabled = true;
            let a = el.getAttribute('data-element').split("||");
            var fd = new FormData();
                fd.append('period',a[0]);
                fd.append('department',a[1]);
                fd.append('rsmAdd',true);
            var xml = new XMLHttpRequest();
                xml.onreadystatechange = function(){
                    if(this.readyState == 4 && this.status == 200) {
                        var new_row = document.createElement("tr");
                        new_row.innerHTML = xml.responseText;
                        var old_row = el.parentNode.parentNode;
                        old_row.parentNode.replaceChild(new_row, old_row);
                    }
                }
                xml.open("POST","?config=rsmStatus",false);
                xml.send(fd);        
        }

        function editdatRsm(el){
            el.disabled = true;
            let a = el.getAttribute('data-element');
            var edit = 0;
                if(el.checked){
                    edit = 1;
                }
                var fd = new FormData();
                    fd.append('rsmAdd',true);
                    fd.append('dat',a);
                    fd.append('edit',edit);
                var xml = new XMLHttpRequest(); 
                    xml.onload = function(){
                        // console.log(xml.responseText);
                        var new_row = document.createElement("tr");
                        new_row.innerHTML = xml.responseText;
                        var old_row = el.parentNode.parentNode;
                        old_row.parentNode.replaceChild(new_row, old_row);

                    }
                    xml.open("POST","?config=rsmStatus",false);
                    xml.send(fd);
        }

        // form function 
        var periodForm = document.getElementsByTagName("form").rsmStatusPeriodForm;
        periodForm.addEventListener('submit',function(){
            event.preventDefault();
            var formEls = this.elements;
            var xml = new XMLHttpRequest();
            var fd = new FormData();
            // button 1st action
                formEls.button.disabled =true;
                formEls.button.innerHTML = "Generating";
            // push data to variable fd 
                fd.append('rsmGetTableData',true);
                fd.append('period',formEls.period.value);
                fd.append('year', formEls.year.value);
            xml.onload = function(){
                document.getElementsByTagName("table").rsmTable.children.tbody.innerHTML = xml.responseText;
                formEls.button.disabled =false;
                formEls.button.innerHTML = "Generate";
                var editBtn = document.getElementsByClassName('editBtn');
                var countBtn = 0;
                while(countBtn<editBtn.length){
                    editBtn[countBtn].disabled=false;
                    editBtn[countBtn].setAttribute('data-target',formEls.period.value+"||"+formEls.year.value);
                    countBtn++;
                }
            }
            xml.open('POST','?config=rsmStatus',false);
            xml.send(fd);
        });
    }());
</script>



<!-- php -->
<?php
    function get_yearOp(){
        $past = date("Y")-10;
        $present = date("Y")+1;
        $op = "";
        while($present>=$past){
            $op .= "<option>$present</option>";
            $present--;
        }
        return $op;
    }
?>




