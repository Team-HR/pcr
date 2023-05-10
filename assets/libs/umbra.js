status = 0;
function promp() {
  if (status == 1) {
    return window.onbeforeunload = function (e) {
      e = e || window.event;
      if (e) {
        window.event.returnValue = 'sure?';
      }
    }
  } else {
    return window.onbeforeunload = function (e) {
      e = e || window.event;
      if (e) {
        preventDefault();
      }
    }
    console.log("false");
  }
}

function timer() {
  const timeOut = 30; //mins
  promp();
  s = 0;
  i = setInterval(function () {
    document.onmousemove = function () {
      s = 0;
    }
    if (s >= timeOut) {
      timeoutLog();
      clearInterval(i);
    }
    status = 0;
    s++;
  }, 60000);
}
function login_log() {
  event.preventDefault();
  elements = event.target.elements;
  btn = elements.submitBtn;
  user = elements.user.value;
  pass = elements.pass.value;
  if (user && pass) {
    btn.disabled = true;
    $.post('assets/pages/login/config.php', {
      p_user: user,
      p_pass: pass
    }, function (data, textStatus, xhr) {
      if (data == 1) {
        location.href = "?home";
      } else {
        btn.disabled = false;
        $('#loginAlertMsg').append("<div class='ui negative message' id='msg_div' ><i class='close icon' onclick='this.parentElement.remove()'></i><div id='msg_promp'>" + data + "</div></div>");
      }
    });
  } else {
    $('#loginAlertMsg').append("<div class='ui negative message' id='msg_div' ><i class='close icon' onclick='this.parentElement.remove()'></i><div id='msg_promp'>Empty Field/s</div></div>");
  }
}
function timeoutLog() {
  status = 1;
  promp();
  $("#timePass").val("");
  $.post('?logout', {
    timeOut: true
  }, function (data, textStatus, xhr) {
    $('.loginPop').modal({
      closable: false,
    }).modal('show');
  });
}
function timeoutForm(i) {
  pass = $("#timePass").val();
  $.post('assets/pages/login/config.php', {
    timeOut: i,
    pass: pass
  }, function (data, textStatus, xhr) {
    if (data == 1) {
      $('.loginPop').modal('hide');
      timer();
    } else {
      $("#timeErrorMsg").html(data);
      $("#timeErrorMsg").fadeIn();
    }
  });
}
// rating scale
// scripts start
function rsm_period(i) {
  var period = $("#period").val();
  var year = $("#year").val();
  $.post('?config=rsm', {
    period_check: period,
    year: year
  }, function (data, textStatus, xhr) {
    if (data == 1) {
      // rsmLoad("table");
      window.location.href = "?MotherRatingScale&Edit";
    } else {
      alert(data);
    }
  });
}
function addMFoRsm(i) {
  var rsmCount = $('#rsmcount' + i).val();
  var pid = $('#mfo_pid' + i).val();
  var titleRsm = $('#titleRsm' + i).val();
  $.post('?config=rsm', {
    addRSMData: titleRsm,
    rsmCount: rsmCount,
    pid: pid
  }, function (data, textStatus, xhr) {
    if (data == 1) {
      rsmLoad('table');
    } else {
      alert(data);
    }
  });
}
function EditRsmTitle(i) {
  var editcountRsm = $('#EditcountRsm' + i).val();
  var edittitleRsm = $('#EdittitleRsm' + i).val();
  $.post('?config=rsm', {
    editRsmTitle: edittitleRsm,
    editcountRsm: editcountRsm,
    dataId: i
  }, function (data, textStatus, xhr) {
    if (data == 1) {
      rsmLoad('table');
    } else {
      alert(data);
    }
  });
}
function rsmLoad(i) {
  $('#appLoader').dimmer({ closable: false }).dimmer('show');
  $.post('?config=rsm', {
    page: i
  }, function (data, textStatus, xhr) {
    $('#appLoader').dimmer('hide');
    $("#rsmCont").html(data);
  });
}
function ShowModalSiAdd(dataId) {
  $('#allModal')
    .modal('setting', 'closable', false)
    .modal('show');
  $("#modalContL").html("<div style='text-align: center'><img src='assets/img/loading.gif' style='transform: scale(.1);height:500px'></div>");


  $.post('?config=RSMmodalCont', {
    ModalSiAddCont: dataId
  }, function (data, textStatus, xhr) {
    $("#modalContL").html(data);
  });
}
function MfoSiDelete(dataId) {
  var con = confirm("Are you sure you want to remove this Data?");
  if (con) {
    $.post('?config=rsm', {
      MfoSiDelete: dataId
    }, function (data, textStatus, xhr) {
      if (data == 1) {
        rsmLoad('table');
      } else {
        alert(data);
      }
    });
  }
}
function checkbox(i, type, dataId) {
  var checkbox_status = document.getElementById(i.id);
  if (checkbox_status.checked == true) {
    $("#container" + type + dataId).slideDown(200);
  } else {
    $("#container" + type + dataId).slideUp(200);
  }
}
function SaveMfoSI(i, dataId) {
  quality = ['', '', '', '', '', ''];
  efficiency = ['', '', '', '', '', ''];
  timeliness = ['', '', '', '', '', ''];
  successIn = $("#succIn" + dataId).val();
  incharge = $("#incharge" + dataId).val();
  count = 1;
  while (count <= 3) {
    count_t = 5;
    while (count_t >= 1) {
      if (count == 1) {
        t = $("#input" + count_t + "Timeliness" + dataId).val();
        if (t != "") {
          timeliness[count_t] = t;
        }
      } if (count == 2) {
        t = $("#input" + count_t + "Quality" + dataId).val();
        if (t != "") {
          quality[count_t] = t;
        }
      } else if (count == 3) {
        t = $("#input" + count_t + "Efficiency" + dataId).val();
        if (t != "") {
          efficiency[count_t] = t;
        }
      }
      count_t--;
    }
    count++;
  }
  $.post('?config=rsm', {
    SaveMfoSI: dataId,
    quality: quality,
    efficiency: efficiency,
    timeliness: timeliness,
    successIn: successIn,
    incharge: incharge
  }, function (data, textStatus, xhr) {
    if (data == 1) {
      rsmLoad('table');
      $("#allModal").modal('hide');
    } else {
      alert(data);
    }
  });
}
function siEditOpenModal(i) {
  $('#allModal')
    .modal('setting', 'closable', false)
    .modal('show');
  $.post('?config=RSMmodalCont', {
    siEditnModalCont: i
  }, function (data, textStatus, xhr) {
    $("#modalContL").html(data);
  });
}
function deleteOpenModal(i) {
  var con = confirm("Are You Sure To Delete This?");
  if (con) {
    $.post('?config=rsm', {
      removeSi: i
    }, function (data, textStatus, xhr) {
      if (data == 1) {
        rsmLoad('table');
        $("#allModal").modal('hide');
      } else {
        alert(data);
      }
    });
  }
}
function SaveMfoSIEdit(i, dataId) {
  var quality = ['', '', '', '', '', ''];
  var efficiency = ['', '', '', '', '', ''];
  var timeliness = ['', '', '', '', '', ''];
  var successIn = $("#succIn" + dataId).val();
  var incharge = $("#incharge" + dataId).val();
  var count = 1;
  while (count <= 3) {
    count_t = 5;
    while (count_t >= 1) {
      if (count == 1) {
        t = $("#input" + count_t + "Timeliness" + dataId).val();
        if (t != "") {
          timeliness[count_t] = t;
        }
      } if (count == 2) {
        t = $("#input" + count_t + "Quality" + dataId).val();
        if (t != "") {
          quality[count_t] = t;
        }
      } else if (count == 3) {
        t = $("#input" + count_t + "Efficiency" + dataId).val();
        if (t != "") {
          efficiency[count_t] = t;
        }
      }
      count_t--;
    }
    count++;
  }
  $.post('?config=rsm', {
    SaveMfoSIEdit: dataId,
    quality: quality,
    efficiency: efficiency,
    timeliness: timeliness,
    successIn: successIn,
    incharge: incharge
  }, function (data, textStatus, xhr) {
    if (data == 1) {
      rsmLoad('table');
      $("#allModal").modal('hide');
    } else {
      alert(data);
    }
  });
}

