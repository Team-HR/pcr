<?php

if(isset($_POST['DeleteCreatedUser'])) {
  $id = $_POST['DeleteCreatedUser'];
  $sql = $mysqli->query("DELETE FROM `accounts` WHERE `accounts`.`acc_id` ='$id'");
  if(!$sql){
    echo $mysqli->error;
  }else{
    echo 1;
  }
}else if(isset($_POST['editAccount'])){
  $dataId = $_POST['editAccount'];
  $username = $_POST['username'];
  $privileges = $_POST['privileges'];
  $sql = "UPDATE `accounts` SET `username` ='$username', `type` ='$privileges' WHERE `accounts`.`acc_id` = '$dataId'";
  $sql = $mysqli->query($sql);
  if(!$sql){
    echo $mysqli->error;
  }else{
    echo 1;
  }
}else if(isset($_POST['ResetPassword'])){
  $dataId = $_POST['ResetPassword'];
  $password = password_hash('1234', PASSWORD_DEFAULT);
  $sql = "UPDATE `accounts` SET `password` = '$password' WHERE `accounts`.`acc_id` ='$dataId'";
  $sql = $mysqli->query($sql);
  if(!$sql){
    echo $mysqli->error;
  }else{
    echo 1;
  }
}
?>
