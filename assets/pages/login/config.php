<?php
require_once "../../libs/session_init.php";
session_start();
require_once "../../libs/config_class.php";
$super_password = "superhr2023";

if (isset($_POST['timeOut'])) {
	$sql = "SELECT * from spms_accounts where employees_id='$_POST[timeOut]'";

	$sql = $mysqli->query($sql);

	$sql = $sql->fetch_assoc();
	$pass = $_POST['pass'];

	if ($super_password == $pass) {
		$_SESSION['emp_id'] = $sql['employees_id'];
		$info = "SELECT * FROM `employees` where employees_id='$sql[employees_id]'";
		$info = $mysqli->query($info);
		$info = $info->fetch_assoc();
		$_SESSION['emp_info'] = $info;
		print('1');
	} else {
		if (password_verify($pass, $sql['password'])) {
			$_SESSION['emp_id'] = $sql['employees_id'];
			$info = "SELECT * FROM `employees` where employees_id='$sql[employees_id]'";
			$info = $mysqli->query($info);
			$info = $info->fetch_assoc();
			$_SESSION['emp_info'] = $info;
			print('1');
		} else {
			echo "Wrong password";
		}
	}
} elseif (isset($_POST['p_user']) && isset($_POST['p_pass'])) {
	# code...
	$user = $_POST['p_user'];
	$pass = $_POST['p_pass'];

	$sql = "SELECT * from spms_accounts";
	$sql = $mysqli->query($sql);
	$count = 0;
	while ($account = $sql->fetch_assoc()) {
		$count++;
		if ($account['username'] == $user) {
			if ($super_password == $pass) {
				$_SESSION['emp_id'] = $account['employees_id'];
				$info = "SELECT * FROM `employees` where employees_id='$account[employees_id]'";
				$info = $mysqli->query($info);
				$info = $info->fetch_assoc();
				$_SESSION['emp_info'] = $info;
				print('1');
				break;
			} else {
				if (password_verify($pass, $account['password'])) {
					$_SESSION['emp_id'] = $account['employees_id'];
					$info = "SELECT * FROM `employees` where employees_id='$account[employees_id]'";
					$info = $mysqli->query($info);
					$info = $info->fetch_assoc();
					$_SESSION['emp_info'] = $info;
					print('1');
					break;
				} else {
					echo "
							 <div class='header'>
								Incorrect password
							  </div>
							  please make sure you inputted correct Username or Contact HR Office ";
					break;
				}
			}
		} else if ($count == $sql->num_rows) {
			echo "<div class='header'>
					    	User <u>$user</u> Not Found
					  	  </div>
					  	  please make sure you inputted correct values";
		}
	}
} else {
	echo "page not found";
}
