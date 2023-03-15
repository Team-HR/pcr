<?php
// copy previous rsm start

if (isset($_POST['get_prev_rsm'])) {
  $data = [];
  $selected_period_id = $_SESSION["period"];

  // get selected period data
  $sql = "SELECT * FROM `spms_mfo_period` WHERE `mfoperiod_id` = '$selected_period_id';";
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  //  {"mfoperiod_id":"10","month_mfo":"July - December","year_mfo":"2022"}

  $selected_months = $row["month_mfo"];
  $selected_year = $row["year_mfo"];

  # get previous period data start
  $period_id = 0;
  $months = "";
  $year = "";
  if ($selected_months == "July - December") {
    $months = "January - June";
    $year = $selected_year;
  } else {
    $months = "July - December";
    $year = $selected_year - 1;
  }
  # get previous period data end
  echo json_encode([
    "previous" => "$months $year",
    "new" => "$selected_months $selected_year"
  ]);
} elseif (isset($_POST['copy_prev_rsm'])) {
  $selected_period_id = $_SESSION["period"];

  // get selected period data
  $sql = "SELECT * FROM `spms_mfo_period` WHERE `mfoperiod_id` = '$selected_period_id';";
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  //  {"mfoperiod_id":"10","month_mfo":"July - December","year_mfo":"2022"}

  $selected_months = $row["month_mfo"];
  $selected_year = $row["year_mfo"];

  // get previous period data
  $period_id = 0;
  $months = "";
  $year = "";
  if ($selected_months == "July - December") {
    $months = "January - June";
    $year = $selected_year;
  } else {
    $months = "July - December";
    $year = $selected_year - 1;
  }
  $sql = "SELECT `mfoperiod_id` FROM `spms_mfo_period` WHERE `month_mfo` = '$months' AND `year_mfo` = '$year'";
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  $period_id = $row["mfoperiod_id"];

  $department_id = $_SESSION["emp_info"]["department_id"];

  // get previous period core functions
  $data = [];
  $sql = "SELECT * FROM `spms_corefunctions` WHERE `mfo_periodId` = '$period_id' AND `dep_id` = '$department_id' AND `parent_id` = '';";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
    // $data[] = [
    //   "core_function_data" => $row,
    //   "success_indicators" => get_success_indicators($mysqli, $row["cf_ID"])
    // ];
    $row["children"] = get_children($mysqli, $row['cf_ID']);
    $data[] = $row;
  }

  $data = start_duplicating($mysqli, $data, $selected_period_id, "");

  /*
  spms_corefunctions
  cf_ID: "9801"
  cf_count: "01."
  cf_title: "Recruitment Services"
  corrections: "a:1:{i:0;a:2:{i:0;s:64:\"<b>VILLARIN, MA. RAYZA</b> - <i>2022-07-15 16:12:15</i>:<br>test\";i:1;i:1;}}"
  dep_id: "32"
  mfo_periodId: "2"
  parent_id: "9816"

  spms_matrixindicators
  cf_ID: "9802"
  corrections: ""
  mi_eff: "a:6:{i:0;s:0:\"\";i:1;s:0:\"\";i:2;s:20:\"No meeting conducted\";i:3;s:0:\"\";i:4;s:0:\"\";i:5;s:22:\"With meeting conducted\";}"
  mi_id: "10123"
  mi_incharge: "21805"
  mi_quality: "a:6:{i:0;s:0:\"\";i:1;s:0:\"\";i:2;s:0:\"\";i:3;s:0:\"\";i:4;s:0:\"\";i:5;s:0:\"\";}"
  mi_succIn: "100% of PMT meetings / sessions conducted\n          "
  mi_time: "a:6:{i:0;s:0:\"\";i:1;s:0:\"\";i:2;s:0:\"\";i:3;s:0:\"\";i:4;s:0:\"\";i:5;s:0:\"\";}"
*/

  // foreach ($data as $i => $datum) {
  // copy to spms_corefunctions
  // $sql = "INSERT INTO `spms_corefunctions`(`mfo_periodId`, `parent_id`, `dep_id`, `cf_count`, `cf_title`, `corrections`) VALUES ('$selected_period_id','$datum[parent_id]','$datum[dep_id]','$datum[cf_count]','$datum[cf_title]','')";
  // $data[$i]['core_function_data']['insert_id'] = '9';
  // }

  echo json_encode(true);

  // echo json_encode($_SESSION["emp_info"]["department_id"]);
} elseif (isset($_POST['copy_to'])) {
  # code...
  $curr_period_id = 10;
  $curr_department_id = 1;
  $parent_id = 14776;

  $selected_period_id = 11;
  $selected_parent_id = 22217;
  $selected_department_id = 3;

  $data = [];
  $sql = "SELECT * FROM `spms_corefunctions` WHERE `mfo_periodId` = '$curr_period_id' AND `dep_id` = '$curr_department_id' AND `parent_id` = '$parent_id';";

  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
    // $data[] = [
    //   "core_function_data" => $row,
    //   "success_indicators" => get_success_indicators($mysqli, $row["cf_ID"])
    // ];
    $row["children"] = get_children($mysqli, $row['cf_ID']);
    $data[] = $row;
  }

  $data = start_duplicating($mysqli, $data, $selected_period_id, $selected_parent_id, $selected_department_id);

  print json_encode($data);
}
// copy previous rsm end
elseif (isset($_POST['period_check'])) {

  $period = "";
  $statusOK = 0;

  $sql = "SELECT * from spms_mfo_period where month_mfo='$_POST[period_check]' and year_mfo='$_POST[year]'";
  $sql = $mysqli->query($sql);

  if (!$sql) {
    die($mysqli->error);
  }

  if ($sql->num_rows) {
    $sqlFetch = $sql->fetch_assoc();
    $period = $sqlFetch['mfoperiod_id'];
    $_SESSION['period'] = $sqlFetch['mfoperiod_id'];
    $statusOK = 1;
  } else {
    $sql = "INSERT INTO `spms_mfo_period` (`mfoperiod_id`, `month_mfo`, `year_mfo`) VALUES (NULL,'$_POST[period_check]','$_POST[year]')";
    $sql = $mysqli->query($sql);
    $statusOK = 1;
    $period = $mysqli->insert_id;
    $_SESSION['period'] = $mysqli->insert_id;
  }
  if ($statusOK) {
    $department = $user->get_emp('department_id');
    $rsmStatus = "SELECT * from `spms_rsmstatus` where `period_id`='$period' and `department_id`='$department'";
    $rsmStatus = $mysqli->query($rsmStatus);
    if ($rsmStatus->num_rows < 1) {
      // if this period will end edit will change to zero;
      $rsm = "INSERT INTO `spms_rsmstatus` (`rsmStatus_id`, `period_id`, `department_id`, `done`, `edit`, `alter_logs`) VALUES (NULL, '$period', '$department', '0', '1', '')";
      $rsm = $mysqli->query($rsm);
      if (!$rsm) {
        die($mysqli->error);
      }
    }
  }
  echo $statusOK;
} elseif (isset($_POST['page'])) {
  $page = $_POST['page'];
  if ($page == 'table') {
    echo table($mysqli);
    if (rsmEditStatus("")) {
      echo "<button class='ui primary button fluid' onclick='closeRsm(" . rsmEditStatus("id") . ")'>Submit Rating Scale Matrix</button>";
    }
  } elseif (false) {
  }
} elseif (isset($_POST['addRSMData'])) {
  $rsmCount = changeCount($_POST['rsmCount']);
  $rsmCount = addslashes($rsmCount);
  $addRSMData = addslashes($_POST['addRSMData']);
  if ($rsmCount != "" && $addRSMData != "") {
    $dep_id = $_SESSION['emp_info']['department_id'];
    $pid = $_POST['pid'];
    $sql = "INSERT INTO `spms_corefunctions` (`cf_ID`,`mfo_periodId`, `parent_id`, `dep_id`, `cf_count`, `cf_title`) VALUES ('','$_SESSION[period]', '$pid','$dep_id', '$rsmCount', '$addRSMData')";
    $sql = $mysqli->query($sql);
    if (!$sql) {
      die($mysqli->error);
    } else {
      print(1);
    }
  } else {
    echo "Some important input fields are Empty";
  }
} elseif (isset($_POST['editRsmTitle'])) {
  $editRsmTitle = $_POST['editRsmTitle'];
  $editcountRsm = changeCount($_POST['editcountRsm']);
  $dataId = $_POST['dataId'];
  $getC = "SELECT * from `spms_corefunctions` where `cf_ID`=$dataId";
  $getC = $mysqli->query($getC);
  $getC = $getC->fetch_assoc();
  $update_correction = "";
  if ($getC['corrections']) {
    $update_correction = [];
    $getC = unserialize($getC['corrections']);
    $count = 0;
    while ($count < count($getC)) {
      $update_correction[] = [$getC[$count][0], 1];
      $count++;
    }
    $update_correction = serialize($update_correction);
  }

  $editRsmTitle = $mysqli->real_escape_string($editRsmTitle);
  $update_correction = $mysqli->real_escape_string($update_correction);
  $sql = "UPDATE `spms_corefunctions` SET `cf_count` = '$editcountRsm', `cf_title` = '$editRsmTitle',`corrections`='$update_correction' WHERE `spms_corefunctions`.`cf_ID` = '$dataId'";
  $sql = $mysqli->query($sql);
  if (!$sql) {
    die($mysqli->error);
  } else {
    print(1);
  }
} elseif (isset($_POST['MfoSiDelete'])) {
  $dataId = $_POST['MfoSiDelete'];
  $sql = "DELETE FROM `spms_corefunctions` WHERE `spms_corefunctions`.`cf_ID` ='$dataId'";
  $sql = $mysqli->query($sql);
  if (!$sql) {
    die($mysqli->error);
  } else {
    print(1);
  }
} elseif (isset($_POST['SaveMfoSI'])) {
  $dataId = $_POST['SaveMfoSI'];
  $quality = $mysqli->real_escape_string(serialize($_POST['quality']));
  $efficiency = $mysqli->real_escape_string(serialize($_POST['efficiency']));
  $timeliness = $mysqli->real_escape_string(serialize($_POST['timeliness']));
  $successIn = $mysqli->real_escape_string($_POST['successIn']);
  $incharge = $mysqli->real_escape_string($_POST['incharge']);
  $sql = "INSERT INTO `spms_matrixindicators`
  (`mi_id`, `cf_ID`, `mi_succIn`, `mi_quality`, `mi_eff`, `mi_time`, `mi_incharge`)
  VALUES
  (NULL, '$dataId', '$successIn', '$quality', '$efficiency', '$timeliness', '$incharge')";
  $sql = $mysqli->query($sql);
  if (!$sql) {
    die($mysqli->error);
  } else {
    print(1);
  }
} elseif (isset($_POST['SaveMfoSIEdit'])) {
  $dataId = $_POST['SaveMfoSIEdit'];
  $quality = $mysqli->real_escape_string(serialize($_POST['quality']));
  $efficiency = $mysqli->real_escape_string(serialize($_POST['efficiency']));
  $timeliness = $mysqli->real_escape_string(serialize($_POST['timeliness']));
  $successIn = $mysqli->real_escape_string($_POST['successIn']);
  $incharge = $mysqli->real_escape_string($_POST['incharge']);
  $getC = "SELECT * from `spms_matrixindicators` where `mi_id`=$dataId";
  $getC = $mysqli->query($getC);
  $getC = $getC->fetch_assoc();
  $update_correction = "";
  if ($getC['corrections']) {
    $update_correction = [];
    $getC = unserialize($getC['corrections']);
    $count = 0;
    while ($count < count($getC)) {
      $update_correction[] = [$getC[$count][0], 1];
      $count++;
    }
    $update_correction = serialize($update_correction);
  }
  $update_correction = $mysqli->real_escape_string($update_correction);
  $sql = "UPDATE `spms_matrixindicators` SET
  `mi_succIn` = '$successIn',
  `mi_quality` = '$quality',
  `mi_eff` = '$efficiency',
  `mi_time` = '$timeliness',
  `mi_incharge` = '$incharge',
  `corrections` = '$update_correction'
  WHERE `spms_matrixindicators`.`mi_id` = $dataId;
  ";
  $sql = $mysqli->query($sql);
  if (!$sql) {
    die($mysqli->error);
  } else {
    print(1);
  }
} elseif (isset($_POST['removeSi'])) {
  $sql = "DELETE FROM `spms_matrixindicators` WHERE `spms_matrixindicators`.`mi_id` = '$_POST[removeSi]'";
  $sql = $mysqli->query($sql);
  if (!$sql) {
    die($mysqli->error);
  } else {
    print(1);
  }
} elseif (isset($_POST['closeRsm'])) {
  $sql = "UPDATE `spms_rsmstatus` SET `edit` = '0' , `done`='1' WHERE `spms_rsmstatus`.`rsmStatus_id` = '$_POST[closeRsm]'";
  $sql = $mysqli->query($sql);
  if (!$sql) {
    die($mysqli->error);
  } else {
    echo 1;
  }
} elseif (isset($_POST['getRsmparentChange'])) {
  $rsmMFO = new RsmClass($host, $usernameDb, $password, $database);
  $rsmMFO->set_period($_SESSION["period"]);
  $rsmMFO->set_department($_POST['dept']);
  $rsmMFO->set_mfoID($_POST['getRsmparentChange']);
  echo "
      <div class='ui icon input fluid'>
        <i class='search icon'></i>
        <input type='text' placeholder='Search MFO...' onkeyup='mfoSearchTable(this)'>
      </div>
      <br>
      <br>
      <button class='ui primary button fluid' onclick=changeParent($_POST[getRsmparentChange],'')>
        Make this a parent
      </button>
  ";
  echo "<table class='ui selectable celled table'>
        <thead>
        <tr>
        <th>MFO</th>
        <th>Option</th>
        </tr>
        <thead>
        <tbody id='mfoChangeBody'>
        " . $rsmMFO->get_view() . "
        </tbody>
        </table>";
} elseif (isset($_POST['changeParent'])) {
  $sub = $_POST['sub'];
  $parent = $_POST['parent'];
  $sql  = "UPDATE `spms_corefunctions` SET `parent_id` = '$parent' WHERE `spms_corefunctions`.`cf_ID` = $sub";
  $sql = $mysqli->query($sql);
  echo 1;
} elseif (false) {
} else {
  echo notFound();
}

