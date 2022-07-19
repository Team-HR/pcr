<?php
// $rsmView = new RsmClass($host, $usernameDb, $password, $database);
// $rsmView->set_period($_GET['period']);
// $rsmView->set_department($_GET["department"]);
// $rsmView->get_rating_scale_matrix();
?>
<div id="rsm_pmt_view">
    <table class="ui mini compact celled structured table" style="border-collapse:collapse;width:98%;margin:auto">
        <thead style="background:#00c4ff36;font-size:14px">
            <tr>
                <th rowspan="2" style="padding:20px">MFO / PAP</th>
                <th rowspan="2">Success Indicator</th>
                <th colspan="3" style="width:40px">Rating Matrix</th>
                <th rowspan="2" style="min-width:100px">Incharge</th>
                <th rowspan="2" style="width:40px">Success Indicator Option</th>
            </tr>
            <tr style="font-size:12px">
                <th>Q</th>
                <th>E</th>
                <th>T</th>
            </tr>
        </thead>
        <tbody id="tbody_rsm">
            <tr v-if="items.length < 1">
                <td colspan="7">
                    <div style="min-height: 500px;"></div>
                </td>
            </tr>
            <tr v-for="item in items" :key="item.cf_ID">
                <template v-if="!item.mi_id">
                    <td colspan="7">
                        <div :style="'margin-left:'+(item.level*50)+'px;' ">
                            <!-- {{ item.id }} -->
                            <button class="ui mini green button" @click="edit_mfo_corrections(item)" style="margin-right: 15px;"><i class="ui edit icon"></i>MFO</button>
                            <span :style="item.correction_status?'color:'+item.correction_status:''" @click="edit_mfo_corrections(item)"> {{ item.code + " " + item.title }}</span>
                            <!-- <br>
                            {{ item.mfo_corrections }}
                            <br>
                            {{ item.correction_status }} -->

                        </div>
                    </td>
                </template>
                <template v-else>
                    <td>
                        <div :style="'margin-left:'+(item.level*50)+'px;'">
                            <!-- {{ item.id }} -->
                            <button v-if="item.title" class="ui mini green button" @click="edit_mfo_corrections(item)" style="margin-right: 15px;"><i class="ui edit icon"></i>MFO</button>
                            <span :style="item.correction_status?'color:'+item.correction_status:''" @click="edit_mfo_corrections(item)"> {{ item.code + " " + item.title }}</span>
                            <!-- <br>
                            {{ item.mfo_corrections }}
                            <br>
                            {{ item.correction_status }} -->
                        </div>
                    </td>
                    <td>
                        {{
                            item.success_indicator
                        }}
                    </td>
                    <td>
                        <template v-for="(quality, i) in item.qualities" :key="i">
                            <div>
                                {{ quality.score + " - " + quality.description }}
                            </div>
                        </template>
                    </td>
                    <td>
                        <template v-for="(efficiency, i) in item.efficiencies" :key="i">
                            <div>
                                {{ efficiency.score + " - " + efficiency.description }}
                            </div>
                        </template>
                    </td>
                    <td>
                        <template v-for="(timeliness, i) in item.timelinesses" :key="i">
                            <div>
                                {{ timeliness.score + " - " + timeliness.description }}
                            </div>
                        </template>
                    </td>
                    <td style="white-space: nowrap;">
                        <template v-for="(employee, i) in item.incharges" :key="i">
                            {{ employee.name }} <br>
                        </template>
                    </td>
                    <td>
                        <button class="ui mini blue button"><i class="ui edit icon"></i>Add Correction</button>
                    </td>
                </template>

            </tr>
        </tbody>
    </table>

    <!-- mfo edit corrections start -->
    <div id="mfo_correction_modal" class="ui modal">
        <div class="header">
            MFO/PAP Corrections
        </div>
        <div class="content">
            <form id="mfo_correction_form" class="ui form" @submit.prevent="save_mfo_correction()">
                <div class="field">
                    <label>MFO/PAP:</label>
                    <input type="text" readonly :value="mfo_edit_item.code +' '+ mfo_edit_item.title"></input>
                </div>
                <div class="field" v-if="mfo_edit_item.mfo_corrections">
                    <!-- <label>Corrections:</label> -->
                    <table class="ui small compact celled structured table" style="width: 100%;">
                        <thead>
                            <tr style="text-align: center;">
                                <th>
                                    Corrections
                                </th>
                                <th>
                                    Status
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(mfo_correction, i) in mfo_edit_item.mfo_corrections" :key="i">
                                <td><span v-html="mfo_correction[0]"></span></td>
                                <td style="text-align: center;">
                                    <span v-if="mfo_correction[1]" style="color: green;">Accomplished</span>
                                    <span v-else style="color: red;">Unaccomplished</span>
                                </td>
                                <td style="text-align: center;">
                                    <button v-if="!mfo_correction[1]" class="ui mini red button" type="button" @click="remove_mfo_correction(i)">Remove</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="field">
                    <label>Add Corrections:</label>
                    <textarea v-model="mfo_correction"></textarea>
                </div>
            </form>
        </div>
        <div class="actions">
            <div class="ui deny button">
                Close
            </div>
            <button form="mfo_correction_form" type="submit" class="ui right labeled icon green button">
                Save
                <i class="checkmark icon"></i>
            </button>
        </div>
    </div>
    <!-- mfo edit corrections end -->

    <!-- si edit corrections start -->

    <!-- si edit corrections end -->
    <!-- loading modal start -->
    <div id="loading_modal" class="ui modal">

    </div>
    <!-- loading modal end -->
