<?php 
	if(isset($_POST['auth'])){
		$username = $_POST['user'];
		$password = $_POST['pass'];
		$sql = "SELECT * from `users`";
		$sql = $mysqli->query($sql);
		while ($arr = $sql->fetch_assoc()) {
			if($arr['username']==$username){
				if(password_verify($password, $arr['password'])){
					$_SESSION['admin'] = $arr['id'];
					echo 1;
					break;
				}else{
					echo "Incorrect Password";
				}
			}else{
				echo 'User not found';
			}
		}
	}else{
 ?>
<!DOCTYPE>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Page</title>
  <link rel="stylesheet" href="assets/libs/custom/umbra.css">
  <link rel="stylesheet" href="assets/libs/css/bootstrap.min.css">
</head>
<body>
	<div class="row" style="padding-top:150px">
		<div class="col-lg-3 col-md-8" style="padding:30px;margin:auto;text-align:center;background:#ffffff7a">
			<img src="assets/image/logo.png" width="50%">
			<br>
			<br>
			<form name="loginForm">
			  <div class="form-group">
			    <input type="text" class="form-control" name="username" placeholder="Username" required>
			  </div>
			  <div class="form-group">
			    <input type="password" class="form-control" name="password" placeholder="Password" required>
			  </div>
			  <button type="submit" class="btn btn-primary" name="submitBtn" style="width:100%">Login</button>
			</form>
		</div>
	</div>
	<script type="text/javascript">
		(function(){
			'use strict';
			document.loginForm.addEventListener('submit',function(e){
				event.preventDefault();
				e.target.elements.submitBtn.disabled = true;
				console.log(e.target.elements);
				$.post('?',{
					auth:true,
					user:e.target.elements.username.value,
					pass:e.target.elements.password.value,
				},function(data,textStatus,xhr){
					if(data=="1"){
						window.location.href = "?";
					}else{
						alert(data);
					}
					e.target.elements.submitBtn.disabled = false;
				});
			});
		})();
	</script>
	<script src="assets/libs/js/jquery-3.4.1.min.js" charset="utf-8"></script>
	<script src="assets/libs/js/bootstrap.min.js" charset="utf-8"></script>
</body>
</html>

<?php
}
?>