<?php
require_once "../../libs/config_class.php";
$removeId = $_POST['removeId'];
$sql = "DELETE FROM `spms_workdocumentation` WHERE `spms_workdocumentation`.`workDocumentationID` = '$removeId'";
$sql = $mysqli->query($sql);
if(!$sql){
  echo "Something went wrong";
}else{
  $img = explode('/',$_POST['imgSrc']);
  $img = $img[count($img)-1];
  unlink("../../uploads/$img");
  echo "DONE";
}
?>
