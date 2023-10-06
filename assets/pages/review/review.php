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
            <div class="sub header">View Section/Department Performance Commitment Reports</div>
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
  $periods = [];

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


    ### testing start 
    $view .= "
      <div class='ui  raised segment ' style='cursor:pointer' onclick='unrevRec(\"$period[mfoperiod_id]\")' >
        <div class='floating ui red label'>$count</div>
        <span style='font-size:20px' >$period[month_mfo] $period[year_mfo]</span>
      </div>";


    $periods[] = [
      "id" => $period['mfoperiod_id'],
      "month" => $period['month_mfo'],
      "year" => $period['year_mfo']
    ];
  }

  $view = "";

  // $specific_value = "2023";
  // $periods = array_filter($periods, function ($obj) use ($specific_value) {
  //   return $obj->year == $specific_value;
  // });
  $periods = sort_mfo_periods($periods);
  // $json = json_encode($periods, JSON_PRETTY_PRINT);
  // $view .= "<div style='width: 100%; text-align: left; background: white;'>";
  // $view .= "<pre>";
  // $view .= $json;
  // $view .= "</pre>";
  // $view .= "</div>";

  $view .= "<table class='ui celled selectable table'>";
  $view .= "<thead>";
  $view .= "<tr>";
  $view .= "<th class='center aligned'>YEAR</th>";
  $view .= "<th colspan='2' style='text-align: center;'>PERIOD</th>";
  $view .= "";
  $view .= "</tr>";
  $view .= "</thead>";
  $view .= "<tbody>";
  foreach ($periods as $key => $period) {
    $view .= "<tr>";
    $view .= "<td class='center aligned'><b>$period[year]</b></td>";
    $view .= "<td class='center aligned'> <button class='ui fluid button blue' onclick='unrevRec(" . $period['period1']['id'] . ")'>" . $period['period1']['month'] . "</button></td>";
    $view .= "<td class='center aligned'> <button class='ui fluid button red' onclick='unrevRec(" . $period['period2']['id'] . ")'>" . $period['period2']['month'] . "</button></td>";
    $view .= "</tr>";
  }
  $view .= "</tbody>";
  $view .= "</table>";

  if (!$view) {
    $view = "<p style='text-align:center;font-size:20px;font-weight:bolder;color:#00000047'>
        <i class='ui massive exclamation icon'></i>
        <br class='noprint'>
        <br class='noprint'>
        No pending Records Needs to be reviewed</p>";
  }
  // $view .= $mysqli->error;
  return $view;
}


function sort_mfo_periods($periods)
{
  $mfos = [];

  // $mfo = [
  //   "year" => "",
  //   "period1" => [],
  //   "period2" => [],
  // ];

  // mfoperiod_id
  // month_mfo
  // year_mfo
  $years = [];
  foreach ($periods as $period) {
    $year = $period['year'];
    if (!in_array($year, $years)) {
      $years[] = (int) $year;
    }
  }

  rsort($years);
  # remove current year +1
  array_splice($years, 0, 1);

  // $mfos = $years;

  foreach ($years as $key => $year) {
    $mfos[] =  [
      "year" => $year,
      "period1" => get_mfo_using_year($periods, "January - June", $year),
      "period2" => get_mfo_using_year($periods, "July - December", $year)
    ];
  }

  return $mfos;
}

function get_mfo_using_year($periods, $month, $year)
{
  $period = [
    "id" => null,
    "month" => $month,
    "year" => $year
  ];

  foreach ($periods as $el) {
    if ($el['month'] == $month && $el['year'] == $year) {
      $period = $el;
      break;
    }
  }

  return $period;
}

?>