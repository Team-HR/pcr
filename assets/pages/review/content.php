<?php
require_once "config.php";
if (isset($_POST['page'])) {
  $page = $_POST['page'];
  if ($page == "viewPending") {
    echo pendingTable($mysqli);
  } elseif ($page == 'viewTopPerformers') {
    // $employee = new   Employee_data();
    echo json_encode("test");
  } elseif ($page == 'UncriticizedPrTable') {
    $employee = new   Employee_data();
    echo uncriticizedTable($employee);
  } elseif ($page == 'defualt') {
    echo "defualt";
  } else {
    echo notFound();
  }
} elseif (isset($_POST['unrevRec'])) {
  // nonsense kaayo ni promise
  //ayaw tagda away koy mahimo rong adlawa
  $_SESSION['periodPending'] = $_POST['unrevRec'];
  print(1);
} elseif (isset($_POST['UncriticizedEmpIdPost'])) {
  $employee_id = $_SESSION['empIdPending'] = $_POST['UncriticizedEmpIdPost'];
  $period_id =  $_SESSION['periodPending'];
  $sql = "SELECT * from `spms_performancereviewstatus` WHERE `period_id` = $period_id AND `employees_id` = $employee_id";
  $res = $mysqli->query($sql);
  $row = $res->fetch_assoc();
  $_SESSION['fileStatusPending'] = $row;
  print(1);
} elseif (isset($_POST['approvalPost'])) {
  $accountId = $_SESSION['emp_id'];
  $dataId = $_POST['approvalPost'];
  $UpdateColumn = "";
  $fetchDataSql = "SELECT * from `spms_performancereviewstatus` where `performanceReviewStatus_id` = '$dataId'";
  $fetchDataSql = $mysqli->query($fetchDataSql);
  $fetchDataSql = $fetchDataSql->fetch_assoc();

  $date = date('d-m-Y');
  if ($fetchDataSql['PMT'] == $accountId) {
    $UpdateColumn = "`panelApproved` = '$date'";
  } elseif ($fetchDataSql['ImmediateSup'] == $accountId and $fetchDataSql['DepartmentHead'] == $accountId) {
    $UpdateColumn = "`approved` = '$date', `certify` = '$date'";
  } elseif ($fetchDataSql['ImmediateSup'] == "" and $fetchDataSql['DepartmentHead'] == $accountId) {
    $UpdateColumn = "`approved` = '$date', `certify` = '$date'";
  } elseif ($fetchDataSql['DepartmentHead'] == $accountId) {
    $UpdateColumn = "`certify` = '$date'";
    if ($fetchDataSql['employees_id'] == $fetchDataSql['ImmediateSup']) {
      $UpdateColumn = "`approved` = '$date', `certify` = '$date'";
    }
  } elseif ($fetchDataSql['ImmediateSup'] == $accountId) {
    $UpdateColumn = "`approved` = '$date'";
  }
  if ($UpdateColumn != "") {
    $sql = "UPDATE `spms_performancereviewstatus` SET $UpdateColumn WHERE `spms_performancereviewstatus`.`performanceReviewStatus_id` = '$dataId'";
    $sql = $mysqli->query($sql);
    if (!$sql) {
      die($mysqli->error);
    } else {
      print(1);
    }
  } else {
    echo "Are you lost?";
  }
}