function getPreviousPeriodId($mysqli)
{

  $previous_period_id = null;
  $period_id = $_SESSION["period"];

  # get previous period_id start

  $selected_months = '';
  $selected_year = '';

  $sql = "SELECT * FROM `spms_mfo_period` WHERE `mfoperiod_id` = '$period_id';";
  $result = $mysqli->query($sql);
  if ($row = $result->fetch_assoc()) {
    $selected_months = $row["month_mfo"];
    $selected_year = $row["year_mfo"];
  }

  $months = "";
  $year = "";

  if ($selected_months == "July - December") {
    $months = "January - June";
    $year = $selected_year;
  } else {
    $months = "July - December";
    $year = $selected_year - 1;
  }

  $sql = "SELECT * FROM `spms_mfo_period` WHERE `month_mfo` = '$months' AND `year_mfo` = '$year';";
  $result = $mysqli->query($sql);
  if ($row = $result->fetch_assoc()) {
    $previous_period_id = $row['mfoperiod_id'];
  }
  # get previous period_id end

  return $previous_period_id;
  // echo json_encode($previous_period_id);
}

function table($mysqli)
{


  # $dep = $_SESSION['emp_info']['department_id']; 
  # get department_id from formstatus of selected period
  # return $_SESSION['emp_info']['department_id'] if no formstatus exists
  $employee_id = $_SESSION['emp_info']['employees_id'];
  $period_id = $_SESSION['period'];

  $sql = "SELECT * FROM `spms_performancereviewstatus` WHERE `employees_id` = '$employee_id' AND `period_id` = '$period_id'";
  $res = $mysqli->query($sql);
  if ($row = $res->fetch_assoc()) {
    $department_id = $row['department_id'];
  } else {
    $department_id = $_SESSION['emp_info']['department_id'];
  }

  $dep = "SELECT * from `department` where department_id='$department_id'";
  $dep = $mysqli->query($dep);
  $dep = $dep->fetch_assoc();
  $dep = $dep['department'];


  $period = "SELECT * from `spms_mfo_period` where `mfoperiod_id`='$period_id'";
  $period = $mysqli->query($period);
  $period = $period->fetch_assoc();
  // $period_id = $period['mfoperiod_id'];
  echo "
  <button class='noprint' onclick = 'rsmLoad(\"table\")'>Refresh</button>
  <script>
  $('.ui.dropdown').dropdown({
    fullTextSearch:true
  });
  </script>
  <table class='tablepr' border='1px' style='border-collapse:collapse;width:100%;font-size:13px'>
  <thead>
  <tr class='noprint'>
  <th colspan='8' style='font-size:20px'>
  Rating Scale Matrix
  <br>$dep
  <br>$period[month_mfo] $period[year_mfo]
  </th>
  <tr>
  <tr>
  <th rowspan='2'>MFO/PAP</th>
  <th rowspan='2'>SUCCESS Indicator</th>
  <th rowspan='2'>Performance Measure</th>
  <th colspan='3'>Rating</th>
  <th rowspan='2'>IN-CHARGE</th>
  <th rowspan='2' class='noprint'>Options</th>
  </tr>
  <tr>
  <th>Q</th>
  <th>E</th>
  <th>T</th>
  </tr>
  </thead>
  <tbody>" . tbody($mysqli) . "
  </tbody>
  </table>
  ";
}
function tbody($mysqli)
{
  $view = "";

  # $dep_id = $_SESSION['emp_info']['department_id'];

  $employee_id = $_SESSION['emp_info']['employees_id'];
  $period_id = $_SESSION['period'];

  $sql = "SELECT * FROM `spms_performancereviewstatus` WHERE `employees_id` = '$employee_id' AND `period_id` = '$period_id'";
  $res = $mysqli->query($sql);
  if ($row = $res->fetch_assoc()) {
    $dep_id = $row['department_id'];
  } else {
    $dep_id = $_SESSION['emp_info']['department_id'];
  }

  $sql = "SELECT * from spms_corefunctions where parent_id='' and mfo_periodId='$period_id' and dep_id='$dep_id' ORDER BY `spms_corefunctions`.`cf_count` ASC ";
  $sql = $mysqli->query($sql);
  $tr = "";
  while ($row1 = $sql->fetch_assoc()) {
    $view .= trows($mysqli, $row1, '10px', '');
    $view .= tbodyChild($row1['cf_ID'], 10);
  }
  $view .= "<tr class='noprint' >
  <td colspan='8' style='padding:10px'>
  " . AddInputs($mysqli, '') . "
  </td>
  </tr>";
  return $view;
}

