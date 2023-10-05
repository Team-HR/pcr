<?php
if (isset($_POST['createUser'])) {
  $userType = $_POST['createUser'];
  $username = $_POST['username'];
  $employee = $_POST['employee'];
  $password = password_hash('1234', PASSWORD_DEFAULT);
  if($employee===""||$username===""){
    echo("Empty Fields");
  }else{
    $check = "SELECT * from accounts where employees_id = '$employee'";
    $check = $mysqli->query($check);
    if ($check->num_rows) {
      echo "Already has an account";
    }else{
      $check = "SELECT * from accounts where  username= '$username'";
      $check = $mysqli->query($check);
      if($check->num_rows){
        echo "Username is already taken";
      }else{
        $sql = "INSERT INTO `accounts`
        (`acc_id`, `employees_id`, `username`, `password`, `type`)
        VALUES (NULL, '$employee', '$username', '$password', '$userType')";
        $sql = $mysqli->query($sql);
        if(!$sql){
          die($mysqli->error);
        }else{
          print(1);
        }
      }
    }
  }
}
?>
