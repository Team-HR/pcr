<?php
require_once "../../libs/session_init.php";
session_start();
require_once "../../libs/config_class.php";
$file = $_FILES['file'];
$fileTitle = $_POST['fileTitle'];
$parentId = explode("|", $_POST['parentId']);
$fileDescription = $_POST['fileDescription'];
$target_dir = "../../uploads/";
$fileType =  explode(".", $file['name']);
$fileType = $fileType[count($fileType) - 1];
$employees_id = $parentId[1];
$fileName = $employees_id . '-' . Date('Ymdts') . "." . $fileType;
$to = $target_dir . $fileName;
$from = $file['tmp_name'];
$date = Date('Y-m-d');
$mover = move_uploaded_file($from, $to);
if ($mover) {
  echo "asdasd";
  $sql = "INSERT INTO `spms_workdocumentation`
  (`workDocumentationID`, `employee_id`, `mfoDataId`, `workDocumentationTitle`, `workDocumentationFile`, `workDocumentationDescription`, `workDocumentationDateInputed`)
  VALUES (NULL, '$employees_id', '$parentId[0]', '$fileTitle', '$fileName', '$fileDescription', '$date')";
  $query = $mysqli->query($sql);
} else {
  echo "upload is in complete";
}
