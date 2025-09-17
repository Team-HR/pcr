<?php
if(isset($_POST['pcrBrowseView'])){
  $emp = $_POST['emp'];
  $period = $_POST['period'];
  $year = $_POST['year'];
  $employee_browse = new Employee_data();
  $employee_browse->set_emp($emp);
  $employee_browse->set_periodMY($period,$year);
  $status = $employee_browse->fileStatus;
  $employee_browse->hideNextBtn();
  // if($status['approved']==""||$status==null){
    if($status==null){
    echo "
    <br>
    <br>
    <br>
    <h2 class='ui center aligned icon header'>
    <i class='ui red exclamation triangle icon'></i>
    <div class='content'>
    No Approved Record Found
    <div class='sub header'>".$employee_browse->get_emp('firstName')." ".$employee_browse->get_emp('lastName')." ".$employee_browse->get_emp('extName')."has no Approved record found as of ".$employee_browse->get_period('month_mfo')." ".$employee_browse->get_period('year_mfo')."</div>
    </div>
    </h2>
    ";
  }else{
    echo "<center>";
    echo "<h2 class='noprint'>Showing the Record of ".$employee_browse->get_emp('firstName')." ".$employee_browse->get_emp('lastName')." ".$employee_browse->get_emp('extName') ;
    echo "<br>as of";
    echo "<br>".$employee_browse->get_period('month_mfo')." ".$employee_browse->get_period('year_mfo')."<br><br><br></h2>";
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
}else{
  echo notFound();
}
?>
