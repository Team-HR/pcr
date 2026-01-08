<?php

if (isset($_POST['showList'])) {

  $department_id = 8;
  $period = $_POST['period'];
  $year = $_POST['year'];


  $sql = "SELECT * FROM spms_mfo_period WHERE month_mfo = '$period' AND year_mfo = '$year'";
  $res = $mysqli->query($sql);

  if ($row = $res->fetch_assoc()) {
    echo $row['mfoperiod_id'];
  }

  // echo json_encode(null);
} elseif (isset($_POST['pcrBrowseView'])) {
  $emp = $_POST['emp'];
  $period = $_POST['period'];
  $year = $_POST['year'];
  $employee_browse = new Employee_data();
  $employee_browse->set_emp($emp);
  $employee_browse->set_periodMY($period, $year);
  $employee_browse->hideCol = true; // hide action buttons
  $status = $employee_browse->fileStatus;
  $employee_browse->hideNextBtn();
  // if($status['approved']==""||$status==null){
  if ($status == null) {
    echo "
    <br>
    <br>
    <br>
    <h2 class='ui center aligned icon header'>
    <i class='ui red exclamation triangle icon'></i>
    <div class='content'>
    No Approved Record Found
    <div class='sub header'>" . $employee_browse->get_emp('firstName') . " " . $employee_browse->get_emp('lastName') . " " . $employee_browse->get_emp('extName') . "has no Approved record found as of " . $employee_browse->get_period('month_mfo') . " " . $employee_browse->get_period('year_mfo') . "</div>
    </div>
    </h2>
    ";
  } else {
    echo "<center>";
    echo "<h2 class='noprint'>Showing the Record of " . $employee_browse->get_emp('firstName') . " " . $employee_browse->get_emp('lastName') . " " . $employee_browse->get_emp('extName');
    echo "<br>as of";
    echo "<br>" . $employee_browse->get_period('month_mfo') . " " . $employee_browse->get_period('year_mfo') . "<br><br><br></h2>";
    echo "</center>";
    $table_browse = new table($employee_browse->hideCol);
    $table_browse->formType($employee_browse->get_status('formType'));
    $table_browse->set_head($employee_browse->tableHeader());
    $table_browse->set_body($employee_browse->get_strategicView());
    $table_browse->set_body($employee_browse->get_coreView());
    $table_browse->set_body($employee_browse->get_supportView());
    $table_browse->set_foot($employee_browse->tableFooter());
    echo $table_browse->_get();
  }
} elseif (isset($_POST['pcrBrowseView2'])) {

  $period_id = $_POST['period_id'];
  $emp = $_POST['emp'];

  $employee_browse = new Employee_data();
  $employee_browse->hideCol = true;
  $semester = $employee_browse->get_period_and_year($period_id);

  $period = $semester['month'];
  $year = $semester['year'];

  $employee_browse->set_emp($emp);
  $employee_browse->set_periodMY($period, $year);
  $status = $employee_browse->fileStatus;
  $employee_browse->hideNextBtn();
  // if($status['approved']==""||$status==null){
  if ($status == null) {
    echo "
    <br>
    <br>
    <br>
    <h2 class='ui center aligned icon header'>
    <i class='ui red exclamation triangle icon'></i>
    <div class='content'>
    No Approved Record Found
    <div class='sub header'>" . $employee_browse->get_emp('firstName') . " " . $employee_browse->get_emp('lastName') . " " . $employee_browse->get_emp('extName') . "has no Approved record found as of " . $employee_browse->get_period('month_mfo') . " " . $employee_browse->get_period('year_mfo') . "</div>
    </div>
    </h2>
    ";
  } else {
    echo "<center>";
    echo "<h2 class='noprint'>Showing the Record of " . $employee_browse->get_emp('firstName') . " " . $employee_browse->get_emp('lastName') . " " . $employee_browse->get_emp('extName');
    echo "<br>as of";
    echo "<br>" . $employee_browse->get_period('month_mfo') . " " . $employee_browse->get_period('year_mfo') . "<br><br><br></h2>";
    echo "</center>";
    $table_browse = new table(true);
    $table_browse->formType($employee_browse->get_status('formType'));
    $table_browse->set_head($employee_browse->tableHeader());
    $table_browse->set_body($employee_browse->get_strategicView());
    $table_browse->set_body($employee_browse->get_coreView());
    $table_browse->set_body($employee_browse->get_supportView());
    $table_browse->set_foot($employee_browse->tableFooter());
    echo $table_browse->_get();
  }
}

// elseif (isset($_POST["getDepartmentPcrs"])) {
//   # code...
// }

else {
  echo notFound();
}
