<?php
if ($user->authorization) {
  for ($index = 0; $index <= count($user->authorization); $index++) {
    if ($index == count($user->authorization)) {
      echo Authorization_Error();
    } else if (strtoupper($user->authorization[$index]) == strtoupper("reviewer")) {
?>
      <center>
        <h2 class="ui header noprint">
          <i class="ui book icon massive"></i>
          <div class="content">
            Performance Commitment Report
            <div class="sub header">Uncriticized Performance Commitment</div>
          </div>
        </h2>
        <br class='noprint'>
        <br class='noprint'>
        <div id="Reviewcontent">
          <div style="width:50%;margin:auto">
            <?= gridNotifView() ?>
          </div>
        </div>
      </center>
<?php
      break;
    }
  }
} else {
  echo  Authorization_Error();
}


function gridNotifView()
{
  global $mysqli;
  $empId = $_SESSION['emp_id'];
  $getAllPeriod = "SELECT * from `spms_mfo_period`";
  $getAllPeriod = $mysqli->query($getAllPeriod);
  $department_id = $_SESSION['emp_info']['department_id'];

  $view = "";
  while ($period = $getAllPeriod->fetch_assoc()) {
    $DepartmentHeadDataCount = 0;
    $count = 0;
    $periodId = $period['mfoperiod_id'];
    $ImmediateSupData = "SELECT * from `spms_performancereviewstatus` where `submitted`= 'Done' and `period_id`= '$period[mfoperiod_id]' and `ImmediateSup`='$empId' and `approved`=''";
    $ImmediateSupData = $mysqli->query($ImmediateSupData);
    $imCount = 0;
    $test = [];
    while ($im = $ImmediateSupData->fetch_assoc()) {
      if ($im['ImmediateSup'] != $im['DepartmentHead']) {
        $imCount++;
      }
    }

    $ImmediateSupData = $imCount;
    // $ImmediateSupData = $ImmediateSupData->num_rows;

    $DepartmentHeadData = "SELECT * from `spms_performancereviewstatus` where `submitted`= 'Done' and `period_id`= '$period[mfoperiod_id]' and `DepartmentHead`='$empId' and `certify`=''";
    $DepartmentHeadData = $mysqli->query($DepartmentHeadData);



    $DepartmentHeadDataCount = $DepartmentHeadData->num_rows;

    while ($row = $DepartmentHeadData->fetch_assoc()) {
      $test[] = $row;
    }

    $test = $department_id;

    $test = json_encode($test);

    $count =  $ImmediateSupData + $DepartmentHeadDataCount;
    if ($count > 0) {
      $view .= "
      <div class='ui  raised segment ' style='cursor:pointer' onclick='unrevRec(\"$period[mfoperiod_id]\")' >
        <div class='floating ui red label'>$count</div>
        <span style='font-size:20px' >$period[month_mfo] $period[year_mfo]</span>
        </div>";
    }
  }
  if (!$view) {
    $view = "<p style='text-align:center;font-size:20px;font-weight:bolder;color:#00000047'>
        <i class='ui massive exclamation icon'></i>
        <br class='noprint'>
        <br class='noprint'>
        No pending Records Needs to be reviewed</p>";
  }
  $view .= $mysqli->error;
  return $view;
}
?>