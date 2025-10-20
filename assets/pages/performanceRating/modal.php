<?php
require_once "assets/pages/performanceRating/config.php";
require_once "assets/libs/config_class.php";
if (isset($_POST['coreFucntionInput'])) {
  $type = $_POST['type'];
  $quality = $_POST['quality'];
  $eff = $_POST['eff'];
  $timeli = $_POST['timeli'];
  if ($quality == 1) {
    $quality = "inline";
  } else {
    $quality = "none";
  }
  if ($eff == 1) {
    $eff = "inline";
  } else {
    $eff = "none";
  }
  if ($timeli == 1) {
    $timeli = "inline";
  } else {
    $timeli = "none";
  }
  echo "
  <h1 class='ui dividing header
  ' style='text-align:center'>Core Function</h1>
  <input type='hidden' value='$type' id='siType'>
  <input type='hidden' value='$_POST[dataId]' id='siDataId'>
  <div class='ui form'>
  <div class='field'>
  <label>Actual Accomplishedment/Expenses</label>
  <textarea id='siActualAcc'></textarea>
  </div>
  </div>
  <br>
  <div class='ui form'>
  <div class='field' style='display:$quality'>
  <label>Quality</label>
  <select class='ui search dropdown' id='siQuality'>
  <option value=''></option>
  <option value='5'>Outstandnig</option>
  <option value='4'>Very Satisfactory</option>
  <option value='3'>Satisfactory</option>
  <option value='2'>Unsatisfactory</option>
  <option value='1'>Poor</option>
  </select>
  </div>
  <br>
  </div>
  <div class='ui form'>
  <div class='field' style='display:$eff'>
  <label>Efficiency</label>
  <select class='ui search dropdown' id='siEfficiency'>
  <option value=''></option>
  <option value='5'>Outstandnig</option>
  <option value='4'>Very Satisfactory</option>
  <option value='3'>Satisfactory</option>
  <option value='2'>Unsatisfactory</option>
  <option value='1'>Poor</option>
  </select>
  </div>
  </div>
  <br>
  <div class='ui form'>
  <div class='field' style='display:$timeli'>
  <label>Timeliness</label>
  <select class='ui search dropdown' id='siTimeliness'>
  <option value=''></option>
  <option value='5'>Outstandnig</option>
  <option value='4'>Very Satisfactory</option>
  <option value='3'>Satisfactory</option>
  <option value='2'>Unsatisfactory</option>
  <option value='1'>Poor</option>
  </select>
  </div>
  </div>
  <br>
  <div class='ui form'>
  <div class='field'>
  <label>Remarks</label>
  <textarea id='siRemarks'></textarea>
  </div>
  </div>
  <br>
  <button class='ui fluid primary button'onclick='SaveInputedSiData()'>Save</button>
  ";
} elseif (isset($_POST['accOpenAdd'])) {
  function s($mysqli, $type)
  {
    $sql = "SELECT * from spms_matrixindicators where mi_id='$_POST[accOpenAdd]'";
    $sql = $mysqli->query($sql);
    $sql = $sql->fetch_assoc();
    if ($type == 'Quality') {
      $q = unserialize($sql['mi_quality']);
    } elseif ($type == 'Efficiency') {
      $q = unserialize($sql['mi_eff']);
    } elseif ($type == 'Timeliness') {
      $q = unserialize($sql['mi_time']);
    }
    $qnum = 1;
    $optQ = "";
    while ($qnum <= count($q) - 1) {
      if ($q[$qnum] != "") {
        $optQ .= "<option value='$qnum'>" . $q[$qnum] . "</option>";
      }
      $qnum++;
    }
    if ($optQ != "") {
      $style = "";
    } else {
      $optQ = "<option value=''></option>";
      $style = "style='display:none'";
    }
    $view = "
    <div class='field' $style>
    <label>$type</label>
    <select class='ui fluid dropdown' id='$type'>
    $optQ
    </select>
    </div>";
    return $view;
  }
  $sqlSuccess = "SELECT * from spms_matrixindicators where mi_id='$_POST[accOpenAdd]'";
  $sqlSuccess = $mysqli->query($sqlSuccess);
  $sqlSuccess = $sqlSuccess->fetch_assoc();

  echo "
  <div class='ui form'>
  <div class='field'>
  <label>Success Indicators</label>
  <p style='padding:10px;border:1px solid #dedede;border-radius:5px 5px 5px 5px'>$sqlSuccess[mi_succIn]</p>
  </div>
  <div class='field'>
  <label>Actual Accomplishments</label>
  <textarea id='accomp'></textarea>
  </div>
  <div class='ui horizontal divider'>
  Rating
  </div>
  " . s($mysqli, "Quality") . s($mysqli, "Efficiency") . s($mysqli, "Timeliness") . "
  <div class='field'>
  <label>Weight Allocation(%)</label>
  <input type='number' id='perc'>
  </div>
  <div class='field'>
  <label>Remark</label>
  <textarea id='remark'></textarea>
  </div>
  <button class='ui fluid primary button'onclick='saveSi(\"" . $_POST['accOpenAdd'] . "\")'>Save</button>
  </div>
  ";
} elseif (isset($_POST['EditCoreFuncDataPost'])) {
  // modal for inputing and editing the core functions
  // this will be be used by the individual,sectionHead, departmentHead, and even pmt
  // including

  $dataId = $_POST['EditCoreFuncDataPost'];
  function s($mysqli, $type, $ind, $pmtCheck)
  {
    $sqlChild = "SELECT * FROM `spms_corefucndata` where cfd_id='$_POST[EditCoreFuncDataPost]'";
    $sqlChild = $mysqli->query($sqlChild);
    $sqlChild = $sqlChild->fetch_assoc();
    $sql = "SELECT * from spms_matrixindicators where mi_id='$sqlChild[p_id]'";
    $sql = $mysqli->query($sql);
    $sql = $sql->fetch_assoc();
    if ($type == 'Quality') {
      $q = unserialize($sql['mi_quality']);
    } elseif ($type == 'Efficiency') {
      $q = unserialize($sql['mi_eff']);
    } elseif ($type == 'Timeliness') {
      $q = unserialize($sql['mi_time']);
    }
    $qnum = 1;
    $optQ = "";
    while ($qnum <= count($q) - 1) {
      if ($q[$qnum] != "") {
        if ($qnum == $ind) {
          $optQ .= "<option value='$qnum' selected>" . $q[$qnum] . "</option>";
        } else {
          $optQ .= "<option value='$qnum'>" . $q[$qnum] . "</option>";
        }
      }
      $qnum++;
    }
    if ($optQ != "") {
      $style = "";
    } else {
      $optQ = "<option value=''></option>";
      $style = "style='display:none'";
    }

    // if($pmtCheck){
    //   $view = "
    //   <div class='field' $style>
    //   <label>$type</label>
    //   <input type='number' step='0.001' id='".$type."Edit' value='".$ind."'>
    //   </div>";
    // }else{
    $view = "
      <div class='field' $style>
      <label>$type</label>
      <select class='ui fluid dropdown' id='" . $type . "Edit'>
      $optQ
      </select>
      </div>";
    // }
    return $view;
  }


  $sql = "SELECT * FROM `spms_corefucndata` where cfd_id='$dataId'";
  $sql = $mysqli->query($sql);
  $sql = $sql->fetch_assoc();
  $IS  = "";
  $DH  = "";
  $PMT = "";
  if ($sql['critics']) {
    $critics = unserialize($sql['critics']);
    $IS = $critics['IS'];
    $DH = $critics['DH'];
    $PMT = $critics['PMT'];
  }



  $immediateSuppCriticInput = "<div class='ui segments'>
  <div class='ui yellow inverted segment'>
  <p>Immediate Suppervisor</p>
  </div>
  <textarea class='ui secondary' data-target='IS' rows='2' placeholder='Type Here'>$IS</textarea>
  </div>";

  $departmentHeadCriticInput = "<div class='ui segments'>
  <div class='ui orange inverted segment'>
  <p>Department Head</p>
  </div>
  <textarea class='ui secondary' data-target='DH' rows='2' placeholder='Type Here'>$DH</textarea>
  </div>";

  $pmtCriticInput = "<div class='ui segments'>
  <div class='ui red inverted segment'>
  <p>PMT</p>
  </div>
  <textarea class='ui secondary' data-target='PMT' rows='2' placeholder='Type Here'>$PMT</textarea>
  </div>";

  // dont look its just to dump
  $getPeriodId = "SELECT 	* from `spms_matrixindicators` where `mi_id`='$sql[p_id]'";
  $getPeriodId = $mysqli->query($getPeriodId);
  $getPeriodId = $getPeriodId->fetch_assoc();
  $getPeriodId = "SELECT * from `spms_corefunctions` where `cf_ID`='$getPeriodId[cf_ID]'";
  $getPeriodId = $mysqli->query($getPeriodId);
  $getPeriodId = $getPeriodId->fetch_assoc();
  $coreFunctionData = new Employee_data();
  $coreFunctionData->set_emp($sql['empId']);
  $coreFunctionData->set_period($getPeriodId['mfo_periodId']);
  $fileStatus = $coreFunctionData->fileStatus;
  $eveluatorsId = $_SESSION['emp_id'];
  $criticInput = "";
  $pmtCheck = false;
  if ($fileStatus['PMT'] == $eveluatorsId) {
    $criticInput = $pmtCriticInput;
    $pmtCheck = true;
  } elseif ($fileStatus['DepartmentHead'] == $eveluatorsId) {
    $criticInput = $departmentHeadCriticInput;
  } elseif ($fileStatus['ImmediateSup'] == $eveluatorsId) {
    $criticInput = $immediateSuppCriticInput;
  }

  $sqlChildSucIn = "SELECT * FROM `spms_corefucndata` where cfd_id='$_POST[EditCoreFuncDataPost]'";
  $sqlChildSucIn = $mysqli->query($sqlChildSucIn);
  $sqlChildSucIn = $sqlChildSucIn->fetch_assoc();
  $sqlSucIn = "SELECT * from spms_matrixindicators where mi_id='$sqlChildSucIn[p_id]'";
  $sqlSucIn = $mysqli->query($sqlSucIn);
  $sqlSucIn = $sqlSucIn->fetch_assoc();
  echo "
  <div class='ui form'>
  <div class='field'>
  <label>Success Indicators</label>
  <p style='padding:10px;border:1px solid #dedede;border-radius:5px 5px 5px 5px'>$sqlSucIn[mi_succIn]</p>
  </div>
  <div class='field'>
  <label>Actual Accomplishments</label>
  <textarea id='accompEdit'>$sql[actualAcc]</textarea>
  </div>
  <div class='ui horizontal divider'>
  Rating
  </div>
  " . s($mysqli, "Quality", $sql['Q'], $pmtCheck) . s($mysqli, "Efficiency", $sql['E'], $pmtCheck) . s($mysqli, "Timeliness", $sql['T'], $pmtCheck) . "
  <div class='field'>
    <label>Weight Allocation(%)</label>
    <input type='text' id='percEdit' value='$sql[percent]'>
  </div>
  <div class='field' style='_display:none'>
  <label>Remarks</label>
  <textarea id='remarkEdit'>$sql[remarks]</textarea>
  </div>
  <div id='criticInput'>
  $criticInput
  </div>
  <br>
  <button class='ui fluid primary button'onclick='EditCoreFuncDataSaveChanges($dataId)'>Save Changes</button>
  </div>
  ";
} elseif (isset($_POST['addSuppAccomplishementModalContent'])) {

  $dataId = $_POST['addSuppAccomplishementModalContent'];
  function cb($mysqli, $type, $col)
  {
    $dataId = $_POST['addSuppAccomplishementModalContent'];
    $sql = "SELECT * FROM `spms_supportfunctions` where id_suppFunc='$dataId'";
    $sql = $mysqli->query($sql);
    $sql = $sql->fetch_assoc();
    $a = unserialize($sql[$col]);
    $count = 1;
    $op = "";
    while ($count <= 5) {
      if ($a[$count] != "") {
        $op .= "<option value='$count'>$a[$count]</option>";
      }
      $count++;
    }
    if ($op == "") {
      $dis = "style='display:none'";
    } else {
      $dis = "";
    }
    $view = "
    <div class='field' $dis>
    <label>$type</label>
    <select class='ui fluid dropdown' id='sup_in$type'>
    $op
    </select>
    </div>
    ";
    return $view;
  }

  $sqlSuc = "SELECT * FROM `spms_supportfunctions` where id_suppFunc='$dataId'";
  $sqlSuc = $mysqli->query($sqlSuc);
  $sqlSuc = $sqlSuc->fetch_assoc();
  echo "
  <script>
  $('.ui.dropdown').dropdown();
  </script>
  <h1>Support Function</h1>
  <span></span>
  <div class='ui divider'></div>
  <form onsubmit='return addSuppAccomplishementSaveData($dataId)'>
  <div class='ui form'>
  <div class='field'>
  <label>Success Indicators</label>
  <p style='padding:10px;border:1px solid #dedede;border-radius:5px 5px 5px 5px'>$sqlSuc[suc_in]</p>
  </div>
  <div class='field'>
  <label>Accomplishments</label>
  <textarea id='acc_supp'></textarea>
  </div>
  " . cb($mysqli, "Quality", "Q") . cb($mysqli, "Efficiency", "E") . cb($mysqli, "Timeliness", "T") . "
  <div class='field'>
  <label>Remarks</label>
  <textarea id='remarks_supp'></textarea>
  </div>
  <button class='ui primary fluid button' >Save</button>
  </form>
  </div>
  ";
} elseif (isset($_POST['suppFuncEditEmpDataPost'])) {
  $empdataId = $_POST['suppFuncEditEmpDataPost'];
  $sqldata = "SELECT * from spms_supportfunctiondata where sfd_id='$empdataId'";
  $sqldata = $mysqli->query($sqldata);
  $sqldata = $sqldata->fetch_assoc();
  $pmtCheck = false;
  $supportFunction = new Employee_data();

  if (isset($_SESSION['empIdPending']) && isset($_SESSION['periodPending'])) {
    $emp = $_SESSION['empIdPending'];
    $period = $_SESSION['periodPending'];
    $supportFunction->set_emp($emp);
    $supportFunction->set_period($period);
    $fileStatus = $supportFunction->fileStatus;
    $eveluatorsId = $_SESSION['emp_id'];
    $criticInput = "";
    if ($fileStatus['PMT'] == $eveluatorsId) {
      $criticInput = $pmtCriticInput;
      $pmtCheck = true;
    }
    // elseif($fileStatus['ImmediateSup']==$eveluatorsId){
    //   $criticInput = $immediateSuppCriticInput;
    // }elseif($fileStatus['DepartmentHead']==$eveluatorsId){
    //   $criticInput = $departmentHeadCriticInput;
    // }
  }

  function cb($mysqli, $type, $col, $pmtCheck)
  {

    $empdataId = $_POST['suppFuncEditEmpDataPost'];
    $sqldata = "SELECT * from spms_supportfunctiondata where sfd_id='$empdataId'";
    $sqldata = $mysqli->query($sqldata);
    $sqldata = $sqldata->fetch_assoc();
    $sql = "SELECT * FROM `spms_supportfunctions` where id_suppFunc='$sqldata[parent_id]'";
    $sql = $mysqli->query($sql);
    $sql = $sql->fetch_assoc();
    $a = unserialize($sql[$col]);
    $count = 1;
    $op = "";
    while ($count <= 5) {
      if ($a[$count] != "") {
        if ($count == $sqldata[$col]) {
          $op .= "<option value='$count' selected>$a[$count]</option>";
        } else {
          $op .= "<option value='$count'>$a[$count] </option>";
        }
      }
      $count++;
    }
    if ($op == "") {
      $dis = "style='display:none'";
    } else {
      $dis = "";
    }

    // if($pmtCheck){
    //   $view = "
    //   <div class='field' $dis>
    //   <label>$type</label>
    //   <input class='ui fluid' id='sup_in$type' value='$sqldata[$col]'>
    //   </div>
    //   ";
    // }else{
    $view = "
      <div class='field' $dis>
      <label>$type</label>
      <select class='ui fluid dropdown' id='sup_in$type'>
      $op
      </select>
      </div>
      ";
    // }
    return $view;
  }

  $sqldataSuccIn = "SELECT * from spms_supportfunctiondata where sfd_id='$_POST[suppFuncEditEmpDataPost]'";
  $sqldataSuccIn = $mysqli->query($sqldataSuccIn);
  $sqldataSuccIn = $sqldataSuccIn->fetch_assoc();
  $sqlSuccIn = "SELECT * FROM `spms_supportfunctions` where id_suppFunc='$sqldataSuccIn[parent_id]'";
  $sqlSuccIn = $mysqli->query($sqlSuccIn);
  $sqlSuccIn = $sqlSuccIn->fetch_assoc();

  echo "
    <script>
    $('.ui.dropdown').dropdown();
    </script>
    <h1>Support Function</h1>
    <span></span>
    <div class='ui divider'></div>
    <form onsubmit='return editSuppAccomplishementdatafunc($empdataId)'>
    <div class='ui form'>
    <div class='field'>
    <label>Success Indicators</label>
    <p style='padding:10px;border:1px solid #dedede;border-radius:5px 5px 5px 5px'>$sqlSuccIn[suc_in]</p>
    </div>
    <div class='field'>
    <label>Accomplishments</label>
    <textarea id='acc_supp'>$sqldata[accomplishment]</textarea>
    </div>
    " . cb($mysqli, "Quality", "Q", $pmtCheck) . cb($mysqli, "Efficiency", "E", $pmtCheck) . cb($mysqli, "Timeliness", "T", $pmtCheck) . "
    <div class='field'>
    <label>Remarks</label>
    <textarea id='remarks_supp'>$sqldata[remark]</textarea>
    </div>
    <button class='ui primary fluid button' >Save Changes</button>
    </form>
    </div>
  ";
} elseif (isset($_POST['strategicModalContentPost'])) {
  // strategic form modal
  $dataId = $_POST['strategicModalContentPost'];
  $sql = "SELECT * from spms_strategicfuncdata where strategicFunc_id='$dataId'";
  $sql = $mysqli->query($sql);
  $sql = $sql->fetch_assoc();
  echo "
    <br>
    <br>
    <div class='ui three column grid'>
    <div class='column'>
    </div>
    <div class='column'>
    <div class='ui fluid'>
    <h1 style='color:#6f6f73'>Strategic Function</h1>
    <form class='ui form' onsubmit='return EditStrategicFunc($dataId)'>
    <div class='field'>
    <label>MFO/PAP:</label>
    <textarea rows='1' id='Editmfo' required placeholder='...'>$sql[mfo]</textarea>
    </div>
    <div class='field'>
    <label>Success Indicator:</label>
    <textarea rows='2' id='Editsuc_in' placeholder='Enter success indicator here...'>$sql[succ_in]</textarea>
    </div>
    <div class='field'>
    <label>Actual Accomplishment:</label>
    <textarea rows='2' id='Editacc' placeholder='Enter the actual accomplishment here...'>$sql[acc]</textarea>
    </div>
<!--    
    <div class='field'>
      <label>Quality</label>
      <input type='number' value='$sql[Q]' id='EditQ'>
    </div>
    <div class='field'>
      <label>Timeliness</label>
      <input type='number' value='$sql[T]' id='EditT'>
    </div>
-->
    <div class='field'>
      <label>Final Rating:</label>
      <input name='my_field' value='$sql[average]' pattern='^\d*(\.\d{0,3})?$' id='EditstratAverage' placeholder='1 to 5'>
    </div>
    <div class='field'>
    <label>Remark</label>
    <textarea rows='2' id='Editremark' placeholder='Enter remarks here...'>$sql[remark]</textarea>
    </div>
    <input type='submit' class='ui fluid button' value='Save'>
    </form>
    </div>
    </div>
    <div class='column'>
    </div>
    </div>
  ";
} elseif (isset($_POST['showcommentOfSignatoriesPost'])) {
  $dataId = $_POST['showcommentOfSignatoriesPost'];
  $sql = "SELECT * from `spms_corefucndata` where `cfd_id` = '$dataId' ";
  $sql = $mysqli->query($sql);
  $sqlData = $sql->fetch_assoc();
  if ($sqlData['critics']) {
    $criticsData = unserialize($sqlData['critics']);
    $viewContent = "";
    if ($criticsData['IS']) {
      $viewContent .= "
      <div class='ui segments'>
      <div class='ui yellow inverted segment'>
        <h3>Immediate Suppervisor</h3>
      </div>
      <div class='ui secondary segment'>
      " . nl2br($criticsData['IS']) . "
      </div>
      </div>
      ";
    }
    if ($criticsData['DH']) {
      $viewContent .= "
      <div class='ui segments'>
      <div class='ui orange inverted segment'>
        <h3>Department Head</h3>
      </div>
      <div class='ui secondary segment'>
      " . nl2br($criticsData['DH']) . "
      </div>
      </div>
      ";
    }
    if ($criticsData['PMT']) {
      $viewContent .= "
      <div class='ui segments'>
      <div class='ui red inverted segment'>
        <h3>PMT</h3>
      </div>
      <div class='ui secondary segment'>
      " . nl2br($criticsData['PMT']) . "
      </div>
      </div>
      ";
    }
    echo "
    <h1>
    View Comments
    </h1>
    <div class='ui divider'></div>
    $viewContent
    ";
  }
} elseif (isset($_POST['changePercent'])) {
  $sql = "SELECT * from spms_corefucndata where `cfd_id`='$_POST[dataId]'";
  $sql = $mysqli->query($sql);
  $dat = $sql->fetch_assoc();
  $view = "
    <div style='margin:auto;width:500px;'>
      <div class='header'>Change Weight Allocation(%)</div>
      <div class='content'>
        <form class='ui form' method='POST' onsubmit='editPercent(event)'>
          <div class='field'>
            <input type='hidden' value='$_POST[dataId]' id='datEditBadge'>
            <input type='text' value='$dat[percent]' id='percentEditBadge'>
          </div>
            <input type='submit' value='Change' class='ui button'>
        </form>
      </div>
    </div>    ";

  echo $view;
} elseif (false) {
} else {
  echo notFound();
}