function tbodyChild($dataId, $padding)
{
  $view = "";
  $mysqli = $GLOBALS['mysqli'];
  $sql2 = "SELECT * from spms_corefunctions where parent_id='$dataId' ORDER BY `spms_corefunctions`.`cf_count` ASC";
  $sql2 = $mysqli->query($sql2);
  $padding += 15;
  while ($row2 = $sql2->fetch_assoc()) {
    $sql3 = "SELECT * from spms_corefunctions where parent_id='$row2[cf_ID]' ORDER BY `spms_corefunctions`.`cf_count` ASC";
    $sql3 = $mysqli->query($sql3);
    $pad = $padding . "px";
    $view .= trows($mysqli, $row2, $pad, '');
    $view .= tbodyChild($row2['cf_ID'], $padding);
  }
  return $view;
}

function editInputs($dataId, $count, $title)
{
  $view = "
  <div class=' field' >
  <div class='ui right labeled input' >
  <textarea  type='text' style='width:50px;height:50px' id='EditcountRsm$dataId'>$count</textarea>
  <textarea  type='text' style='width:250px;height:50px'  id='EdittitleRsm$dataId'>$title</textarea>
  <div class='mini green ui basic icon button' onclick='EditRsmTitle($dataId)'><i class='edit icon'></i></div>
  </div>
  </div>";
  return $view;
}
function unserData($ser_arr)
{
  $count = 5;
  $data = "";
  $arr = unserialize($ser_arr);
  while ($count >= 1) {
    if ($arr[$count]) {
      $data .= "<b>" . $count . "</b> - " . $arr[$count] . "<br>";
    }
    $count--;
  }



  // foreach ($arr as $unser) {
  //   if($unser!=""){
  //     $data.=$count." - ". $unser."<br>";
  //   }
  //   $count++;
  // }
  return $data;
}