function ShowIPcrModal(i) {
  $("#allModalFull").modal('show');
  $('#contLFull').html("<div style='text-align: center'><img src='assets/img/loading.gif' style='transform: scale(.1);height:500px'></div>");
  $.post('?config=RSMmodalCont', {
    ShowIPcrModalPost: i
  }, function (data, textStatus, xhr) {
    $('#contLFull').html(data);
  });
}
// srcipts End
function cbmodal(i, o) {
  qcb = document.getElementById(i.id);
  if (qcb.checked == true) {
    $('#' + o).show(150);
  } else {
    $('#' + o).hide(150);
    $('#' + o).val("");
  }
}
function performanceRatingCore() {
  period = $("#period").val();
  year = $("#year").val();
  $.post('?config=prContent', {
    findP: true,
    period: period,
    year: year
  }, function (data, textStatus, xhr) {
    // coreFunctionForm
    if (data) {
      window.location.href = "?performanceRating&form";
    }
    // showPr("coreFunction", "");
  });
}
function showPr(page, go) {
  $('#appLoader').dimmer({ closable: false }).dimmer('show');
  $.post('?config=prContent', {
    page: page,
    gotoStep: go
  }, function (data, textStatus, xhr) {
    console.log(data);
    $('#appLoader').dimmer('hide');
    if (data == "error") {
      window.location.href = "?performanceRating&error";
    } else {
      $('#perfratingBody').html(data);
    }

  });
}
function coreShowModal(type, quality, eff, timeli, dataId) {
  $('#allModal')
    .modal('setting', 'closable', false)
    .modal('show');
  $("#modalContL").html("<div style='text-align: center'><img src='assets/img/loading.gif' style='transform: scale(.1);height:500px'></div>");

  $.post('?config=prModalL', {
    coreFucntionInput: true,
    type: type,
    quality: quality,
    eff: eff,
    timeli: timeli,
    dataId: dataId
  }, function (data, textStatus, xhr) {
    $('#modalContL').html(data)
  });
}
function SaveInputedSiData() {
  $("#allModal").modal('hide');
  type = $('#siType').val();
  dataId = $('#siDataId').val();
  actualacc = $('#siActualAcc').val();
  quality = $('#siQuality').val();
  efficiency = $('#siEfficiency').val();
  timeliness = $('#siTimeliness').val();
  remarks = $('#siRemarks').val();
  $.post('?config=prContent', {
    storeGatheredSiData: true,
    type: type,
    dataId: dataId,
    actualacc: actualacc,
    quality: quality,
    efficiency: efficiency,
    timeliness: timeliness,
    remarks: remarks
  }, function (data, textStatus, xhr) {
    if (data == 1) {
      showPr("coreFunction", "");
    } else {
      alert(data);
    }
  });
}
function accOpenModal(i) {
  $('#allModal')
    .modal('setting', 'closable', false)
    .modal('show');
  $("#modalContL").html("<div style='text-align: center'><img src='assets/img/loading.gif' style='transform: scale(.1);height:500px'></div>");

  $.post('?config=prModalL', {
    accOpenAdd: i,
  }, function (data, textStatus, xhr) {
    $('#modalContL').html(data)
  });
}
function saveSi(i) {
  $acc = $("#accomp").val();
  $qual = $("#Quality").val();
  $eff = $("#Efficiency").val();
  $time = $("#Timeliness").val();
  $remark = $("#remark").val();
  $perc = $('#perc').val();
  $.post('?config=prContent', {
    saveSiData: i,
    acc: $acc,
    qual: $qual,
    eff: $eff,
    time: $time,
    remark: $remark,
    perc: $perc
  }, function (data, textStatus, xhr) {
    if (data == 1) {
      $("#allModal").modal('hide');
      showPr("coreFunction", "");
    } else {
      alert(data);
    }
  });
}
function fileUplod() {
  $('#allModal')
    .modal('setting', 'closable', false)
    .modal('show');
  $("#modalContL").html("<div style='text-align: center'><img src='assets/img/loading.gif' style='transform: scale(.1);height:500px'></div>");

  document.getElementById('modalContL').innerHTML = "Please wait!!";
  console.log(event.target.attributes['data-id'].value);
  xml = new XMLHttpRequest();
  fd = new FormData();
  show = event;
  fd.append('dataId', show.target.attributes['data-id'].value);
  xml.onreadystatechange = function () {
    document.getElementById('modalContL').innerHTML = this.responseText;
  }
  xml.open('POST', 'assets/pages/uploadDocs/upload.php', true);
  xml.send(fd);


  // $('#modalContL').html("<center><br><br><br><br><br><br><br><i class='ui folder open icon' style='font-size:50px'></i><br><br><br>this Page Is still Underconstruction</center>")
}
function RemoveCoreFuncData(i) {
  con = confirm("Are you Sure to Remove this Data?");
  if (con == true) {
    $.post('?config=prContent', {
      RemoveCoreFuncDataPost: i,
    }, function (data, textStatus, xhr) {
      if (data == 1) {
        showPr("coreFunction", "");
      } else {
        alert(data);
      }
    });
  }
}
function EditCoreFuncData(i) {
  $('#allModal')
    .modal('setting', 'closable', false)
    .modal('show');
  $("#modalContL").html("<div style='text-align: center'><img src='assets/img/loading.gif' style='transform: scale(.1);height:500px'></div>");

  $.post('?config=prModalL', {
    EditCoreFuncDataPost: i,
  }, function (data, textStatus, xhr) {
    $('#modalContL').html(data)
  });
}
function EditCoreFuncDataSaveChanges(i) {
  $("#allModal").modal('hide');
  criticInput = document.getElementById('criticInput');
  criticInput = criticInput.getElementsByTagName("textarea");
  if (criticInput.length > 0) {
    criticInputTarget = criticInput[0].attributes['data-target'].value;
    criticInput = criticInput[0].value;
  } else {
    criticInput = "";
    criticInputTarget = "";
  }
  $acc = $("#accompEdit").val();
  $qual = $("#QualityEdit").val();
  $eff = $("#EfficiencyEdit").val();
  $time = $("#TimelinessEdit").val();
  $remark = $("#remarkEdit").val();
  $percEdit = $("#percEdit").val();
  $.post('?config=prContent', {
    EditCoreFuncDataSaveChangesPost: i,
    acc: $acc,
    qual: $qual,
    eff: $eff,
    time: $time,
    remark: $remark,
    criticInput: criticInput,
    criticInputTarget: criticInputTarget,
    percEdit: $percEdit
  }, function (data, textStatus, xhr) {
    if (data == 1) {
      showPr("coreFunction", "");
      showRev("UncriticizedPrTable");
    } else {
      alert(data);
    }
  });
}
function addSuppAccomplishement(i) {
  $('#modalContLFull').html("<div style='text-align: center'><img src='assets/img/loading.gif' style='transform: scale(.1);height:500px'></div>");
  $('#allModal')
    .modal('setting', 'closable', false)
    .modal('show');
  $("#modalContL").html("<div style='text-align: center'><img src='assets/img/loading.gif' style='transform: scale(.1);height:500px'></div>");

  $.post('?config=prModalL', {
    addSuppAccomplishementModalContent: i,
  }, function (data, textStatus, xhr) {
    $('#modalContL').html(data);
  });
}
function addSuppAccomplishementSaveData(i) {
  this.event.preventDefault();
  quality = $("#sup_inQuality").val();
  eff = $("#sup_inEfficiency").val();
  time = $("#sup_inTimeliness").val();
  acc = $("#acc_supp").val();
  remark = $("#remarks_supp").val();
  $.post('?config=prContent', {
    addSuppAccomplishementSave: i,
    qual: quality,
    eff: eff,
    time: time,
    acc: acc,
    remark: remark
  }, function (data, textStatus, xhr) {
    if (data == 1) {
      $("#allModal").modal('hide');
      showPr("coreFunction", "");
    } else {
      alert(data);
    }
  });
}
function suppFuncRemoveEmpData(i) {
  con = confirm("Are you sure you want to permanently remove this data?");
  if (con) {
    $.post('?config=prContent', {
      suppFuncRemoveEmpDataPost: i,
    }, function (data, textStatus, xhr) {
      if (data == 1) {
        showPr("coreFunction", "");
      } else {
        $('#allModal')
          .modal('setting', 'closable', false)
          .modal('show');
        $('#modalContL').html(data);
      }
    });
  }
}
function suppFuncEditEmpData(i) {
  $('#allModal')
    .modal('setting', 'closable', false)
    .modal('show');
  $("#modalContL").html("<div style='text-align: center'><img src='assets/img/loading.gif' style='transform: scale(.1);height:500px'></div>");

  $.post('?config=prModalL', {
    suppFuncEditEmpDataPost: i,
  }, function (data, textStatus, xhr) {
    $('#modalContL').html(data);
  });
}
function editSuppAccomplishementdatafunc(i) {
  // var critics = document.getElementById('critics');
  //     critics = critics.getElementsByTagName('textarea')[0];
  var remark = $("#remarks_supp").val();
  var acc = $("#acc_supp").val();
  var efficiency = $("#sup_inEfficiency").val();
  var quality = $("#sup_inQuality").val();
  var timeliness = $("#sup_inTimeliness").val();
  $.post('?config=prContent', {
    editSuppAccomplishementdataPost: i,
    acc: acc,
    efficiency: efficiency,
    quality: quality,
    timeliness: timeliness,
    remarks: remark,
    // remarks                         : critics.value,
    // criticsPerson                : critics.attributes['data-target'].value,
  }, function (data, textStatus, xhr) {
    if (data == 1) {
      showPr("coreFunction", "");
      showRev("UncriticizedPrTable");
      $("#allModal").modal('hide');
    } else {
      $('#allModal')
        .modal('setting', 'closable', false)
        .modal('show');
      $('#modalContL').html(data);
    }
  });
  return false;
}
function saveStrategicFunc() {
  mfo = $("#mfo").val();
  suc_in = $("#suc_in").val();
  acc = $("#acc").val();
  // quality = $("#quality").val();
  // time    = $("#time").val();
  stratAverage = $("#stratAverage").val();
  remark = $("#remark").val();
  $.post('?config=prContent', {
    saveStrategicFuncPost: true,
    mfo: mfo,
    suc_in: suc_in,
    acc: acc,
    // quality               : quality,
    // time                  : time,
    stratAverage: stratAverage,
    remark: remark
  }, function (data, textStatus, xhr) {
    if (data == 1) {
      showPr("coreFunction", "");
      $("#allModal").modal('hide');
    } else {
      $('#allModal')
        .modal('setting', 'closable', false)
        .modal('show');
      $('#modalContL').html(data);
    }
  });
  return false;
}
function noStrategicFunc() {
  mfo = "N/A";
  suc_in = "N/A";
  acc = "N/A";
  stratAverage = 0;
  remark = "N/A";
  $.post('?config=prContent', {
    saveStrategicFuncPost: true,
    mfo: mfo,
    suc_in: suc_in,
    acc: acc,
    stratAverage: stratAverage,
    noStrat: true,
    remark: remark
  }, function (data, textStatus, xhr) {
    if (data == 1) {
      showPr("coreFunction", "");
      $("#allModal").modal('hide');
    } else {
      $('#allModal')
        .modal('setting', 'closable', false)
        .modal('show');
      $('#modalContL').html(data);
    }
  });
  return false;
}
function strategicDeleteFunc(i) {
  con = confirm("Are you sure you want to Remove this Data?");
  if (con) {
    $.post('?config=prContent', {
      strategicDeletePost: i,
    }, function (data, textStatus, xhr) {
      if (data == 1) {
        showPr("coreFunction", "");
      } else {
        $('#allModal')
          .modal('setting', 'closable', false)
          .modal('show');
        $('#modalContL').html(data);
      }
    });
  }
}
function finishperformanceReview(s, r, a) {
  $.post('?config=prContent', {
    finishperformanceReviewPost: true,
    assembleAll: s,
    reviewed: r,
    approved: a
  }, function (data, textStatus, xhr) {
    if (data == 1) {
      showPr("coreFunction", "");
    } else {
      $('#allModal')
        .modal('setting', 'closable', false)
        .modal('show');
      $('#modalContL').html(data);
    }
  });
}

