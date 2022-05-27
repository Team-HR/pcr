<?php
require_once "config.php";
if(isset($_POST['page'])){
  $page = $_POST['page'];
  if($page == "viewPending"){
    echo pendingTable($mysqli);
  }elseif($page=='UncriticizedPrTable'){
    $employee = new   Employee_data();
    echo uncriticizedTable($employee);
  }elseif($page=='defualt'){
    echo "defualt";
  }else{
    echo notFound();
  }
}elseif(isset($_POST['unrevRec'])) {
  // nonsense kaayo ni promise
  //ayaw tagda away koy mahimo rong adlawa
  $_SESSION['periodPending'] = $_POST['unrevRec'];
  print(1);
}elseif(isset($_POST['UncriticizedEmpIdPost'])) {
  $_SESSION['empIdPending'] = $_POST['UncriticizedEmpIdPost'];
  print(1);
}elseif(isset($_POST['approvalPost'])){
  $accountId = $_SESSION['emp_id'];
  $dataId = $_POST['approvalPost'];
  $UpdateColumn = "";
  $fetchDataSql = "SELECT * from `spms_performancereviewstatus` where `performanceReviewStatus_id` = '$dataId'";
  $fetchDataSql = $mysqli->query($fetchDataSql);
  $fetchDataSql = $fetchDataSql->fetch_assoc();
  if ($fetchDataSql['PMT']==$accountId){
    $UpdateColumn = 'panelApproved';
  }elseif ($fetchDataSql['DepartmentHead']==$accountId){
    $UpdateColumn = 'certify';
  }elseif($fetchDataSql['ImmediateSup']==$accountId){
      $UpdateColumn = 'approved';
  }
  if($UpdateColumn!=""){
    $date = date('d-m-Y');
    $sql = "UPDATE `spms_performancereviewstatus` SET `$UpdateColumn` = '$date' WHERE `spms_performancereviewstatus`.`performanceReviewStatus_id` = '$dataId'";
    $sql = $mysqli->query($sql);
    if(!$sql){
      die($mysqli->error);
    }else{
      print(1);
    }
  }else{
    echo "Are you lost?";
  }
}
?>