function validaateCorrection($dat)
{
  $color = false;
  if ($dat) {
    $count = 0;
    $dat = unserialize($dat);
    while ($count < count($dat)) {
      if ($dat[$count][1] == 0) {
        $color = true;
        break;
      }
      $count++;
    }
  }
  return $color;
}

function trows($mysqli, $row, $padding, $addDisplay)
{
  $sql2 = "SELECT * from spms_corefunctions where parent_id='$row[cf_ID]'";
  $sql2 = $mysqli->query($sql2);
  $sql2count = $sql2->num_rows;
  if ($sql2count > 0) {
    $set_drop = settingDrop($mysqli, $row, '', $addDisplay, 'display:none');
  } else {
    $set_drop = settingDrop($mysqli, $row, '', $addDisplay, '');
  }
  $view = "";
  $siData1 = "SELECT * from spms_matrixindicators where cf_ID='$row[cf_ID]'";
  $siData1 = $mysqli->query($siData1);
  $siDatacount1 = $siData1->num_rows;
  $count = 1;
  $correctionColorMFO = "";
  $correctionMFO = validaateCorrection($row['corrections']);
  if ($correctionMFO) {
    $correctionColorMFO = "color:red;";
  }

  if ($siDatacount1 > 0) {
    while ($siDataRow1 = $siData1->fetch_assoc()) {
      // $mi_id = $siDataRow1['mi_id'];
      $correctionColor = "";
      $correction = validaateCorrection($siDataRow1['corrections']);
      if ($correction) {
        $correctionColor = "color:red;";
      }
      $empincharge = "";
      $incharge = explode(',', $siDataRow1['mi_incharge']);
      #iterate employees
      foreach ($incharge as $empDataId) {
        if (!$empDataId || $empDataId == null) {
          continue;
        }
        $sqlIncharge = "SELECT * from employees where employees_id='$empDataId'";
        $sqlIncharge = $mysqli->query($sqlIncharge);
        $sqlIncharge = $sqlIncharge->fetch_assoc();
        $empincharge .= "<br><a onclick='ShowIPcrModal(\"$sqlIncharge[employees_id]\")' style='cursor:pointer;'>$sqlIncharge[firstName] $sqlIncharge[lastName]</a><br>";
        // if (isset($siDataRow1['mi_id'])) {
        //   $mi_id = $siDataRow1['mi_id'];
        //   $sql = "SELECT * FROM `spms_corefucndata`where `p_id` = '$mi_id' AND `empId` = '$empDataId';";
        //   $res = $mysqli->query($sql);
        //   if ($rowdata = $res->fetch_assoc()) {
        //     // $empincharge .= " -- " . json_encode($rowdata);
        //     # Rehabilitation Leave Benefits
        //     if ($rowdata['disable'] != 1) {
        //       # code...
        //       $score = 0;
        //       $q = "";
        //       $e = "";
        //       $t = "";
        //       $count_scales = 0;
        //       if ($rowdata['Q']) {
        //         $score += $rowdata['Q'];
        //         $q = $rowdata['Q'];
        //         $count_scales++;
        //       }
        //       if ($rowdata['E']) {
        //         $score += $rowdata['E'];
        //         $e = $rowdata['E'];
        //         $count_scales++;
        //       }
        //       if ($rowdata['T']) {
        //         $score += $rowdata['T'];
        //         $t = $rowdata['T'];
        //         $count_scales++;
        //       }

        //       // $empincharge .= "<br/>";
        //       $score = bcdiv($score, $count_scales, 1);
        //       $score = explode(".", $score);
        //       if ($score[1] == 0) {
        //         $score = $score[0];
        //       } else {
        //         $score = implode(".", $score);
        //       }
        //       $final_mfo_rating = $score . "/5";

        //       $empincharge .= "<table class='ui mini compact structured celled table'>
        //         <thead>
        //             <tr style='text-align: left;'>
        //               <th colspan='4'><a onclick='ShowIPcrModal(\"$sqlIncharge[employees_id]\")' style='cursor:pointer;'>$sqlIncharge[firstName] $sqlIncharge[lastName]</a></th>
        //             </tr>
        //             <tr style='text-align: center;'>
        //               <th>Q</th>
        //               <th>E</th>
        //               <th>T</th>
        //               <th>FINAL</th>
        //             </tr>
        //         </thead>
        //         <tbody>
        //             <tr style='text-align: center;'>
        //               <td>$q</td>
        //               <td>$e</td>
        //               <td>$t</td>
        //               <td>$final_mfo_rating</td>
        //             </tr>
        //         </tbody>
        //       </table>";
        //       // $empincharge .= $final_mfo_rating;
        //     } #else not applicable
        //     else {
        //       $empincharge .= "<a onclick='ShowIPcrModal(\"$sqlIncharge[employees_id]\")' style='cursor:pointer;'>$sqlIncharge[firstName] $sqlIncharge[lastName]</a><br/>";
        //       $empincharge .= "N/A (" . $rowdata['remarks'] . ")";
        //     }
        //   } else {
        //     $empincharge .= "<a onclick='ShowIPcrModal(\"$sqlIncharge[employees_id]\")' style='cursor:pointer;'>$sqlIncharge[firstName] $sqlIncharge[lastName]</a><br/>";
        //     $empincharge .= "NOT ACCOMPLISHED";
        //   }
        // }
      }
      $Qdata = "";
      $Edata = "";
      $Tdata = "";
      $performanceMeasure = "";
      if (unserData($siDataRow1['mi_quality']) != "") {
        $performanceMeasure .= "Quality<br>";
      }
      if (unserData($siDataRow1['mi_eff']) != "") {
        $performanceMeasure .= "Efficiency<br>";
      }
      if (unserData($siDataRow1['mi_time']) != "") {
        $performanceMeasure .= "Timeliness<br>";
      }
      if ($count == 1) {
        $view .= "
        <tr >
        <td style='padding-left:$padding;width:25%;$correctionColorMFO'>
        " . $set_drop . "
        $row[cf_count]) $row[cf_title] " .  ""/*json_encode($row)*/ . "
        </td>
        <td style='width:25%;$correctionColor'>" . nl2br($siDataRow1['mi_succIn']) . ""/*json_encode($siDataRow1)*/ . "</td>
        <td>$performanceMeasure</td>
        <td style='width:150px;padding-bottom:10px;$correctionColor'>" . unserData($siDataRow1['mi_quality']) . "</td>
        <td style='width:150px;padding-bottom:10px;$correctionColor'>" . unserData($siDataRow1['mi_eff']) . "</td>
        <td style='width:150px;padding-bottom:10px;$correctionColor'>" . unserData($siDataRow1['mi_time']) . "</td>
        <td>$empincharge</td>
        <td class='noprint' style='width:100px;padding:5px'>
        ";
        if (rsmEditStatus("") || $correction) {
          $view .= "
            <button class='ui green icon basic button' onclick='siEditOpenModal($siDataRow1[mi_id])'><i class='edit icon' ></i></button>
            <button class='ui red icon basic button' onclick='deleteOpenModal($siDataRow1[mi_id])'><i class='trash icon'></i></button>
            ";
        }
        $view .= "
        </td>
        </tr>
        ";
      } else {
        $view .= "
        <tr >
        <td></td>
        <td style='width:25%;$correctionColor'>" . nl2br($siDataRow1['mi_succIn']) . "</td>
        <td>$performanceMeasure</td>
        <td style='width:150px;padding-bottom:10px;$correctionColor'>" . unserData($siDataRow1['mi_quality']) . "</td>
        <td style='width:150px;padding-bottom:10px;$correctionColor'>" . unserData($siDataRow1['mi_eff']) . "</td>
        <td style='width:150px;padding-bottom:10px;$correctionColor'>" . unserData($siDataRow1['mi_time']) . "</td>
        <td>$empincharge</td>
        <td class='noprint' style='width:100px;padding:5px'>
        ";
        if (rsmEditStatus("") || $correction) {
          $view .= "<button class='ui green icon basic button' onclick='siEditOpenModal($siDataRow1[mi_id])'><i class='edit icon' ></i></button>
          <button class='ui red icon basic button' onclick='deleteOpenModal($siDataRow1[mi_id])'><i class='trash icon'></i></button>";
        }
        $view .= "
        </td>
        </tr>
        ";
      }
      $count++;
    }
  } else {
    $view .= "
    <tr >
    <td style='padding-left:$padding;width:500px;$correctionColorMFO'>
    " . $set_drop . "
    $row[cf_count]) $row[cf_title] " . ""/*json_encode($row)*/ . "
    </td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td class='noprint'></td>
    </tr>
    ";
  }
  return $view;
}
function rsmEditStatus($dat)
{
  $mysqli = $GLOBALS['mysqli'];
  $department_id = $GLOBALS['user'];
  $department_id = $department_id->get_emp('department_id');
  $period = $_SESSION['period'];
  $enable = false;

  $sql = "SELECT * from `spms_rsmstatus` where `period_id`='$period' and `department_id`='$department_id'";
  $sql = $mysqli->query($sql);
  $sql = $sql->fetch_assoc();
  if ($sql['edit']) {
    $enable = true;
  }
  if ($dat == "id") {
    return $sql['rsmStatus_id'];
  } else {
    return $enable;
  }
}


