<?php

if (isset($_POST['page'])) {
  $page = $_POST['page'];
  if ($page == "RatingScale") {
    $user->set_period($_SESSION['iMatrix_period']);
    if ($user->core_countTotal > 0) {
      echo $user->RatingScaleTable();
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
  $sql = "SELECT * FROM  department ORDER BY department ASC";
  $res = $mysqli->query($sql);
  $data = [];
  while ($row = $res->fetch_assoc()) {
    $data[] = [
      "id" => $row["department_id"],
      "name" => $row["department"]
    ];
  }
  echo  json_encode($data);
} elseif (isset($_POST["setDepartmentOnPeriod"])) {

  $department_id = $_POST["department_id"];
  $period_id = $_SESSION["iMatrix_period"];
  $employee_id = $_SESSION["emp_id"];

  $sql = "INSERT INTO `spms_performancereviewstatus` (`performanceReviewStatus_id`, `period_id`, `employees_id`, `ImmediateSup`, `DepartmentHead`, `HeadAgency`, `PMT`, `submitted`, `certify`, `approved`, `panelApproved`, `dateAccomplished`, `formType`, `department_id`, `assembleAll`) VALUES (NULL, $period_id, $employee_id, '', '', '', '', '', '', '', '', '', '', '$department_id', '')";
  $res = $mysqli->query($sql);

  echo json_encode($res);
}
