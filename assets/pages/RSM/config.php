<?php
// copy previous rsm start
require_once __DIR__ . '/../../libs/SystemLogger.php';
require_once __DIR__ . '/../../libs/Db.php';
$db = new Db();
$mysqli = $db->getMysqli();

if (isset($_GET['rsm_print'])) {
  // Standalone printable Rating Scale Matrix for a period (opened in a new tab).
  // Routed via ?config=rsm&rsm_print=1[&period=Month&year=YYYY]
  if (isset($_GET['period']) && isset($_GET['year'])) {
    $p = $mysqli->real_escape_string($_GET['period']);
    $y = $mysqli->real_escape_string($_GET['year']);
    $pres = $mysqli->query("SELECT mfoperiod_id FROM spms_periods WHERE month_mfo='$p' AND year_mfo='$y' LIMIT 1");
    if ($pres && $prow = $pres->fetch_assoc()) {
      $_SESSION['period'] = $prow['mfoperiod_id'];
    }
  }

  $period_id = isset($_SESSION['period']) ? $_SESSION['period'] : '';

  // Resolve department for the current user/period (mirrors table() logic)
  $employee_id = $_SESSION['emp_info']['employees_id'];
  $department_id = $_SESSION['emp_info']['department_id'];
  $st = $mysqli->query("SELECT department_id FROM spms_pcr_status WHERE employees_id='$employee_id' AND period_id='$period_id' LIMIT 1");
  if ($st && $strow = $st->fetch_assoc()) {
    $department_id = $strow['department_id'];
  }

  $dept_name = '';
  $dres = $mysqli->query("SELECT department FROM department WHERE department_id='" . $mysqli->real_escape_string($department_id) . "' LIMIT 1");
  if ($dres && $drow = $dres->fetch_assoc()) {
    $dept_name = $drow['department'];
  }

  $period_label = '';
  if ($period_id !== '') {
    $pr = $mysqli->query("SELECT month_mfo, year_mfo FROM spms_periods WHERE mfoperiod_id='" . $mysqli->real_escape_string($period_id) . "' LIMIT 1");
    if ($pr && $prow2 = $pr->fetch_assoc()) {
      $period_label = $prow2['month_mfo'] . ' ' . $prow2['year_mfo'];
    }
  }
?>
  <!DOCTYPE html>
  <html>

  <head>
    <title>Rating Scale Matrix<?= $dept_name ? ' - ' . htmlspecialchars($dept_name) : '' ?></title>
    <link rel="shortcut icon" href="assets/ico/logo.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="assets/libs/ui/dist/semantic.css">
    <script src="assets/libs/jquery/jquery-3.3.1.min.js"></script>
    <style media="screen">
      body {
        background-color: #f7f7f7;
        padding: 30px;
      }

      .rsm-wrapper {
        width: 100%;
        margin: 0 auto;
        background: #fff;
        padding: 24px;
        border-radius: 6px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.12);
      }

      .rsm-toolbar {
        text-align: right;
        margin-bottom: 16px;
      }

      .rsm-print-header {
        text-align: center;
        margin-bottom: 16px;
      }

      .rsm-print-header h2 {
        margin: 0;
      }

      /* Hide editing controls/options for the clean read-only view */
      .noprint {
        display: none !important;
      }

      .tablepr th,
      .tablepr td {
        padding: 8px 10px;
      }
    </style>
    <style media="print">
      @page {
        size: landscape;
      }

      body {
        background: #fff;
        padding: 0;
      }

      .rsm-wrapper {
        box-shadow: none;
        padding: 0;
      }

      .noprint {
        display: none !important;
      }

      .rsm-print-header {
        text-align: center;
      }

      table {
        font-size: 12px;
      }

      .tablepr th,
      .tablepr td {
        padding: 8px 10px;
      }
    </style>
  </head>

  <body>
    <div class="rsm-wrapper">
      <div class="rsm-toolbar noprint">
        <button class="ui small primary button" onclick="window.print()"><i class="print icon"></i> Print</button>
      </div>
      <div class="rsm-print-header">
        <h2>Rating Scale Matrix</h2>
        <?php if ($dept_name) : ?>
          <div style="text-transform:uppercase;font-weight:bold;font-size:1.2em"><?= htmlspecialchars($dept_name) ?></div>
        <?php endif; ?>
        <?php if ($period_label) : ?>
          <div style="color:#555"><?= htmlspecialchars($period_label) ?></div>
        <?php endif; ?>
      </div>
      <?php table($mysqli); ?>
    </div>
  </body>

  </html>