function AddInputs($mysqli, $dataId)
{

  # check first if rating scale matrix has already existing data
  # also check if previous rsm exist for copying
  // period
  $view = "";
  $period_id = $_SESSION["period"];
  $employee_id = $_SESSION['emp_info']['employees_id'];

  $sql = "SELECT * FROM `spms_performancereviewstatus` WHERE `employees_id` = '$employee_id' AND `period_id` = '$period_id'";
  $res = $mysqli->query($sql);
  if ($row = $res->fetch_assoc()) {
    $department_id = $row['department_id'];
  } else {
    $department_id = $_SESSION['emp_info']['department_id'];
  }


  $curr_rsm_exists = false;
  $prev_rsm_exists = false;
  $previous_period_id = getPreviousPeriodId($mysqli);
  $sql = "SELECT * FROM `spms_corefunctions` WHERE `mfo_periodId` = '$period_id' AND `dep_id` = '$department_id' LIMIT 1;";
  $result = $mysqli->query($sql);
  if ($result->num_rows > 0) {
    $curr_rsm_exists = true;
  }

  $sql = "SELECT * FROM `spms_corefunctions` WHERE `mfo_periodId` = '$previous_period_id' AND `dep_id` = '$department_id' LIMIT 1;";
  $result = $mysqli->query($sql);
  if ($result->num_rows > 0) {
    $prev_rsm_exists = true;
  }

  if (!$curr_rsm_exists && $prev_rsm_exists) {
    $view .= "<button class='ui green large button' onclick='copyRSM()'>Copy Previous RSM</button>";
  } else {
    $view = "
    <div class='ui mini form'>
    <div class='fields'>
    <input type='hidden' value='$dataId' id='mfo_pid$dataId'>
    <div class='field'>
    <label>Category. No.</label>
    <input type='text' style='width:90px' placeholder='ex: I,II,1,1.0,1.1.0' id='rsmcount$dataId'>
    </div>
    <div class=' field' >
    <label>Title</label>
    <div class='ui right labeled input'>
    <input type='text' style='width:200px' placeholder='Type Here.....' id='titleRsm$dataId'>
    <div class='mini ui primary basic icon button' onclick='addMFoRsm(\"$dataId\")'><i class='save icon'></i></div>
    </div>
    </div>
    </div>
    </div>";
  }
  if (!rsmEditStatus("")) {
    $view = "";
  }
  return $view;
}
function settingDrop($mysqli, $row, $edit, $add, $delete)
{
  $correction = "";
  if ($row['corrections']) {
    $c = unserialize($row['corrections']);
    $count = 0;
    $crt = "";
    while ($count < count($c)) {
      $state = "<b style='color:red'>Unaccomplished</b>";
      if ($c[$count][1]) {
        $state = "<b style='color:green'>Accomplished</b>";
      }
      $crt .= $c[$count][0] . " - $state <br>";
      $count++;
    }
    $correction = "
    <div class='header'>
    <p class='ui horizontal divider'>
    <i class='indent icon'></i>
    <span style='font-size:10px'>Corrections</span>
    </p>
    </div>
    <div class='header'>
      $crt
    </div>
    ";
  }
  $view = "
  <div class='mini ui left pointing dropdown icon noprint'>
  <i class='green settings icon'></i>
  <div class='menu'>
  <div class='header'>
  <i class='tags icon'></i>
  Actions
  </div>
  $correction
  <div class='header' style='$edit'>
  <p class='ui horizontal divider'>
  <i class='green edit icon'></i>
  Edit
  </p>
  </div>
  <div class='header' style='$edit'>
  " . editInputs($row['cf_ID'], $row['cf_count'], $row['cf_title']) . "
  </div>
  <div class='header'>
  <p class='ui horizontal divider'>
  <i class='green tasks icon'></i>
  <span style='font-size:10px'>Success Indicators & Rating Matrix</span>
  </p>
  </div>
  <div class='header'>
  <button class='mini ui fluid primary button' onclick='ShowModalSiAdd($row[cf_ID])'><i class='tasks icon'></i> Indicators</button>
  </div>
  <div class='header'>
  <p class='ui horizontal divider'>
  <i class='indent icon'></i>
  <span style='font-size:10px'>Change Parent</span>
  </p>
  </div>
  <div class='header'>
  <button class='mini ui fluid black button' onclick='ShowMfoList($row[cf_ID],$row[dep_id])'><i class='indent icon'></i>Change Mfo Parent</button>
  </div>
  <div class='header' style='$add'>
  <p class='ui horizontal divider'>
  <i class='blue add icon'></i>
  Add Sub-Function
  </p>
  <button onclick='copyToRSM()' style='display:none;'>Delete All (exec copy to RSM)</button>
  </div>
  <div class='header' style='$add'>
  " . AddInputs($mysqli, $row['cf_ID']) . "
  </div>
  <div class='header' style='$delete'>
  <p class='ui horizontal divider'>
  <i class='red Trash icon'></i>
  Delete
  </p>
  </div>
  <div class='header' style='$delete'>
  <button class='mini ui negative fluid button' onclick='MfoSiDelete($row[cf_ID])'><i class='trash icon'></i> Remove</button>
  </div>
  <div class='item' style='display:none'>
  </div>
  <br>
  </div>
  </div>
  ";
  $correctionMFO = validaateCorrection($row['corrections']);
  if (!rsmEditStatus("") && !$correctionMFO) {
    $view = "";
  }
  return $view;
}
function changeCount($dat)
{
  $dat = str_replace(")", "", $dat);
  $dat = explode(".", $dat);
  $d = "";
  foreach ($dat as $a) {
    $a = str_replace(' ', '', $a);
    if ($a) {
      if (is_numeric($a)) {
        if ($a < 10 && strlen($a) == 1) {
          $d .= "0" . $a . ".";
        } else {
          $d .= $a . ".";
        }
      } else {
        $d .= $a . ".";
      }
    }
  }
  return $d;
}

