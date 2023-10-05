<?php

if (isset($_POST['page'])) {
  $page = $_POST['page'];
  if ($page == "RatingScale") {
    $user->set_period($_SESSION['iMatrix_period']);
    if ($user->core_countTotal > 0) {
      echo "ok";
      // echo $user->RatingScaleTable();
    } else {
      echo "error";
    }
  }
} elseif (isset($_POST['period_check'])) {
  $month = $_POST['period_check'];
  $year = $_POST['year'];
  $sql = "SELECT * from spms_mfo_period where month_mfo='$month' and year_mfo='$year'";
  $sql = $mysqli->query($sql);
  if (!$sql) {
    die($mysqli->error);
  } else {
    $sql = $sql->fetch_assoc();
    $_SESSION['iMatrix_period'] = $sql['mfoperiod_id'];
    $_SESSION['period_pr'] = $sql['mfoperiod_id'];
    print(1);
  }
} elseif (isset($_POST["getListOfDepartments"])) {
  $data = [];
  $data["period"] = "";
  $data["departments"] = [];
  $mfoperiod_id = $_SESSION["iMatrix_period"];
  $sql = "SELECT * FROM spms_mfo_period WHERE mfoperiod_id = '$mfoperiod_id'";
  $res = $mysqli->query($sql);

  if ($row = $res->fetch_assoc()) {
    $data["period"] = $row["month_mfo"] . ", " . $row["year_mfo"];
  }

  $sql = "SELECT * FROM  department ORDER BY department ASC";
  $res = $mysqli->query($sql);
  while ($row = $res->fetch_assoc()) {
    $data["departments"][] = [
      "id" => $row["department_id"],
      "name" => $row["department"]
    ];
  }

  echo  json_encode($data);
} elseif (isset($_POST["setDepartmentOnPeriod"])) {

  $department_id = $_POST["department_id"];
  $period_id = $_SESSION["iMatrix_period"];
  $employee_id = $_SESSION["emp_id"];


  $sql = "SELECT * FROM spms_performancereviewstatus WHERE period_id = '$period_id' AND employees_id = '$employee_id'";
  $res = $mysqli->query($sql);

  if ($res->num_rows < 1) {
    $sql = "INSERT INTO `spms_performancereviewstatus` (`performanceReviewStatus_id`, `period_id`, `employees_id`, `ImmediateSup`, `DepartmentHead`, `HeadAgency`, `PMT`, `submitted`, `certify`, `approved`, `panelApproved`, `dateAccomplished`, `formType`, `department_id`, `assembleAll`) VALUES (NULL, $period_id, $employee_id, '', '', '', '', '', '', '', '', '', '', '$department_id', '1')";
    $res = $mysqli->query($sql);
  } elseif ($res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $id = $row["performanceReviewStatus_id"];
    $sql = "UPDATE spms_performancereviewstatus SET department_id = '$department_id' WHERE performanceReviewStatus_id = '$id'";
    $mysqli->query($sql);
  }

  echo json_encode($res);
} elseif (isset($_POST["view"])) {
  $user->set_period($_SESSION['iMatrix_period']);
  echo $user->RatingScaleTable();
}