<?php
  exit;
} elseif (isset($_POST['get_mfo_tree'])) {
  if (!isset($_SESSION["emp_info"]["department_id"]) || !isset($_SESSION["period"])) {
    echo json_encode(["error" => "Session data not available"]);
    exit;
  }

  $department_id = $_SESSION["emp_info"]["department_id"];
  $period_id = $_SESSION["period"];

  // Get supervisor IDs for the department/period (used to highlight personnel)
  $supervisor_ids = get_department_supervisor_ids($mysqli, $department_id, $period_id);
  $department_head_id = get_department_head_id($mysqli, $department_id, $period_id);

  // Get department name
  $dept_name = "";
  $dept_alias = "";
  $dept_sql = "SELECT department,alias FROM department WHERE department_id='$department_id'";
  $dept_result = $mysqli->query($dept_sql);
  if ($dept_result && $dept_row = $dept_result->fetch_assoc()) {
    $dept_name = $dept_row["department"];
    $dept_alias = $dept_row["alias"];
  } else {
    $dept_name = "Department";
  }

  // Get top-level MFOs (parent_id='')
  $mfo_children = [];
  $sql = "SELECT cf_ID, cf_count, cf_title, dep_id, corrections FROM spms_pcr_mfos 
          WHERE parent_id='' AND dep_id='$department_id' AND mfo_periodId='$period_id' 
          ORDER BY cf_count ASC";
  $result = $mysqli->query($sql);

  if (!$result) {
    echo json_encode(["error" => $mysqli->error]);
    exit;
  }

  while ($row = $result->fetch_assoc()) {
    $mfo_children[] = build_mfo_tree_node($mysqli, $row, $department_id, $supervisor_ids, $department_head_id);
  }

  // Determine if a previous-period RSM exists for this department (for the duplicator button)
  $prev_rsm_exists = false;
  $previous_period_id = getPreviousPeriodId($mysqli);
  if ($previous_period_id) {
    $prev_sql = "SELECT cf_ID FROM spms_pcr_mfos WHERE mfo_periodId = '$previous_period_id' AND dep_id = '$department_id' LIMIT 1;";
    $prev_res = $mysqli->query($prev_sql);
    if ($prev_res && $prev_res->num_rows > 0) {
      $prev_rsm_exists = true;
    }
  }

  // Create root node with department name
  $tree_data = [[
    "id" => "dept_root",
    "code" => $dept_alias ? $dept_alias : $dept_name,
    "title" => $dept_name,
    "edit_enabled" => rsmEditStatus("") ? true : false,
    "prev_rsm_exists" => $prev_rsm_exists,
    "rsm_status_id" => rsmEditStatus("id") ?: null,
    "children" => $mfo_children
  ]];

  echo json_encode($tree_data);
  exit;
} elseif (isset($_POST['get_mfo_actions'])) {
  $cf_ID = $mysqli->real_escape_string($_POST['get_mfo_actions']);
  $sql = "SELECT * FROM spms_pcr_mfos WHERE cf_ID = '$cf_ID'";
  $result = $mysqli->query($sql);
  if (!$result || !($row = $result->fetch_assoc())) {
    echo "<div style='text-align:center;padding:20px;color:#999;'>MFO not found.</div>";
    exit;
  }

  $has_children = false;
  $childSql = "SELECT cf_ID FROM spms_pcr_mfos WHERE parent_id = '$cf_ID' LIMIT 1";
  $childRes = $mysqli->query($childSql);
  if ($childRes && $childRes->num_rows > 0) {
    $has_children = true;
  }
  $delete_style = $has_children ? 'display:none' : '';

  echo mfoActionsModal($mysqli, $row, '', '', $delete_style);
  exit;
} elseif (isset($_POST['get_org_tree'])) {
  if (!isset($_SESSION["emp_info"]["department_id"]) || !isset($_SESSION["period"])) {
    echo json_encode(["error" => "Session data not available"]);
    exit;
  }

  $department_id = $_SESSION["emp_info"]["department_id"];
  $period_id = $_SESSION["period"];

  $tree_data = get_department_personnel_hierarchy($mysqli, $department_id, $period_id);

  echo json_encode($tree_data);
  exit;
} elseif (isset($_POST['get_prev_rsm'])) {
  $data = [];
  $selected_period_id = $_SESSION["period"];

  // get selected period data
  $sql = "SELECT * FROM spms_periods WHERE mfoperiod_id = '$selected_period_id';";
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
  $sql = "SELECT * FROM spms_periods WHERE mfoperiod_id = '$selected_period_id';";
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
  $sql = "SELECT mfoperiod_id FROM spms_periods WHERE month_mfo = '$months' AND year_mfo = '$year'";
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  $period_id = $row["mfoperiod_id"];

  $department_id = $_SESSION["emp_info"]["department_id"];

  // get previous period core functions
  $data = [];
  $sql = "SELECT * FROM spms_pcr_mfos WHERE mfo_periodId = '$period_id' AND dep_id = '$department_id' AND parent_id = '';";
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
  spms_pcr_mfos
  cf_ID: "9801"
  cf_count: "01."
  cf_title: "Recruitment Services"
  corrections: "a:1:{i:0;a:2:{i:0;s:64:\"<b>VILLARIN, MA. RAYZA</b> - <i>2022-07-15 16:12:15</i>:<br>test\";i:1;i:1;}}"
  dep_id: "32"
  mfo_periodId: "2"
  parent_id: "9816"

  spms_pcr_indicators
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
  // copy to spms_pcr_mfos
  // $sql = "INSERT INTO spms_pcr_mfos(mfo_periodId, parent_id, dep_id, cf_count, cf_title, corrections) VALUES ('$selected_period_id','$datum[parent_id]','$datum[dep_id]','$datum[cf_count]','$datum[cf_title]','')";
  // $data[$i]['core_function_data']['insert_id'] = '9';
  // }

  echo json_encode([$data, $selected_period_id, ""]);

  // echo json_encode($_SESSION["emp_info"]["department_id"]);
}

/*
elseif (isset($_POST['copy_to'])) {...
used to copy rsm or part of rsm with all the children of mfos, 
success indicators and incharges.
set the parameters according to period_id, department_id and mfo parent_id
*/ elseif (isset($_POST['copy_to'])) {
  //origin period and department
  $curr_period_id = 18;
  $curr_department_id = 15;
  $parent_id = "26499";

  //target period and department
  $selected_period_id = 18;
  $selected_parent_id = "";
  $selected_department_id = 26;

  $data = [];
  $sql = "SELECT * FROM spms_pcr_mfos WHERE mfo_periodId = '$curr_period_id' AND dep_id = '$curr_department_id' AND parent_id = '$parent_id';";

  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
    // $data[] = [
    //   "core_function_data" => $row,
    //   "success_indicators" => get_success_indicators($mysqli, $row["cf_ID"])
    // ];
    $row["children"] = get_children($mysqli, $row['cf_ID']);
    $data[] = $row;
  }

  $sql = "SELECT * FROM spms_pcr_mfos WHERE cf_ID = '$parent_id';";
  $res = $mysqli->query($sql);
  if ($row = $res->fetch_assoc()) {
    $row["children"] = $data;
    $data = [];
    $data[] = $row;
  }

  $data = start_duplicating_copy_to($mysqli, $data, $selected_period_id, $selected_parent_id, $selected_department_id);

  print json_encode($data);
}
// copy previous rsm end

