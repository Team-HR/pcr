<?php
session_start();

require_once "Auth.php";

	if (isset($_POST['timeOut'])) {

		$renew = new Auth();
		$renew->setBasic($_POST['timeOut'], $_POST['pass'], true);
		$renew = $renew->login();
		if ($renew) {
			$_SESSION['emp_id'] = $renew['employees_id'];
			$_SESSION['emp_info'] = $renew;
			print('1');
		} else {
			echo "Wrong password";
		}
	} elseif (isset($_POST['p_user']) && isset($_POST['p_pass'])) {
		# code...
		$user = $_POST['p_user'];
		$pass = $_POST['p_pass'];

		$login = new Auth();
		$login->setBasic($user, $pass);
		$login = $login->login();
		if ($login) {
			$_SESSION['emp_id'] = $login['employees_id'];
			$_SESSION['emp_info'] = $login;
			print('1');
		} else {
			echo "Unable To Login";
		}

	} else {
		echo "page not found";
	}
