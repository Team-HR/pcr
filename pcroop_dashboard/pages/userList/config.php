<?php

if(isset($_POST['DeleteCreatedUser'])) {
  $id = $_POST['DeleteCreatedUser'];
  $stmt = $mysqli->prepare("DELETE FROM accounts WHERE accounts.acc_id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $sql = $stmt->get_result();
  if(!$sql){
    echo $mysqli->error;
  }else{
    echo 1;
  }
  $stmt->close();
}else if(isset($_POST['editAccount'])){
  $dataId = $_POST['editAccount'];
  $username = $_POST['username'];
  $privileges = $_POST['privileges'];
  $stmt = $mysqli->prepare("UPDATE accounts SET username = ?, type = ? WHERE accounts.acc_id = ?");
  $stmt->bind_param("ssi", $username, $privileges, $dataId);
  $sql = $stmt->execute();
  if(!$sql){
    echo $mysqli->error;
  }else{
    echo 1;
  }
  $stmt->close();
}else if(isset($_POST['ResetPassword'])){
  $dataId = $_POST['ResetPassword'];
  $password = password_hash('1234', PASSWORD_DEFAULT);
  $stmt = $mysqli->prepare("UPDATE accounts SET password = ? WHERE accounts.acc_id = ?");
  $stmt->bind_param("si", $password, $dataId);
  $sql = $stmt->execute();
  if(!$sql){
    echo $mysqli->error;
  }else{
    echo 1;
  }
  $stmt->close();
}
?>