function signatoriesFunc() {
  formType = $('#formType').val();
  immediateSup = $('#immediateSup').val();
  departmentHead = $('#departmentHead').val();
  headAgency = $('#headAgency').val();
  // if(formType == 2){
  //   immediateSup = null;

  if (formType == 3) {
    immediateSup = null;
    departmentHead = null;
  }
  $.post('?config=prContent', {
    signatoriesAddPost: true,
    immediateSup: immediateSup,
    departmentHead: departmentHead,
    headAgency: headAgency,
    formType: formType,
  }, function (data, textStatus, xhr) {
    if (data == 1) {
      showPr("coreFunction", "");
    } else {
      $('#allModal')
        .modal('setting', 'closable', false)
        .modal('show');
      $('#modalContL').html(data);
    }
  });
  return false;
}
function commentRecFunc() {
  comRec = $('#comRec').val();
  $.post('?config=prContent', {
    commentRecPost: comRec,
  }, function (data, textStatus, xhr) {
    if (data == 1) {
      showPr("coreFunction", "");
    } else {
      $('#allModal')
        .modal('setting', 'closable', false)
        .modal('show');
      $('#modalContL').html(data);
    }
  });
  return false;
}
function commentRecModalShow(period, emp_id) {
  $('#allModal')
    .modal('setting', 'closable', false)
    .modal('show');
  $("#modalContL").html("<div style='text-align: center'><img src='assets/img/loading.gif' style='transform: scale(.1);height:500px'></div>");

  $.post('?config=prContent', {
    getCommentRecommendationForm: true,
    commentRecommendationEmpId: emp_id,
    commentRecommendationPeriod: period,
  }, function (data, textStatus, xhr) {
    $("#modalContL").html(data);
  });
}
function commentRecInputType() {
  el = event.target;
  if (el.value.length > 0) {
    el.form.elements.commentBtn.disabled = false;
  } else {
    el.form.elements.commentBtn.disabled = true;
  }
}
function commentReccomendationOfSupp(period, emp_id, fileStatusId) {
  $("#allModal").modal('hide');
  form = event.target;
  if (form.elements.comRec.value.length < 1) {
    alert("Hacker ka ahh!!!");
  } else {
    $.post('?config=prContent', {
      commentReccomendationOfSuppSave: form.elements.comRec.value,
      commentReccomendationOfSuppPeriod: period,
      commentReccomendationOfSuppEmpId: emp_id,
    }, function (data, textStatus, xhr) {
      approval(fileStatusId, emp_id);
    });
  }
  return false;
}
















