<?php
$rsmView = new RsmClass($host, $usernameDb, $password, $database);
$rsmView->set_period($_GET['period']);
$rsmView->set_department($_GET["department"]);
?>

<div id="rsm_pmt_view">
    <table border='1px' style="border-collapse:collapse;width:98%;margin:auto">
        <thead style="background:#00c4ff36;font-size:14px">
            <tr>
                <th rowspan="2" style="padding:20px">MFO / PAP</th>
                <th rowspan="2">Success Indicator</th>
                <th colspan="3" style="width:40px">Rating Matrix</th>
                <th rowspan="2" style="width:40px">Incharge</th>
                <th rowspan="2" style="width:40px">Option</th>
            </tr>
            <tr style="font-size:12px">
                <th>Q</th>
                <th>E</th>
                <th>T</th>
            </tr>
        </thead>
        <tbody>
            <?= $rsmView->get_view() ?>
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
                message: 'Hello Vue!'
            }
        }
    }).mount('#rsm_pmt_view')
    /* Vue3 End*/

    // (function() {
    //     document.addEventListener('click', (e) => {
    //         try {
    //             var t = e.target.attributes['data-target'].value;
    //             if (t == "showIRM") {
    //                 if (openModalIRM()) {
    //                     var fd = new FormData();
    //                     fd.append('getIRM', e.target.attributes['data-id'].value);
    //                     xml($('#modalContL'), "?config=rsmPMTview", fd);
    //                 }
    //             } else if (t == "correction" || t == "mfoCorrection") {
    //                 openModalIRM();
    //                 var tds = e.path[2].children;
    //                 console.log(e.path);
    //                 var divShowSelected = document.createElement("div");
    //                 divShowSelected.style.border = "1px solid #00000073";
    //                 divShowSelected.style.padding = "10px";
    //                 divShowSelected.style.borderRadius = "10px";
    //                 divShowSelected.style.marginBottom = "10px";
    //                 // mfo
    //                 var h3Mfo = document.createElement("h3");
    //                 var textCont = document.createElement("p");
    //                 h3Mfo.innerHTML = "MFO";
    //                 textCont.innerHTML = tds[0].innerText;
    //                 divShowSelected.appendChild(h3Mfo);
    //                 divShowSelected.appendChild(textCont);
    //                 // SI
    //                 var h3SI = document.createElement("h3");
    //                 var textSI = document.createElement("p");
    //                 h3SI.innerHTML = "Success Indicators";
    //                 textSI.innerHTML = tds[1].innerHTML;
    //                 divShowSelected.appendChild(h3SI);
    //                 divShowSelected.appendChild(textSI);
    //                 // Q
    //                 var h3Q = document.createElement("h3");
    //                 var textQ = document.createElement("p");
    //                 h3Q.innerHTML = "Quality";
    //                 textQ.innerHTML = tds[2].innerHTML;
    //                 divShowSelected.appendChild(h3Q);
    //                 divShowSelected.appendChild(textQ);
    //                 // E
    //                 var h3E = document.createElement("h3");
    //                 var textE = document.createElement("p");
    //                 h3E.innerHTML = "Efficiency";
    //                 textE.innerHTML = tds[3].innerHTML;
    //                 divShowSelected.appendChild(h3E);
    //                 divShowSelected.appendChild(textE);
    //                 // E
    //                 var h3T = document.createElement("h3");
    //                 var textT = document.createElement("p");
    //                 h3T.innerHTML = "Timeliness";
    //                 textT.innerHTML = tds[4].innerHTML;
    //                 divShowSelected.appendChild(h3T);
    //                 divShowSelected.appendChild(textT);
    //                 var createForm = document.createElement("form");

    //                 createForm.setAttribute('class', 'ui form');
    //                 var txtarea = document.createElement("textarea");
    //                 txtarea.setAttribute('name', 'addComment');
    //                 var btn = document.createElement("input");
    //                 btn.setAttribute('type', 'submit');
    //                 btn.setAttribute('value', 'Submit Comment');
    //                 btn.setAttribute('class', 'ui primary fluid button');
    //                 createForm.appendChild(divShowSelected);
    //                 createForm.appendChild(txtarea);
    //                 createForm.appendChild(document.createElement("br"));
    //                 createForm.appendChild(document.createElement("br"));
    //                 createForm.appendChild(btn);
    //                 $("#modalContL").html(createForm);
    //                 createForm.addEventListener('submit', (evnt) => {
    //                     evnt.preventDefault();
    //                     var fd = new FormData();
    //                     if (t == "correction") {
    //                         fd.append("siCorrection", e.target.attributes['data-id'].value);
    //                     } else {
    //                         fd.append("mfoCorrection", e.target.attributes['data-id'].value);
    //                     }
    //                     fd.append("correction", evnt.target.addComment.value);
    //                     $res = xml($("#modalContL"), "PMTview", fd);
    //                     $("#allModal").modal("hide");
    //                     location.reload();
    //                 })
    //             } else if (t == "showCorrections" || t == "showCorrectionsMFO") {
    //                 openModalIRM();
    //                 var fd = new FormData();
    //                 if (t == "showCorrections") {
    //                     fd.append("showCorrections", e.target.attributes['data-id'].value);
    //                 } else {
    //                     fd.append("showCorrectionsMFO", e.target.attributes['data-id'].value);
    //                 }
    //                 $res = xml($("#modalContL"), "?config=rsmPMTview", fd);
    //             } else if (t == 'removeCorrection') {
    //                 removeCorrection(e);
    //             }

    //         } catch (e) {

    //         }
    //     });

    //     function openModalIRM() {
    //         $("#modalContL").html("<div style='text-align: center'><img src='assets/img/loading.gif' style='transform: scale(.1);height:500px'></div>");
    //         $('#allModal')
    //             .modal('setting', 'closable', false)
    //             .modal('show');
    //         return 1;
    //     }

    //     function xml(el, link, fd) {
    //         var x = new XMLHttpRequest();
    //         x.onload = function() {
    //             el.html(x.responseText);
    //         }
    //         x.open('POST', link, false);
    //         x.send(fd);
    //         return;
    //     }

    //     function removeCorrection(e) {
    //         var con = confirm("Are you sure?");
    //         if (con) {
    //             var btn = e.target;
    //             var fd = new FormData();
    //             fd.append('arIndex', e.target.attributes['data-id'].value);
    //             fd.append("removeCorrection", true);
    //             var xml = new XMLHttpRequest();
    //             xml.onload = function() {
    //                 if (xml.responseText == 1) {
    //                     location.reload();
    //                 } else {
    //                     alert(xml.responseText);
    //                 }
    //                 // console.log(xml.responseText);
    //             }
    //             xml.open('POST', "?config=rsmPMTview", true);
    //             xml.send(fd);
    //         }
    //     }
    // }())
</script>