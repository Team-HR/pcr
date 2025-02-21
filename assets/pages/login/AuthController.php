<?php

require_once "AuthModal.php";


class AuthController
{
	public function __construct()
	{
	}

	public function renew()
	{
		$renew = new AuthModal();
		$renew->setBasic($_POST['timeOut'], $_POST['pass'], true);
		$renew = $renew->login();
		if ($renew) {
			$_SESSION['emp_id'] = $renew['employees_id'];
			$_SESSION['emp_info'] = $renew;
			print('1');
		} else {
			echo "Wrong password";
		}
	}

	public function login(){

		$user = $_POST['p_user'];
		$pass = $_POST['p_pass'];

		$login = new AuthModal();
		$login->setBasic($user, $pass);
		$login = $login->login();
		if ($login) {
			$_SESSION['emp_id'] = $login['employees_id'];
			$_SESSION['emp_info'] = $login;

			$response = array(
				"data" => $login,
				"status" => "success",
				"message" => "Login Successful"
			);

			echo json_encode($response);
		} else {
			$response = array(
				"status" => "error",
				"message" => "Unable To Login"
			);

			echo json_encode($response);
		}
	}


}


?>