function unrevRec(i) {
  $.post('?config=revContent', {
    unrevRec: i,
  }, function (data, textStatus, xhr) {
    if (data == 1) {
      // showRev("viewPending");
      window.location.href = "?RPC&subordinates";
    } else {
      $('#allModal')
        .modal('setting', 'closable', false)
        .modal('show');
      $('#modalContL').html(data);
    }
  });
}







// 
function showRev(page) {
  $('#appLoader').dimmer({ closable: false }).dimmer('show');
  $.post('?config=revContent', {
    page: page,
  }, function (data, textStatus, xhr) {
    $('#ReviewcontentSubs').html(data);
    $('#appLoader').dimmer('hide');
  });
}




function UncriticizedEmpIdFunc(i) {
  $.post('?config=revContent', {
    UncriticizedEmpIdPost: i,
  }, function (data, textStatus, xhr) {
    if (data == 1) {
      // showRev("UncriticizedPrTable");
      window.location.href = "?RPC&subordinates&view";
    } else {
      $('#allModal')
        .modal('setting', 'closable', false)
        .modal('show');
      $('#modalContL').html(data);
    }
  });
}
function strategicOpenModal(i) {
  $('#allModal')
    .modal('setting', 'closable', false)
    .modal('show');
  $("#modalContL").html("<div style='text-align: center'><img src='assets/img/loading.gif' style='transform: scale(.1);height:500px'></div>");

  $.post('?config=prModalL', {
    strategicModalContentPost: i,
  }, function (data, textStatus, xhr) {
    $('#modalContL').html(data)
  });
}
function EditStrategicFunc(i) {
  mfo = $("#Editmfo").val();
  suc_in = $("#Editsuc_in").val();
  acc = $("#Editacc").val();
  // q      = $("#EditQ").val();
  // t      = $("#EditT").val();
  stratAverage = $("#EditstratAverage").val();
  remark = $("#Editremark").val();

  $.post('?config=prContent', {
    EditStrategicFuncPost: i,
    mfo: mfo,
    suc_in: suc_in,
    acc: acc,
    // quality               : q,
    // time                  : t,
    stratAverage: stratAverage,
    remark: remark
  }, function (data, textStatus, xhr) {
    if (data == 1) {
      showPr("coreFunction", "");
      $("#allModal").modal('hide');
    } else {
      $('#allModal')
        .modal('setting', 'closable', false)
        .modal('show');
      $('#modalContL').html(data);
    }
  });
  return false;
}
function approval(i, dataId) {
  $.post('?config=revContent', {
    approvalPost: i,
  }, function (data, textStatus, xhr) {
    if (data == 1) {
      UncriticizedEmpIdFunc(dataId);
    } else {
      $('#allModal')
        .modal('setting', 'closable', false)
        .modal('show');
      $('#modalContL').html(data);
    }
  });
}