</div>
<script>
    /* Vue3 Start*/
    const {
        createApp
    } = Vue

    createApp({
        data() {
            return {
                // loading: true,
                mfo_edit_item: {},
                mfo_correction: "",
                items: []
            }
        },
        watch: {
            // loading(newValue, oldValue) {
            //     if (newValue) {
            //         $('tbody').dimmer('show')
            //     } else $('tbody').dimmer('hide')
            // },
        },
        methods: {

            get_rating_scale_matrix() {
                $('#tbody_rsm').dimmer({
                        closable: false,
                    })
                    .dimmer('add content', '<div class="ui text loader">Loading<div>')
                    .dimmer('show ');
                const queryString = window.location.search;
                const urlParams = new URLSearchParams(queryString);
                const period_id = urlParams.get('period');
                const department_id = urlParams.get('department');
                $.get("?config=rsmPMTview", {
                        get_rating_scale_matrix: true,
                        period_id: period_id,
                        department_id: department_id
                    }, (data, textStatus, jqXHR) => {
                        // console.log("get_rating_scale_matrix:", data);
                        this.items = JSON.parse(data);
                        $('#tbody_rsm')
                            .dimmer('hide')
                    },
                    "json"
                );
            },
            edit_mfo_corrections(item) {
                this.mfo_edit_item = item;
                $("#mfo_correction_modal").modal({
                    closable: false,
                }).modal("show")
            },
            remove_mfo_correction(index) {
                if (confirm('Are you sure you want to delete this correction?')) {
                    $.post("?config=rsmPMTview", {
                            remove_mfo_correction: true,
                            index: index,
                            cf_ID: this.mfo_edit_item.id
                        }, (data, textStatus, jqXHR) => {
                            data ? this.mfo_edit_item.mfo_corrections.splice(index, 1) : null
                            this.get_rating_scale_matrix()
                        },
                        "json"
                    );
                }
            },
            save_mfo_correction() {
                // console.log(this.mfo_edit_item);
                $.post("?config=rsmPMTview", {
                    add_correction: true,
                    cf_ID: this.mfo_edit_item.id,
                    correction: this.mfo_correction
                }).then(res => {
                    // console.log(res);
                    if (res == "false") {
                        alert('There is an existing unaccomplished correction, cannot add new correction until accomplished. Please remove/wait for the department to accomplish to add new correction.')
                    } else {
                        this.get_rating_scale_matrix()
                        this.mfo_edit_item = {}
                        this.mfo_correction = ""
                        $("#mfo_correction_modal").modal({
                            closable: false,
                        }).modal("hide")
                    }
                })
            }
        },
        mounted() {
            this.get_rating_scale_matrix()
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