<?php
		if(isset($_POST['timeOut'])){
			$_SESSION["emp_id"] = null;
		}else{
			session_start();
			if(session_destroy()){
				header("location:../../index.php");
			}else{
				echo "Something went wrong";
			}
		}
 ?>