// iMatrix
function iMatrix_period(i) {
  period = $("#period").val();
  year = $("#year").val();
  check = $.post('?config=iMatrixConfig', {
    period_check: period,
    year: year
  }, function (data, textStatus, xhr) {
    if (data == 1) {
      iMatrixLoad("RatingScale");
    } else {
      alert(data);
      // window.location.href = "?RatingScale&Error";
    }
  });
}
function iMatrixLoad(i) {
  $('#appLoader').dimmer({ closable: false }).dimmer('show');
  $.post('?config=iMatrixConfig', {
    page: i
  }, function (data, textStatus, xhr) {
    // $('#appLoader').dimmer('hide');
    // if error or no rsm found goto 
    if (data == "error") {
      window.location.href = "?RatingScale&Error";
    } else {
      window.location.href = "?RatingScale&View";
      // $("#iMatrixCont").html(data);
    }
  });
}
function fileDisplayContent() {
  ev = event;
  formElement = ev.srcElement.form.elements;
  fileSize = ev.target.files[0].size;
  fileType = ev.target.files[0].name.split(".");
  fileType = fileType[fileType.length - 1];
  fileDictionary = ["xlsx", "png", "pdf", "jpg", "jpeg", "docx", "gif", 'tiff', "zip", "rar"];
  checkFile = fileDictionary.includes(fileType.toLowerCase());
  if (fileSize <= 5000000) {
    if (checkFile) {
      if (!formElement.fileTitle.value) {
        formElement.fileTitle.value = formElement.file1.files[0].name;
      }
      formElement.uploadBtn.disabled = false;
      document.getElementById('fileNameSelectedFile').innerHTML = "<i style='color:#808080ad;'>Selected File:</i> " + ev.target.files[0].name;
    } else {
      alert('Please Insert .docx(word file),.xlsx(excel),.png,.jpg,.jpeg,.gif,.pdf');
      formElement.uploadBtn.disabled = true;
    }
  } else {
    alert("File to large");
    formElement.uploadBtn.disabled = true;
  }
}
function fileUpload() {
  document.getElementById('progressBar').style.display = 'block';
  uploadForm = document.getElementById("upload_form");
  uploadForm.elements;
  ind = 0;
  while (ind < uploadForm.elements.length) {
    uploadForm.elements[ind].disabled = true;
    ind++;
  }
  ev = this.event;
  ev.preventDefault();
  var xml = new XMLHttpRequest();
  var fd = new FormData();
  dataCheckPoint = ev.srcElement.elements.parentId.value;
  fd.append('file', ev.srcElement.elements.file1.files[0]);
  fd.append('fileTitle', ev.srcElement.elements.fileTitle.value);
  fd.append('parentId', ev.srcElement.elements.parentId.value);
  fd.append('fileDescription', ev.srcElement.elements.file1_description.value);
  xml.upload.addEventListener('progress', progressHandler, false);
  xml.addEventListener("load", completeHandler(dataCheckPoint), false);
  xml.addEventListener("error", errorHandler, false);
  xml.addEventListener('abort', abortHandler, false);
  xml.open('POST', 'assets/pages/uploadDocs/file.php');
  xml.send(fd);
}
function progressHandler() {
  window.onbeforeunload = function () {
    return 'Cancel Uploading?';
  }
  var uploadForm = document.getElementById("upload_form");
  var percent = (event.loaded / event.total) * 100;
  document.getElementById('fileBarUpload').style.width = percent + "%";
  document.getElementById('fileTextUpload').innerHTML = Math.round(percent) + "%";
  color = 'grey';
  if (percent > 99) {
    document.getElementById('fileTextUpload').innerHTML = Math.round(percent) + "% Done!!";
    color = 'green';
  }
  document.getElementById('fileBarUpload').style.background = color;
}
function completeHandler(dataCheckPoint) {
  ind = 1;
  document.getElementById('progressBar').style.display = 'none';
  document.getElementById('fileNameSelectedFile').innerHTML = "Click or drag and drop a file";
  while (ind < uploadForm.elements.length) {
    if (uploadForm.elements[ind].type != 'submit') {
      uploadForm.elements[ind].value = null;
    }
    uploadForm.elements[ind].disabled = false;
    ind++;
  }
  document.getElementById('fileBarUpload').style.background = "grey";
  document.getElementById('fileBarUpload').style.width = "0%";
  document.getElementById('fileTextUpload').innerHTML = "0%";
  document.getElementById("upload_form").style.display = 'none';
  document.getElementById("doneUpload_Msg").style.display = 'block';
  loadDocumentations(dataCheckPoint);
}
function errorHandler() {
  alert("something wemt wrong");
}
function abortHandler() {
}
function uploadMore() {
  btn = event.srcElement.parentElement;
  btn.style.display = 'none';
  document.getElementById('upload_form').style.display = 'block';
}
function loadDocumentations(dataIdLoad) {
  document.getElementById('listOfDocument').innerHTML = "<center><h3 style='color:#00000052'>Loading Files..</h3></center>";
  count = 0;
  loadInterval = setInterval(function () {
    if (count >= 1) {
      clearInterval(loadInterval);
    } else {
      window.onbeforeunload = null;
      xml = new XMLHttpRequest();
      fd = new FormData();
      fd.append('documentationId', dataIdLoad);
      xml.onreadystatechange = function () {
        document.getElementById('listOfDocument').innerHTML = this.responseText;
      };
      xml.open("POST", "assets/pages/uploadDocs/getUploads.php", true);
      xml.send(fd);
    }
    count++;
  }, 1000);
}
function removeDocumentationFile() {
  e = event.target.parentElement.parentElement;
  conf = confirm('Are you sure? This cant be recover');
  if (conf) {
    xml = new XMLHttpRequest();
    fd = new FormData();
    fd.append('removeId', e.attributes['data-id'].value);
    fd.append('imgSrc', e.getElementsByTagName('a')[0].attributes.href.value);
    xml.onreadystatechange = function () {
      dat = document.getElementById("parentId").value;
      // dataId = e.attributes['data-target'].value;
      if (this.responseText == 'DONE') {
        loadDocumentations(dat);
      }
    }
    xml.open('POST', 'assets/pages/uploadDocs/removeDocumentation.php', true);
    xml.send(fd);
  }
}
//
function loadPersonalData(personalData) {
  personalData = personalData[0];
  xml = new XMLHttpRequest();
  fd = new FormData();
  fd.append('personalData', personalData.attributes['data-target'].value);
  xml.onreadystatechange = function () {
    personalData.children.modalContLFull.innerHTML = this.responseText;
  }
  xml.open('POST', 'assets/pages/personalData/personalData.php', true);
  xml.send(fd);
}
function passwordCheckValid() {
  inputtxt = event.target;
  passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/;
  if (inputtxt.value.match(passw)) {
    msgcolor = 'green';
    messageVal = 'Great!!';
    retypeStatus = false;
  } else {
    msgcolor = 'red';
    messageVal = 'Password must contain an uppercase,a number and atleast 6 total digits';
    retypeStatus = true;
  }
  _(inputtxt.attributes.msgId.value).style.color = msgcolor;
  _(inputtxt.attributes.msgId.value).innerHTML = messageVal;
  _(inputtxt.attributes['data-target'].value).disabled = retypeStatus;
  _('submitNewPass').disabled = true;
}
function checkMatchPass() {
  inputtxt = event.target;
  inputtxtAttr = inputtxt.attributes;
  // console.log(inputtxt.value);
  newPass = _(inputtxtAttr['data-target'].value).value;
  if (inputtxt.value == newPass) {
    msgcolor = 'green';
    messageVal = 'Matched!!';
    btnStatus = false;
  } else {
    btnStatus = true;
    msgcolor = 'red';
    messageVal = 'This password must matched with the new password that you enter above';
  }
  _(inputtxt.attributes.msgId.value).style.color = msgcolor;
  _(inputtxt.attributes.msgId.value).innerHTML = messageVal;
  _('submitNewPass').disabled = btnStatus;
}
function changePasswordForm() {
  event.preventDefault();
  formElements = event.target;
  xml = new XMLHttpRequest();
  fd = new FormData();
  fd.append('dataId', formElements.attributes['data-id'].value);
  fd.append('oldPass', formElements.elements.oldPass.value);
  fd.append('newPassword', formElements.elements.newPassword.value);
  if (formElements.elements.oldPass.value === formElements.elements.newPassword.value) {
    alert('Why Changing your password with same Password?KID!!');
  } else {
    xml.onreadystatechange = function () {
      if (this.readyState == 4) {
        if (this.responseText == 1) {
          i = 0;
          while (i <= formElements.elements.length - 2) {
            formElements.elements[i].value = "";
            i++;
          };
          _(inputtxt.attributes.msgId.value).style.color = "green";
          _(inputtxt.attributes.msgId.value).innerHTML = "<h3>Successful!!</h2>";
        } else {
          alert(this.responseText);
        }
      }
    }
    xml.open('POST', "assets/pages/personalData/change.php", true);
    xml.send(fd);
  }
}
function changeUsernameForm() {
  event.preventDefault();
  formElement = event.target;
  xml = new XMLHttpRequest();
  fd = new FormData();
  fd.append('newUsername', formElement.elements.newUsername.value);
  fd.append('dataId', formElement.attributes['data-id'].value);
  fd.append('password', formElement.elements.password.value);
  xml.onreadystatechange = function () {
    if (this.responseText == 1) {
      _('oldUsername').innerHTML = formElement.elements.newUsername.value;
      formElement.elements.newUsername.value = "";
      formElement.elements.password.value = "";
      _(formElement.attributes['data-target'].value).innerHTML = "<i style='color:green'>Done!</i>";
    } else {
      _(formElement.attributes['data-target'].value).innerHTML = this.responseText;
    }
  }
  xml.open('POST', "assets/pages/personalData/change.php", true);
  xml.send(fd);
}
function _(el) {
  return document.getElementById(el);
}
function showcommentOfSignatories(dataId) {
  // commentev = event;
  $('#allModal')
    .modal('setting', 'closable', false)
    .modal('show');
  $("#modalContL").html("<div style='text-align: center'><img src='assets/img/loading.gif' style='transform: scale(.1);height:500px'></div>");
  $.post('?config=prModalL', {
    showcommentOfSignatoriesPost: dataId
  }, function (data, textStatus, xhr) {
    $("#modalContL").html(data);
  });
}
function agencies() {
  // var ev = event;
  // console.log($("#agencyTypeSwitch"));
  // console.log(this.event.target.value);
  // var form = $("#agencyTypeSwitch")[0];
  // console.log(form);
  var agencyName = this.event.target.value
  // var form = document.getElementsByTagName('form')[0];
  var form = document.getElementsByTagName('form')[1];

  // console.log(form);
  form.innerHTML = 'Please wait....';
  $.post('?config=getAgencyForm', {
    agencyName: agencyName,
  }, function (data, textStatus, xhr) {
    form.innerHTML = data;
  });
}
function console_log(s) {
  console.log(s);
}
function storeToISIinputs(x, y) {
  x = x.split('||');
  document.getElementById("input5" + y).value = x[5];
  document.getElementById("input4" + y).value = x[4];
  document.getElementById("input3" + y).value = x[3];
  document.getElementById("input2" + y).value = x[2];
  document.getElementById("input1" + y).value = x[1];
  document.getElementById('suggestions' + y).innerHTML = "";
}
function findSuggestion($type, $dataId) {
  ev = event;
  if (ev.keyCode == 13 && ev.srcElement.value.length > 2) {
    document.getElementById('suggestions' + $type + $dataId).innerHTML = "<br><center>Fetching Suggestions.....</center>";
    $.post('?config=suggestions', {
      type: $type,
      dataId: $dataId,
      search: ev.srcElement.value,
    }, function (data, textStatus, xhr) {
      // console.log(data);
      document.getElementById('suggestions' + $type + $dataId).innerHTML = data;
    });
  }
}
function reassign(dataId) {
  $('#allModal')
    .modal('setting', 'closable', false)
    .modal('show');
  $("#modalContL").html("<div style='text-align: center'><img src='assets/img/loading.gif' style='transform: scale(.1);height:500px'></div>");
  $.post('?config=reassign', {
    showModal: 'true',
    dataId: dataId,
  }, function (data, textStatus, xhr) {
    $("#modalContL").html(data);
  });
}
function changePercent(dat) {
  $('#allModal')
    .modal('setting', 'closable', false)
    .modal('show');
  $("#modalContL").html("<div style='text-align: center'><img src='assets/img/loading.gif' style='transform: scale(.1);height:500px'></div>");
  $.post('?config=prModalL', {
    changePercent: 'true',
    dataId: dat,
  }, function (data, textStatus, xhr) {
    $("#modalContL").html(data);
  });
}
function editPercent(e) {
  e.preventDefault();
  percent = e.target.percentEditBadge.value;
  dataId = e.target.datEditBadge.value;
  $.post('?config=prContent', {
    editPercent: true
    , percent: percent
    , dataId: dataId
  }, function (data, textStatus, xhr) {
    if (data == '1') {
      showPr("coreFunction", "");
      $("#allModal").modal('hide');
    } else {
      alert(data);
    }
  });
}
function closeRsm(datID) {
  var con = confirm('Please confirm by clicking yes');
  if (con) {
    $.post('?config=rsm', {
      closeRsm: datID
    }, function (data, textStatus, xhr) {
      if (data == 1) {
        rsmLoad('table');
      } else {
        alert(data);
      }
    })
  }
}
function submitPerformance() {
  $.post('?config=prContent', {
    submitPerformance: true
  }, function (data, textStatus, xhr) {
    showPr("coreFunction", "");
  })
}
function ShowMfoList(dat, dept) {
  $('#allModal')
    .modal('setting', 'closable', false)
    .modal('show');
  $("#modalContL").html("<div style='text-align: center'><img src='assets/img/loading.gif' style='transform: scale(.1);'></div>");
  setTimeout(() => {
    $.post('?config=rsm', {
      getRsmparentChange: dat,
      dept: dept
    }, function (data, textStatus, xhr) {
      $("#modalContL").html(data);
    });
  }, 200);
}
function changeParent(sub, parent) {
  setTimeout(() => {
    $.post('?config=rsm', {
      changeParent: true,
      sub: sub,
      parent: parent
    }, function (data, textStatus, xhr) {
      rsmLoad("table");
      $('#allModal').modal('hide');
    });
  }, 200);
}
function mfoSearchTable(el) {
  var search = el.value.toLowerCase();
  var mfoChangeBody = _('mfoChangeBody');
  var mfos = mfoChangeBody.children;
  var count = 0;
  while (count < mfos.length) {
    if (mfos[count].innerText.toLowerCase().indexOf(search) > -1) {
      mfos[count].style.display = '';
    } else {
      mfos[count].style.display = 'none';
    }
    count++;
  }
}
function copyRSM() {
  // alert()

  $.post('?config=rsm', {
    get_prev_rsm: true
  }, function (data, textStatus, xhr) {
    // console.log(data);
    data = JSON.parse(data)
    // console.log(data);

    $("#rsm_copy_modal .content .previous").html(data.previous);
    $("#rsm_copy_modal .content .new").html(data.new);
    $("#rsm_copy_modal").modal({
      closable: false,
      onApprove: () => {
        // start the duplicating process
        $("#rsm_copy_modal .content").html("<div class='ui active inverted dimmer'><div class='ui text loader'>Copying... please wait...</div></div>");
        $.post('?config=rsm', {
          copy_prev_rsm: true
        }, function (data, textStatus, xhr) {
          location.reload();
        })
        return false;
      }
    }).modal("show");
  })

  // console.log("copy RSM");
}

function copyToRSM() {
  // alert()

  $.post('?config=rsm', {
    copy_to: true
  }, function (data, textStatus, xhr) {
    console.log(data);
  })

  // console.log("copy RSM");
}