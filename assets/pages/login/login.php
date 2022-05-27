
<!DOCTYPE >
<html>
<head>
	<title>Login</title>
	<meta name="viewport" content="width=device-width, height=device-height , initial-scale=1">
	<link rel="stylesheet" type="text/css" href="assets/libs/ui/dist/semantic.css">
	<script src="assets/libs/jquery/jquery-3.3.1.min.js"></script>
	<script src="assets/libs/ui/dist/semantic.min.js"></script>
	<script src="assets/libs/node_modules/chart.js/dist/Chart.js"></script>
	<script src="assets/libs/node_modules/chart.js/dist/Chart.min.js"></script>
	<script src="assets/libs/umbra.js"></script>
	<style media="screen">
	body{
		background:url('assets/img/mainPage.jpg');
		background-size:cover;
		background-attachment: fixed;
		background-repeat: no-repeat;
		/* color:red; */
	}
	</style>
</head>
<body>
	<div class="ui three column grid">
		<div class="centered column" style="margin-top:10%;">
			<div id="loginAlertMsg">
			</div>
			<div class="ui fluid card">
				<div class="content">
					<h1>Login</h1>
				</div>
				<div class="content">
					<form class="noSubmit" onsubmit="login_log()">
						<div class="ui form">
							<div class="field">
								<label>Username:</label>
								<input style="border:1px solid #4075a9;background:#4075a9;font-weight:bold" type="text" name="user" placeholder="">
							</div>
							<div class="field">
								<label>Password:</label>
								<input style="border:1px solid #4075a9;background:#4075a9;font-weight:bold" type="password" name="pass" placeholder="">
							</div>
							<div class="field">
								<input type="submit" name='submitBtn' class="ui primary button">
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
