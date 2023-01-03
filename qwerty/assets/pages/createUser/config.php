<?php
    if(isset($_POST['getEmp'])){
        $ar = [];
        $sql = "SELECT * from `employees`";
        $sql = $mysqli->query($sql);
        while($arr = $sql->fetch_assoc()){
            $check = "SELECT * from `spms_accounts` where `employees_id`='$arr[employees_id]'";
            $check = $mysqli->query($check);
            if(!$check->num_rows){
                $ar[] = $arr;
            }
        }
        echo json_encode($ar);
    }elseif(isset($_POST['createAccount'])){
        $username = $_POST['username'];
        $userId = $_POST['userId'];
        $password = password_hash("1234", PASSWORD_DEFAULT);
        $sql = "INSERT INTO `spms_accounts` (`acc_id`, `employees_id`, `username`, `password`, `type`)
        VALUES (NULL, '$userId', '$username', '$password', '')";
        $sql = $mysqli->query($sql);
        if(!$sql){
            echo $mysqli->error;
        }else{
            echo 1;
        }
    }



?>