// for copying prev rsm of prev dept to new dept as requested by SP
elseif (isset($_POST['copy_to_other_dept'])) {

  $selected_period_id = 12;

  // get selected period data
  $sql = "SELECT * FROM spms_periods WHERE mfoperiod_id = '$selected_period_id';";
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

  $sql = "SELECT mfoperiod_id FROM spms_periods WHERE month_mfo = '$months' AND year_mfo = '$year'";
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  $period_id = $row["mfoperiod_id"];

  $department_id = 15; //set previous department 

  // get previous period core functions
  $data = [];
  $sql = "SELECT * FROM spms_pcr_mfos WHERE mfo_periodId = '$period_id' AND dep_id = '$department_id' AND parent_id = '';";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
    $row["children"] = get_children($mysqli, $row['cf_ID']);
    $data[] = $row;
  }

  $new_department = 26;

  $data = start_duplicating_to_diff_dept($mysqli, $data, $selected_period_id, "", $new_department);

  echo json_encode($data);
} elseif (isset($_POST['period_check'])) {

  $period = "";
  $statusOK = 0;

  $sql = "SELECT * from spms_periods where month_mfo='$_POST[period_check]' and year_mfo='$_POST[year]'";
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
    $sql = "INSERT INTO spms_periods (mfoperiod_id, month_mfo, year_mfo) VALUES (NULL,'$_POST[period_check]','$_POST[year]')";
    $sql = $mysqli->query($sql);
    $statusOK = 1;
    $period = $mysqli->insert_id;
    $_SESSION['period'] = $mysqli->insert_id;
  }
  if ($statusOK) {
    $department = $_SESSION['emp_info']['department_id'];
    $rsmStatus = "SELECT * from spms_rsm_status where period_id='$period' and department_id='$department'";
    $rsmStatus = $mysqli->query($rsmStatus);
    if ($rsmStatus->num_rows < 1) {
      // if this period will end edit will change to zero;
      $rsm = "INSERT INTO spms_rsm_status (rsmStatus_id, period_id, department_id, done, edit, alter_logs) VALUES (NULL, '$period', '$department', '0', '1', '')";
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
    table($mysqli);
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
    $sqlQuery = "INSERT INTO spms_pcr_mfos (cf_ID,mfo_periodId, parent_id, dep_id, cf_count, cf_title) VALUES ('','$_SESSION[period]', '$pid','$dep_id', '$rsmCount', '$addRSMData')";
    $sql = $mysqli->query($sqlQuery);
    if (!$sql) {
      die($mysqli->error);
    } else {
      $escapedLogQuery = $mysqli->real_escape_string($sqlQuery);
      if (!logSpmsSystemQuery($mysqli, $escapedLogQuery)) {
        die($mysqli->error);
      }
      print(1);
    }
  } else {
    echo "Some important input fields are Empty";
  }
} elseif (isset($_POST['editRsmTitle'])) {
  $editRsmTitle = $_POST['editRsmTitle'];
  $editcountRsm = changeCount($_POST['editcountRsm']);
  $dataId = $_POST['dataId'];
  $getC = "SELECT * from spms_pcr_mfos where cf_ID=$dataId";
  $getC = $mysqli->query($getC);
  $getC = $getC->fetch_assoc();
  $update_correction = "";
  if ($getC['corrections']) {
    $update_correction = [];
    $getC = json_decode($getC['corrections'], true) ?? [];
    $count = 0;
    while ($count < count($getC)) {
      $update_correction[] = [$getC[$count][0], 1];
      $count++;
    }
    $update_correction = json_encode($update_correction);
  }

  $editRsmTitle = $mysqli->real_escape_string($editRsmTitle);
  $update_correction = $mysqli->real_escape_string($update_correction);
  $sqlQuery = "UPDATE spms_pcr_mfos SET cf_count = '$editcountRsm', cf_title = '$editRsmTitle',corrections='$update_correction' WHERE spms_pcr_mfos.cf_ID = '$dataId'";
  $sql = $mysqli->query($sqlQuery);
  if (!$sql) {
    die($mysqli->error);
  } else {

    $escapedLogQuery = $mysqli->real_escape_string($sqlQuery);
    if (!logSpmsSystemQuery($mysqli, $escapedLogQuery)) {
      die($mysqli->error);
    }

    print(1);
  }
} elseif (isset($_POST['MfoSiDelete'])) {
  $dataId = $_POST['MfoSiDelete'];
  $sqlQuery = "DELETE FROM spms_pcr_mfos WHERE spms_pcr_mfos.cf_ID ='$dataId'";
  $sql = $mysqli->query($sqlQuery);
  if (!$sql) {
    die($mysqli->error);
  } else {

    $escapedLogQuery = $mysqli->real_escape_string($sqlQuery);
    if (!logSpmsSystemQuery($mysqli, $escapedLogQuery)) {
      die($mysqli->error);
    }

    print(1);
  }
} elseif (isset($_POST['SaveMfoSI'])) {
  $dataId = $_POST['SaveMfoSI'];
  $successIn = $mysqli->real_escape_string($_POST['successIn']);
  $incharge = $mysqli->real_escape_string($_POST['incharge']);
  $sqlQuery = "INSERT INTO spms_pcr_indicators
  (mi_id, cf_ID, mi_succIn)
  VALUES
  (NULL, '$dataId', '$successIn')";
  $sql = $mysqli->query($sqlQuery);
  if (!$sql) {
    die($mysqli->error);
  } else {
    $new_mi_id   = $mysqli->insert_id;
    $period_id   = $_SESSION['period'];
    $assigned_by = $_SESSION['emp_info']['employees_id'];
    $emp_arr = array_filter(array_map('trim', explode(',', $incharge)));
    foreach ($emp_arr as $emp_id) {
      if (!is_numeric($emp_id)) continue;
      $assign_sql = "INSERT INTO spms_pcr_si_assignments (success_indicator_id, user_id, period_id, assigned_by, created_at, updated_at)
                     VALUES ('$new_mi_id', '$emp_id', '$period_id', '$assigned_by', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())";
      $mysqli->query($assign_sql);
    }
    $qet_map = ['quality' => $_POST['quality'], 'efficiency' => $_POST['efficiency'], 'timeliness' => $_POST['timeliness']];
    foreach ($qet_map as $measure_type => $scores) {
      if (!is_array($scores)) continue;
      foreach ($scores as $score => $descriptor) {
        $descriptor = trim($descriptor);
        if ($descriptor === '' || !is_numeric($score)) continue;
        $esc = $mysqli->real_escape_string($descriptor);
        $mysqli->query("INSERT IGNORE INTO spms_pcr_si_qet_descriptors (success_indicator_id, measure_type, score, descriptor, created_at, updated_at)
                        VALUES ('$new_mi_id', '$measure_type', '$score', '$esc', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())");
      }
    }
    $escapedLogQuery = $mysqli->real_escape_string($sqlQuery);
    if (!logSpmsSystemQuery($mysqli, $escapedLogQuery)) {
      die($mysqli->error);
    }
    print(1);
  }
} elseif (isset($_POST['SaveMfoSIEdit'])) {
  $dataId = $_POST['SaveMfoSIEdit'];
  $successIn = $mysqli->real_escape_string($_POST['successIn']);
  $incharge = $mysqli->real_escape_string($_POST['incharge']);
  $getC = "SELECT * from spms_pcr_indicators where mi_id=$dataId";
  $getC = $mysqli->query($getC);
  $getC = $getC->fetch_assoc();
  $update_correction = "";
  if ($getC['corrections']) {
    $update_correction = [];
    $getC = json_decode($getC['corrections'], true) ?? [];
    $count = 0;
    while ($count < count($getC)) {
      $update_correction[] = [$getC[$count][0], 1];
      $count++;
    }
    $update_correction = json_encode($update_correction);
  }
  $update_correction = $mysqli->real_escape_string($update_correction);
  $sqlQuery = "UPDATE spms_pcr_indicators SET
  mi_succIn = '$successIn',
  corrections = '$update_correction'
  WHERE spms_pcr_indicators.mi_id = $dataId;
  ";
  $sql = $mysqli->query($sqlQuery);
  if (!$sql) {
    die($mysqli->error);
  } else {
    $period_id   = $_SESSION['period'];
    $assigned_by = $_SESSION['emp_info']['employees_id'];
    $stmt = $mysqli->prepare("DELETE FROM spms_pcr_si_assignments WHERE success_indicator_id = ?");
    $stmt->bind_param("i", $dataId);
    $stmt->execute();
    $stmt->close();
    $emp_arr = array_filter(array_map('trim', explode(',', $incharge)));
    foreach ($emp_arr as $emp_id) {
      if (!is_numeric($emp_id)) continue;
      $assign_sql = "INSERT INTO spms_pcr_si_assignments (success_indicator_id, user_id, period_id, assigned_by, created_at, updated_at)
                     VALUES ('$dataId', '$emp_id', '$period_id', '$assigned_by', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())";
      $mysqli->query($assign_sql);
    }
    $stmt = $mysqli->prepare("DELETE FROM spms_pcr_si_qet_descriptors WHERE success_indicator_id = ?");
    $stmt->bind_param("i", $dataId);
    $stmt->execute();
    $stmt->close();
    $qet_map = ['quality' => $_POST['quality'], 'efficiency' => $_POST['efficiency'], 'timeliness' => $_POST['timeliness']];
    foreach ($qet_map as $measure_type => $scores) {
      if (!is_array($scores)) continue;
      foreach ($scores as $score => $descriptor) {
        $descriptor = trim($descriptor);
        if ($descriptor === '' || !is_numeric($score)) continue;
        $esc = $mysqli->real_escape_string($descriptor);
        $mysqli->query("INSERT INTO spms_pcr_si_qet_descriptors (success_indicator_id, measure_type, score, descriptor, created_at, updated_at)
                        VALUES ('$dataId', '$measure_type', '$score', '$esc', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())");
      }
    }
    $escapedLogQuery = $mysqli->real_escape_string($sqlQuery);
    if (!logSpmsSystemQuery($mysqli, $escapedLogQuery)) {
      die($mysqli->error);
    }

    print(1);
  }
} elseif (isset($_POST['removeSi'])) {
  $removeSiId = (int) $_POST['removeSi'];
  $sqlQuery = "DELETE FROM spms_pcr_indicators WHERE spms_pcr_indicators.mi_id = '$removeSiId'";

  $sql = $mysqli->query($sqlQuery);
  if (!$sql) {
    die($mysqli->error);
  } else {
    $stmt = $mysqli->prepare("DELETE FROM spms_pcr_si_assignments WHERE success_indicator_id = ?");
    $stmt->bind_param("i", $removeSiId);
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("DELETE FROM spms_pcr_si_qet_descriptors WHERE success_indicator_id = ?");
    $stmt->bind_param("i", $removeSiId);
    $stmt->execute();
    $stmt->close();
    $escapedLogQuery = $mysqli->real_escape_string($sqlQuery);
    if (!logSpmsSystemQuery($mysqli, $escapedLogQuery)) {
      die($mysqli->error);
    }

    print(1);
  }
} elseif (isset($_POST['closeRsm'])) {
  $rsmStatusId = (int)$_POST['closeRsm'];

  $sqlQuery = "UPDATE spms_rsm_status SET edit = '0' , done='1' WHERE spms_rsm_status.rsmStatus_id = '$rsmStatusId'";
  $sql = $mysqli->query($sqlQuery);

  if (!$sql) {
    die($mysqli->error);
  } else {

    $escapedLogQuery = $mysqli->real_escape_string($sqlQuery);
    if (!logSpmsSystemQuery($mysqli, $escapedLogQuery)) {
      die($mysqli->error);
    }

    echo 1;
  }
} elseif (isset($_POST['getRsmparentChange'])) {
  $rsmMFO = new RsmClass();
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
  $sql  = "UPDATE spms_pcr_mfos SET parent_id = '$parent' WHERE spms_pcr_mfos.cf_ID = $sub";
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

  $sql = "SELECT * FROM spms_periods WHERE mfoperiod_id = '$period_id';";
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

  $sql = "SELECT * FROM spms_periods WHERE month_mfo = '$months' AND year_mfo = '$year';";
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

  $sql = "SELECT * FROM spms_pcr_status WHERE employees_id = '$employee_id' AND period_id = '$period_id'";
  $res = $mysqli->query($sql);
  if ($row = $res->fetch_assoc()) {
    $department_id = $row['department_id'];
  } else {
    $department_id = $_SESSION['emp_info']['department_id'];
  }

  $dep = "SELECT * from department where department_id='$department_id'";
  $dep = $mysqli->query($dep);
  $dep = $dep->fetch_assoc();
  $dep = $dep['department'];


  $period = "SELECT * from spms_periods where mfoperiod_id='$period_id'";
  $period = $mysqli->query($period);
  $period = $period->fetch_assoc();
  // $period_id = $period['mfoperiod_id'];
  echo "
  <button class='noprint' onclick = 'rsmLoad(\"table\")' style='cursor:pointer;'>Refresh</button>
  <button class='noprint' id='rsmToggleAll' onclick='rsmToggleAll()' style='margin-left:6px;cursor:pointer;'>&#9660; Collapse All</button>
  <style>
  tr[data-mfo-id] { transition: opacity 0.15s; }
  .rsm-chevron { user-select:none; font-size:18px; padding:2px 4px; cursor:pointer; }
  .rsm-chevron.collapsed { transform: rotate(-90deg); }
  #rsm-toggle-loader { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.5); z-index:9999; align-items:center; justify-content:center; font-size:20px; }
  #rsm-toggle-loader.active { display:flex; }
  </style>
  <div id='rsm-toggle-loader'><i class='ui spinner loading icon' style='font-size:32px;'></i></div>
  <script>
  $('.ui.dropdown').dropdown({
    fullTextSearch:true
  });
  (function(){
    function getAllDescendants(mfoId) {
      var rows = [];
      document.querySelectorAll('[data-mfo-parent=\"' + mfoId + '\"]').forEach(function(tr) {
        rows.push(tr);
        var childId = tr.getAttribute('data-mfo-id');
        if (childId) {
          getAllDescendants(childId).forEach(function(c){ rows.push(c); });
        }
      });
      return rows;
    }
    document.querySelectorAll('.rsm-chevron').forEach(function(chevron) {
      chevron.addEventListener('click', function() {
        var mfoId = this.getAttribute('data-toggle');
        var collapsed = this.classList.toggle('collapsed');
        getAllDescendants(mfoId).forEach(function(tr) {
          tr.style.display = collapsed ? 'none' : '';
        });
      });
    });
    window.rsmToggleAll = function() {
      var loader = document.getElementById('rsm-toggle-loader');
      loader.classList.add('active');
      setTimeout(function() {
        var btn = document.getElementById('rsmToggleAll');
        var collapsing = btn.getAttribute('data-state') !== 'collapsed';
        document.querySelectorAll('.rsm-chevron').forEach(function(chevron) {
          var mfoId = chevron.getAttribute('data-toggle');
          if (collapsing) {
            chevron.classList.add('collapsed');
          } else {
            chevron.classList.remove('collapsed');
          }
          getAllDescendants(mfoId).forEach(function(tr) {
            tr.style.display = collapsing ? 'none' : '';
          });
        });
        if (collapsing) {
          btn.setAttribute('data-state', 'collapsed');
          btn.innerHTML = '&#9654; Expand All';
        } else {
          btn.setAttribute('data-state', '');
          btn.innerHTML = '&#9660; Collapse All';
        }
        loader.classList.remove('active');
      }, 50);
    };
  })();
  </script>
  <style>
  .tablepr, .tablepr th, .tablepr td { border: 1px solid #000 !important; }
  </style>
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

  $sql = "SELECT * FROM spms_pcr_status WHERE employees_id = '$employee_id' AND period_id = '$period_id'";
  $res = $mysqli->query($sql);
  if ($row = $res->fetch_assoc()) {
    $dep_id = $row['department_id'];
  } else {
    $dep_id = $_SESSION['emp_info']['department_id'];
  }

  $sql = "SELECT * from spms_pcr_mfos where parent_id='' and mfo_periodId='$period_id' and dep_id='$dep_id' ORDER BY spms_pcr_mfos.cf_count ASC ";
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
  $sql2 = "SELECT * from spms_pcr_mfos where parent_id='$dataId' ORDER BY spms_pcr_mfos.cf_count ASC";
  $sql2 = $mysqli->query($sql2);
  $padding += 15;
  while ($row2 = $sql2->fetch_assoc()) {
    $pad = $padding . "px";
    $view .= trows($mysqli, $row2, $pad, '', $dataId);
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
  <div><button class='mini green ui basic icon button' style='margin-top: 10px; margin-left:2px;' onclick='EditRsmTitle($dataId)'><i class='save icon'></i> Save</button></div>
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
    if (isset($arr[$count]) && $arr[$count] != "") {
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
    $dat = json_decode($dat);
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

function trows($mysqli, $row, $padding, $addDisplay, $mfo_parent_id = '')
{
  $mfo_id = $row['cf_ID'];
  $sql2 = "SELECT * from spms_pcr_mfos where parent_id='$mfo_id'";
  $sql2 = $mysqli->query($sql2);
  $sql2count = $sql2->num_rows;
  if ($sql2count > 0) {
    $set_drop = settingDrop($mysqli, $row, '', $addDisplay, 'display:none');
    $chevron = "<span class='rsm-chevron noprint' data-toggle='$mfo_id' style='cursor:pointer;margin-right:8px;display:inline-block;transition:transform 0.2s;font-size:18px;line-height:1;vertical-align:middle;'>&#9660;</span>";
  } else {
    $set_drop = settingDrop($mysqli, $row, '', $addDisplay, '');
    $chevron = "<span style='display:inline-block;width:18px;margin-right:6px;'></span>";
  }
  $view = "";
  $siData1 = "SELECT * from spms_pcr_indicators where cf_ID='$mfo_id'";
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
      $siMiId = $siDataRow1['mi_id'];
      $assignSql = "SELECT a.user_id, e.employees_id, e.firstName, e.lastName, e.middleName, e.extName
                    FROM spms_pcr_si_assignments a
                    LEFT JOIN employees e ON a.user_id = e.employees_id
                    WHERE a.success_indicator_id = '$siMiId'";
      $assignRes = $mysqli->query($assignSql);
      while ($sqlIncharge = $assignRes->fetch_assoc()) {
        if (!$sqlIncharge['employees_id']) continue;
        $firstName  = $sqlIncharge['firstName']  ?? '';
        $lastName   = $sqlIncharge['lastName']   ?? '';
        $middleName = $sqlIncharge['middleName'] ?? '';
        $extName    = $sqlIncharge['extName']    ?? '';

        $middleInitial = $middleName !== '' ? $middleName[0] . '.' : '';
        $extFormatted  = $extName !== '' ? ", $extName" : '';

        $parts = array_filter([$lastName, $firstName, $middleInitial]);
        $fullName = implode(' ', $parts) . $extFormatted;

        $empincharge .= "<br><a onclick='ShowIPcrModal(\"$sqlIncharge[employees_id]\")' style='cursor:pointer;'>$fullName</a><br>";
      }

      // if (isset($siDataRow1['mi_id'])) {
      //   $mi_id = $siDataRow1['mi_id'];
      //   $sql = "SELECT * FROM spms_pcr_indicator_accomplishments where p_id = '$mi_id' AND empId = '$empDataId';";
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
      $Qdata = "";
      $Edata = "";
      $Tdata = "";
      $performanceMeasure = "";
      $qetDisplay = [];
      foreach (['quality' => 'Quality', 'efficiency' => 'Efficiency', 'timeliness' => 'Timeliness'] as $mtype => $mlabel) {
        $stmt = $mysqli->prepare("SELECT score, descriptor FROM spms_pcr_si_qet_descriptors WHERE success_indicator_id = ? AND measure_type = ? ORDER BY score DESC");
        $stmt->bind_param("is", $siMiId, $mtype);
        $stmt->execute();
        $qetRes = $stmt->get_result();
        $qetHtml = "";
        while ($qetRow = $qetRes->fetch_assoc()) {
          $qetHtml .= "<b>" . $qetRow['score'] . "</b> - " . htmlspecialchars($qetRow['descriptor']) . "<br>";
        }
        $stmt->close();
        $qetDisplay[$mtype] = $qetHtml;
        if ($qetHtml !== "") {
          $performanceMeasure .= "$mlabel<br>";
        }
      }
      if ($count == 1) {
        $view .= "
        <tr data-mfo-id='$mfo_id' data-mfo-parent='$mfo_parent_id'>
        <td style='padding-left:$padding;width:25%;$correctionColorMFO'>
        $chevron" . $set_drop . "
        $row[cf_count]) $row[cf_title] " .  ""/*json_encode($row)*/ . "
        </td>
        <td style='width:25%;$correctionColor'>" . nl2br($siDataRow1['mi_succIn']) . ""/*json_encode($siDataRow1)*/ . "</td>
        <td>$performanceMeasure</td>
        <td style='width:150px;padding-bottom:10px;$correctionColor'>" . $qetDisplay['quality'] . "</td>
        <td style='width:150px;padding-bottom:10px;$correctionColor'>" . $qetDisplay['efficiency'] . "</td>
        <td style='width:150px;padding-bottom:10px;$correctionColor'>" . $qetDisplay['timeliness'] . "</td>
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
        <tr data-mfo-owner='$mfo_id' data-mfo-parent='$mfo_parent_id'>
        <td></td>
        <td style='width:25%;$correctionColor'>" . nl2br($siDataRow1['mi_succIn']) . "</td>
        <td>$performanceMeasure</td>
        <td style='width:150px;padding-bottom:10px;$correctionColor'>" . $qetDisplay['quality'] . "</td>
        <td style='width:150px;padding-bottom:10px;$correctionColor'>" . $qetDisplay['efficiency'] . "</td>
        <td style='width:150px;padding-bottom:10px;$correctionColor'>" . $qetDisplay['timeliness'] . "</td>
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
    <tr data-mfo-id='$mfo_id' data-mfo-parent='$mfo_parent_id'>
    <td colspan='7' style='padding-left:$padding;width:500px;$correctionColorMFO'>
    $chevron" . $set_drop . "
    $row[cf_count]) $row[cf_title] " . ""/*json_encode($row)*/ . "
    </td>
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

  $sql = "SELECT * from spms_rsm_status where period_id='$period' and department_id='$department_id'";
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

  $sql = "SELECT * FROM spms_pcr_status WHERE employees_id = '$employee_id' AND period_id = '$period_id'";
  $res = $mysqli->query($sql);
  if ($row = $res->fetch_assoc()) {
    $department_id = $row['department_id'];
  } else {
    $department_id = $_SESSION['emp_info']['department_id'];
  }


  $curr_rsm_exists = false;
  $prev_rsm_exists = false;
  $previous_period_id = getPreviousPeriodId($mysqli);
  $sql = "SELECT * FROM spms_pcr_mfos WHERE mfo_periodId = '$period_id' AND dep_id = '$department_id' LIMIT 1;";
  $result = $mysqli->query($sql);
  if ($result->num_rows > 0) {
    $curr_rsm_exists = true;
  }

  $sql = "SELECT * FROM spms_pcr_mfos WHERE mfo_periodId = '$previous_period_id' AND dep_id = '$department_id' LIMIT 1;";
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
    <div> <button class='mini ui primary basic icon button' onclick='addMFoRsm(\"$dataId\")' style='margin-left: 2px;'> <i class='save icon'></i> Save</button></div>
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
function mfoActionsItems($mysqli, $row, $edit, $add, $delete)
{
  $correction = "";
  if ($row['corrections']) {
    $c = json_decode($row['corrections'], true) ?? [];
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
  return "
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
  ";
}
function settingDrop($mysqli, $row, $edit, $add, $delete, $triggerIcon = "green settings icon")
{
  $view = "
  <div class='mini ui left pointing dropdown icon noprint'>
  <i class='{$triggerIcon}'></i>
  <div class='menu'>
  <div class='header'>
  <i class='tags icon'></i>
  Actions
  </div>
  " . mfoActionsItems($mysqli, $row, $edit, $add, $delete) . "
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
function mfoActionsModal($mysqli, $row, $edit, $add, $delete)
{
  $view = "
  <div style='max-width:500px;margin:0 auto;'>
  <div class='header' style='text-align:center;'>
  <i class='tags icon'></i>
  Actions
  </div>
  " . mfoActionsItems($mysqli, $row, $edit, $add, $delete) . "
  </div>
  ";
  $correctionMFO = validaateCorrection($row['corrections']);
  if (!rsmEditStatus("") && !$correctionMFO) {
    $view = "<div style='text-align:center;padding:20px;color:#999;'>Editing is not enabled for this period.</div>";
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
  $sql = "SELECT * FROM spms_pcr_mfos WHERE parent_id ='$cf_ID'";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
    $row["children"] = get_children($mysqli, $row["cf_ID"]);
    $data[] = $row;
  }
  return $data;
}

function copy_qet_descriptors($mysqli, $source_mi_id, $new_mi_id)
{
  $stmt = $mysqli->prepare("SELECT measure_type, score, descriptor FROM spms_pcr_si_qet_descriptors WHERE success_indicator_id = ?");
  $stmt->bind_param("i", $source_mi_id);
  $stmt->execute();
  $res = $stmt->get_result();
  while ($row = $res->fetch_assoc()) {
    $esc = $mysqli->real_escape_string($row['descriptor']);
    $mysqli->query("INSERT IGNORE INTO spms_pcr_si_qet_descriptors (success_indicator_id, measure_type, score, descriptor, created_at, updated_at)
                    VALUES ('$new_mi_id', '$row[measure_type]', '$row[score]', '$esc', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())");
  }
  $stmt->close();
}

function start_duplicating($mysqli, $data, $selected_period_id, $parent_id, $department_id = null)
{
  // $department_id = 3;
  foreach ($data as $key => $core_function) {
    $department_id = $core_function['dep_id'];
    if (!$department_id) {
      $department_id = $core_function['dep_id'];
    }
    $parent_id = $parent_id ? $parent_id : NULL;
    $cf_title = $mysqli->real_escape_string($core_function['cf_title']);
    $cf_count = $mysqli->real_escape_string($core_function['cf_count']);
    $sql = "INSERT INTO spms_pcr_mfos(mfo_periodId, parent_id, dep_id, cf_count, cf_title, corrections) VALUES ('$selected_period_id','$parent_id','$department_id','$cf_count','$cf_title','')";
    $mysqli->query($sql);
    $insert_id = $mysqli->insert_id;

    #get success indicators
    $success_idicators = get_success_indicators($mysqli, $core_function["cf_ID"]);
    foreach ($success_idicators as $success_idicator) {

      $mi_succIn = $mysqli->real_escape_string($success_idicator['mi_succIn']);

      $sql = "INSERT INTO spms_pcr_indicators(cf_ID, mi_succIn, corrections) VALUES ('$insert_id','$mi_succIn','')";
      $mysqli->query($sql);
      $new_mi_id = $mysqli->insert_id;
      $src_mi_id = $success_idicator['mi_id'];
      $stmt = $mysqli->prepare("SELECT user_id FROM spms_pcr_si_assignments WHERE success_indicator_id = ?");
      $stmt->bind_param("i", $src_mi_id);
      $stmt->execute();
      $inRes = $stmt->get_result();
      while ($inRow = $inRes->fetch_assoc()) {
        $emp_id = $inRow['user_id'];
        $mysqli->query("INSERT INTO spms_pcr_si_assignments (success_indicator_id, user_id, period_id, assigned_by, created_at, updated_at)
                        VALUES ('$new_mi_id', '$emp_id', '$selected_period_id', 9, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())");
      }
      $stmt->close();
      copy_qet_descriptors($mysqli, $src_mi_id, $new_mi_id);
    }

    $data[$key]["children"] = start_duplicating($mysqli, $core_function["children"], $selected_period_id, $insert_id);
  }

  return $data;
}

function start_duplicating_copy_to($mysqli, $data, $selected_period_id, $parent_id, $department_id = null)
{
  // $department_id = 3;
  foreach ($data as $key => $core_function) {
    // $department_id = $core_function['dep_id'];
    // if (!$department_id) {
    //   $department_id = $core_function['dep_id'];
    // }
    $parent_id = $parent_id ? $parent_id : NULL;
    $cf_title = $mysqli->real_escape_string($core_function['cf_title']);
    $cf_count = $mysqli->real_escape_string($core_function['cf_count']);
    $sql = "INSERT INTO spms_pcr_mfos(mfo_periodId, parent_id, dep_id, cf_count, cf_title, corrections) VALUES ('$selected_period_id','$parent_id','$department_id','$cf_count','$cf_title','')";
    $mysqli->query($sql);
    $insert_id = $mysqli->insert_id;

    #get success indicators
    $success_idicators = get_success_indicators($mysqli, $core_function["cf_ID"]);
    foreach ($success_idicators as $success_idicator) {

      $mi_succIn = $mysqli->real_escape_string($success_idicator['mi_succIn']);

      $sql = "INSERT INTO spms_pcr_indicators(cf_ID, mi_succIn, corrections) VALUES ('$insert_id','$mi_succIn','')";
      $mysqli->query($sql);
      $new_mi_id = $mysqli->insert_id;
      $src_mi_id = $success_idicator['mi_id'];
      $stmt = $mysqli->prepare("SELECT user_id FROM spms_pcr_si_assignments WHERE success_indicator_id = ?");
      $stmt->bind_param("i", $src_mi_id);
      $stmt->execute();
      $inRes = $stmt->get_result();
      while ($inRow = $inRes->fetch_assoc()) {
        $emp_id = $inRow['user_id'];
        $mysqli->query("INSERT INTO spms_pcr_si_assignments (success_indicator_id, user_id, period_id, assigned_by, created_at, updated_at)
                        VALUES ('$new_mi_id', '$emp_id', '$selected_period_id', 9, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())");
      }
      $stmt->close();
      copy_qet_descriptors($mysqli, $src_mi_id, $new_mi_id);
    }

    $data[$key]["children"] = start_duplicating_copy_to($mysqli, $core_function["children"], $selected_period_id, $insert_id);
  }

  return $data;
}


function start_duplicating_to_diff_dept($mysqli, $data, $selected_period_id, $parent_id, $department_id)
{
  foreach ($data as $key => $core_function) {
    // $department_id = $core_function['dep_id'];
    // if (!$department_id) {
    //   $department_id = $core_function['dep_id'];
    // }
    $parent_id = $parent_id ? $parent_id : NULL;
    $cf_title = $mysqli->real_escape_string($core_function['cf_title']);
    $cf_count = $mysqli->real_escape_string($core_function['cf_count']);
    $sql = "INSERT INTO spms_pcr_mfos(mfo_periodId, parent_id, dep_id, cf_count, cf_title, corrections) VALUES ('$selected_period_id','$parent_id','$department_id','$cf_count','$cf_title','')";
    $mysqli->query($sql);
    $insert_id = $mysqli->insert_id;

    #get success indicators
    $success_idicators = get_success_indicators($mysqli, $core_function["cf_ID"]);
    foreach ($success_idicators as $success_idicator) {

      $mi_succIn = $mysqli->real_escape_string($success_idicator['mi_succIn']);

      $sql = "INSERT INTO spms_pcr_indicators(cf_ID, mi_succIn, corrections) VALUES ('$insert_id','$mi_succIn','')";
      $mysqli->query($sql);
      $new_mi_id = $mysqli->insert_id;
      $src_mi_id = $success_idicator['mi_id'];
      $stmt = $mysqli->prepare("SELECT user_id FROM spms_pcr_si_assignments WHERE success_indicator_id = ?");
      $stmt->bind_param("i", $src_mi_id);
      $stmt->execute();
      $inRes = $stmt->get_result();
      while ($inRow = $inRes->fetch_assoc()) {
        $emp_id = $inRow['user_id'];
        $mysqli->query("INSERT INTO spms_pcr_si_assignments (success_indicator_id, user_id, period_id, assigned_by, created_at, updated_at)
                        VALUES ('$new_mi_id', '$emp_id', '$selected_period_id', 9, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())");
      }
      $stmt->close();
      copy_qet_descriptors($mysqli, $src_mi_id, $new_mi_id);
    }

    $data[$key]["children"] = start_duplicating_to_diff_dept($mysqli, $core_function["children"], $selected_period_id, $insert_id, $department_id);
  }

  return $data;
}


function get_success_indicators($mysqli, $cf_ID)
{
  $data = [];
  $sql = "SELECT * FROM spms_pcr_indicators WHERE cf_ID = '$cf_ID'";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
    $data[] = $row;
  }
  return $data;
}

// Helper function to get formatted success indicators for tree
function get_success_indicators_formatted($mysqli, $cf_ID, $supervisor_ids = [], $department_head_id = null)
{
  $data = [];
  $sql = "SELECT mi_id, mi_succIn, corrections FROM spms_pcr_indicators WHERE cf_ID = '$cf_ID'";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
    $qet = get_si_qet_measures($mysqli, $row["mi_id"]);
    $data[] = [
      "id" => $row["mi_id"],
      "description" => $row["mi_succIn"],
      "quality" => $qet["quality"],
      "efficiency" => $qet["efficiency"],
      "timeliness" => $qet["timeliness"],
      "personnel_incharge" => get_si_personnel_incharge($mysqli, $row["mi_id"], $supervisor_ids, $department_head_id),
      "corrections" => parse_corrections($row["corrections"])
    ];
  }
  return $data;
}

// Parse a corrections JSON string into a list of comments with accomplished status
function parse_corrections($corrections)
{
  $out = [];
  if (!$corrections) return $out;
  $c = json_decode($corrections, true);
  if (!is_array($c)) return $out;
  foreach ($c as $item) {
    if (!is_array($item) || !isset($item[0])) continue;
    $out[] = [
      "comment" => $item[0],
      "accomplished" => !empty($item[1])
    ];
  }
  return $out;
}

// Helper function to get personnel in-charge for a single success indicator
function get_si_personnel_incharge($mysqli, $mi_id, $supervisor_ids = [], $department_head_id = null)
{
  $personnel = [];
  $seen_ids = [];

  $assign_sql = "SELECT a.user_id, e.employees_id, e.firstName, e.lastName, e.middleName, e.extName
                  FROM spms_pcr_si_assignments a
                  LEFT JOIN employees e ON a.user_id = e.employees_id
                  WHERE a.success_indicator_id = '$mi_id'";
  $assign_result = $mysqli->query($assign_sql);

  while ($emp_row = $assign_result->fetch_assoc()) {
    if (!$emp_row['employees_id']) continue;
    if (in_array($emp_row['employees_id'], $seen_ids)) continue;
    $seen_ids[] = $emp_row['employees_id'];

    $firstName  = $emp_row['firstName']  ?? '';
    $lastName   = $emp_row['lastName']   ?? '';
    $middleName = $emp_row['middleName'] ?? '';
    $extName    = $emp_row['extName']    ?? '';

    $middleInitial = $middleName !== '' ? $middleName[0] . '.' : '';
    $extFormatted  = $extName !== '' ? ", $extName" : '';

    $parts = array_filter([$lastName, $firstName, $middleInitial]);
    $fullName = implode(' ', $parts) . $extFormatted;

    $personnel[] = [
      "employee_id" => $emp_row['employees_id'],
      "full_name" => $fullName,
      "is_supervisor" => in_array($emp_row['employees_id'], $supervisor_ids),
      "is_department_head" => ($department_head_id && $emp_row['employees_id'] == $department_head_id)
    ];
  }

  return $personnel;
}

// Helper function to get Q/E/T measures for a success indicator
function get_si_qet_measures($mysqli, $mi_id)
{
  $measures = ["quality" => [], "efficiency" => [], "timeliness" => []];

  $sql = "SELECT measure_type, score, descriptor FROM spms_pcr_si_qet_descriptors 
          WHERE success_indicator_id = '$mi_id' 
          ORDER BY score DESC";
  $result = $mysqli->query($sql);

  if ($result) {
    while ($row = $result->fetch_assoc()) {
      $type = $row["measure_type"];
      if (isset($measures[$type])) {
        $measures[$type][] = [
          "score" => $row["score"],
          "descriptor" => $row["descriptor"]
        ];
      }
    }
  }

  return $measures;
}

// Helper function to get personnel in-charge for an MFO
function get_mfo_personnel_incharge($mysqli, $cf_id, $supervisor_ids = [], $department_head_id = null)
{
  $personnel = [];
  $seen_ids = [];

  // Get all success indicators for this MFO
  $si_sql = "SELECT mi_id FROM spms_pcr_indicators WHERE cf_ID = '$cf_id'";
  $si_result = $mysqli->query($si_sql);

  while ($si_row = $si_result->fetch_assoc()) {
    $mi_id = $si_row['mi_id'];

    // Get personnel assignments for this success indicator
    $assign_sql = "SELECT a.user_id, e.employees_id, e.firstName, e.lastName, e.middleName, e.extName
                    FROM spms_pcr_si_assignments a
                    LEFT JOIN employees e ON a.user_id = e.employees_id
                    WHERE a.success_indicator_id = '$mi_id'";
    $assign_result = $mysqli->query($assign_sql);

    while ($emp_row = $assign_result->fetch_assoc()) {
      if (!$emp_row['employees_id']) continue;

      // Skip duplicates
      if (in_array($emp_row['employees_id'], $seen_ids)) continue;
      $seen_ids[] = $emp_row['employees_id'];

      $firstName  = $emp_row['firstName']  ?? '';
      $lastName   = $emp_row['lastName']   ?? '';
      $middleName = $emp_row['middleName'] ?? '';
      $extName    = $emp_row['extName']    ?? '';

      $middleInitial = $middleName !== '' ? $middleName[0] . '.' : '';
      $extFormatted  = $extName !== '' ? ", $extName" : '';

      $parts = array_filter([$lastName, $firstName, $middleInitial]);
      $fullName = implode(' ', $parts) . $extFormatted;

      $personnel[] = [
        "employee_id" => $emp_row['employees_id'],
        "full_name" => $fullName,
        "is_supervisor" => in_array($emp_row['employees_id'], $supervisor_ids),
        "is_department_head" => ($department_head_id && $emp_row['employees_id'] == $department_head_id)
      ];
    }
  }

  return $personnel;
}

// Helper function to get IDs of supervisors (those with subordinates) in a department/period
function get_department_supervisor_ids($mysqli, $department_id, $period_id)
{
  $supervisor_ids = [];

  $sql = "SELECT DISTINCT ImmediateSup, DepartmentHead FROM spms_pcr_status 
          WHERE department_id = '$department_id' AND period_id = '$period_id'";
  $result = $mysqli->query($sql);

  if ($result) {
    while ($row = $result->fetch_assoc()) {
      if (!empty($row['ImmediateSup'])) {
        $supervisor_ids[] = $row['ImmediateSup'];
      }
      if (!empty($row['DepartmentHead'])) {
        $supervisor_ids[] = $row['DepartmentHead'];
      }
    }
  }

  return array_values(array_unique($supervisor_ids));
}

// Helper function to get the department head ID for a department/period
function get_department_head_id($mysqli, $department_id, $period_id)
{
  $sql = "SELECT DepartmentHead FROM spms_pcr_status 
          WHERE department_id = '$department_id' AND period_id = '$period_id' AND DepartmentHead != '' 
          LIMIT 1";
  $result = $mysqli->query($sql);

  if ($result && $row = $result->fetch_assoc()) {
    return $row['DepartmentHead'];
  }

  return null;
}

// Recursive function to get MFO children
function get_mfo_tree_children($mysqli, $parent_id, $department_id, $supervisor_ids = [], $department_head_id = null)
{
  $children = [];
  $sql = "SELECT cf_ID, cf_count, cf_title, dep_id, corrections FROM spms_pcr_mfos 
          WHERE parent_id='$parent_id' AND dep_id='$department_id' 
          ORDER BY cf_count ASC";
  $result = $mysqli->query($sql);

  while ($row = $result->fetch_assoc()) {
    $children[] = build_mfo_tree_node($mysqli, $row, $department_id, $supervisor_ids, $department_head_id);
  }

  return $children;
}

function build_mfo_tree_node($mysqli, $row, $department_id, $supervisor_ids, $department_head_id)
{
  $children = get_mfo_tree_children($mysqli, $row["cf_ID"], $department_id, $supervisor_ids, $department_head_id);
  $can_edit = rsmEditStatus("") ? true : false;

  return [
    "id" => $row["cf_ID"],
    "code" => "",
    "title" => $row["cf_count"] . ". " . $row["cf_title"],
    "personnel_incharge" => get_mfo_personnel_incharge($mysqli, $row["cf_ID"], $supervisor_ids, $department_head_id),
    "success_indicators" => get_success_indicators_formatted($mysqli, $row["cf_ID"], $supervisor_ids, $department_head_id),
    "children" => $children,
    "can_edit" => $can_edit
  ];
}

// Helper function to get employee name
function get_employee_name($mysqli, $employee_id)
{
  $sql = "SELECT * from employees where employees_id='$employee_id'";
  $res = $mysqli->query($sql);

  $name = "";

  if ($row = $res->fetch_assoc()) {
    $name = "$row[lastName], $row[firstName]";
    if ($row['extName']) {
      $name .= " " . $row['extName'];
    }
    $name = mb_strtoupper($name);
  }

  return $name;
}

// Helper function to sort personnel alphabetically
function order_personnel($personnel)
{
  usort($personnel, fn($a, $b) => strcmp($a['name'], $b['name']));
  return $personnel;
}

// Helper function to build hierarchical tree
function buildTree(array $elements, $parentId)
{
  $branch = array();

  foreach ($elements as $element) {
    if ($element['parent_id'] == $parentId) {
      $children = buildTree($elements, $element['id']);
      if ($children) {
        $element['children'] = $children;
      }
      $branch[] = $element;
    }
  }

  return $branch;
}

// Main function to get department personnel hierarchy
function get_department_personnel_hierarchy($mysqli, $department_id, $period_id)
{
  // Get all personnel in the department for the period
  $sql = "SELECT * FROM spms_pcr_status WHERE department_id = '$department_id' AND period_id = '$period_id'";
  $res = $mysqli->query($sql);

  $personnel = [];
  $department_head_id = null;

  while ($row = $res->fetch_assoc()) {
    // Store department head ID for reference
    if ($row['DepartmentHead'] && !$department_head_id) {
      $department_head_id = $row['DepartmentHead'];
    }

    $employee_id = $row['employees_id'];
    $parent_id = $row['ImmediateSup'];

    // If ImmediateSup is null, use DepartmentHead as parent
    if (!$parent_id) {
      $parent_id = $row['DepartmentHead'];
    }

    $datum = [
      "id" => $employee_id,
      "parent_id" => $parent_id,
      "name" => get_employee_name($mysqli, $employee_id)
    ];

    $personnel[] = $datum;
  }

  // Sort personnel by name
  $personnel = order_personnel($personnel);

  // Build hierarchical tree starting from department head
  if ($department_head_id) {
    $tree = buildTree($personnel, $department_head_id);

    // Create root node with department head
    $root_node = [
      "id" => $department_head_id,
      "name" => get_employee_name($mysqli, $department_head_id),
      "children" => $tree
    ];

    return $root_node;
  } else {
    // If no department head found, return flat list
    return $personnel;
  }
}