function get_children($mysqli, $cf_ID)
{
  $data = [];
  $sql = "SELECT * FROM `spms_corefunctions` WHERE `parent_id` ='$cf_ID'";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
    $row["children"] = get_children($mysqli, $row["cf_ID"]);
    $data[] = $row;
  }
  return $data;
}

function start_duplicating($mysqli, $data, $selected_period_id, $parent_id, $department_id = null)
{
  // $department_id = 3;
  foreach ($data as $key => $core_function) {
    // $department_id = $core_function['dep_id'];
    if (!$department_id) {
      $department_id = $core_function['dep_id'];
    }
    $parent_id = $parent_id ? $parent_id : NULL;
    $cf_title = $mysqli->real_escape_string($core_function['cf_title']);
    $cf_count = $mysqli->real_escape_string($core_function['cf_count']);
    $sql = "INSERT INTO `spms_corefunctions`(`mfo_periodId`, `parent_id`, `dep_id`, `cf_count`, `cf_title`, `corrections`) VALUES ('$selected_period_id','$parent_id','$department_id','$cf_count','$cf_title','')";
    $mysqli->query($sql);
    $insert_id = $mysqli->insert_id;

    #get success indicators
    $success_idicators = get_success_indicators($mysqli, $core_function["cf_ID"]);
    foreach ($success_idicators as $success_idicator) {

      $mi_succIn = $mysqli->real_escape_string($success_idicator['mi_succIn']);

      $mi_quality = $mysqli->real_escape_string($success_idicator['mi_quality']);
      $mi_eff = $mysqli->real_escape_string($success_idicator['mi_eff']);
      $mi_time = $mysqli->real_escape_string($success_idicator['mi_time']);

      $sql = "INSERT INTO `spms_matrixindicators`(`cf_ID`, `mi_succIn`, `mi_quality`, `mi_eff`, `mi_time`, `mi_incharge`, `corrections`) VALUES ('$insert_id','$mi_succIn','$mi_quality','$mi_eff','$mi_time','$success_idicator[mi_incharge]','')";
      $mysqli->query($sql);
    }

    $data[$key]["children"] = start_duplicating($mysqli, $core_function["children"], $selected_period_id, $insert_id);
  }

  return $data;
}

function get_success_indicators($mysqli, $cf_ID)
{
  $data = [];
  $sql = "SELECT * FROM `spms_matrixindicators` WHERE `cf_ID` = '$cf_ID'";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
    $data[] = $row;
  }
  return $data;
}
