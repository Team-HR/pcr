<?php
require_once "../../libs/config_class.php";
$dataId = explode("|",$_POST['documentationId']);
// echo $_POST['documentationId'];
  // $documentOnwerId = $_SESSION['emp_id'];
$sql = "SELECT * from spms_workdocumentation where employee_id='$dataId[1]' and mfoDataId='$dataId[0]'";
$sql = $mysqli->query($sql);
if($sql->num_rows>0){
  while($dat = $sql->fetch_assoc()){
    echo "
    <div class='item' data-id='$dat[workDocumentationID]' data-target='$_POST[documentationId]'>
    <div class='right floated content'>
    <a href='assets/uploads/$dat[workDocumentationFile]' download>
    <div class='ui button blue'>Show</div>
    </a>
    <div class='ui button red' onclick='removeDocumentationFile()'>Remove</div>
    </div>
    <i class='file icon blue'></i>
    <div class='content'>
    <div class='header'>$dat[workDocumentationTitle]</div>
    $dat[workDocumentationDescription]
    </div>
    </div>
    ";
  }
}else{
  echo "<center><h3 style='color:#00000052'>No Documentation Records Found</h3></center>";
}
?>
