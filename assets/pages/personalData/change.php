<?php
require_once "../../libs/config_class.php";
if(isset($_POST['newUsername'])){
  $data = $_POST['dataId'];
  $password = "SELECT * from `spms_accounts` where employees_id='$data'";
  $password = $mysqli->query($password);
  $password = $password->fetch_assoc();
  if(password_verify($_POST['password'],$password['password'])){
    $sql = "UPDATE `spms_accounts` SET `username` = '$_POST[newUsername]' WHERE `spms_accounts`.`employees_id`='$data'";
    $sql = $mysqli->query($sql);
    if(!$sql){
      echo $mysqli->error;
    }else{
      echo 1;
    }
  }else{
    echo "<i style='color:red'>Wrong Password!!</i>";
  }
}else if(isset($_POST['newPassword'])){
  $newPass = $_POST['newPassword'];
  $dataId = $_POST['dataId'];
  $oldPass = $_POST['oldPass'];
  $password = "SELECT * from `spms_accounts` where employees_id='$dataId'";
  $password = $mysqli->query($password);
  $password = $password->fetch_assoc();
  if(password_verify($oldPass,$password['password'])){
    $hashPass = password_hash($newPass, PASSWORD_DEFAULT);
    $sql = "UPDATE `spms_accounts` SET `password` = '$hashPass' WHERE `spms_accounts`.`employees_id` = '$dataId'";
    $sql = $mysqli->query($sql);
    if(!$sql){
      echo "Broken query";
    }else{
      echo 1;
    }
  }else{
    echo "Error password";
  }

}else{
  echo 'broken link';
}
?>
