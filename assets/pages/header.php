<!DOCTYPE>
<html>

<head>
	<title>HR Office</title>
	<link rel="shortcut icon" href="assets/ico/logo.ico" />
	<meta name="viewport" content="width=device-width, height=device-height , initial-scale=1">
	<link rel="stylesheet" type="text/css" href="assets/libs/ui/dist/semantic.css">
	<script src="assets/libs/jquery/jquery-3.3.1.min.js"></script>
	<script src="assets/libs/ui/dist/semantic.min.js"></script>
	<script src="assets/libs/node_modules/chart.js/dist/Chart.js"></script>
	<script src="assets/libs/node_modules/chart.js/dist/Chart.min.js"></script>
	<script src="assets/libs/umbra.js"></script>
	<!-- Addition of Vue3js -->
	<script src="https://unpkg.com/vue@3"></script>

	<style media="screen">
		body {
			background-color: white;
			background: url('assets/img/mainPage.jpg');
			background-size: cover;
			background-repeat: no-repeat;
			background-attachment: fixed;
		}

		table tbody {
			line-height: 25px;
		}

		table {
			background: white;
		}

		tbody td {
			padding: 10px;
			font-size: 14;
		}
	</style>
	<style media="print">
		.noprint {
			display: none !important;
		}

		table {
			font-size: 12px;
		}

		table tr {
			color: black !important;
		}
	</style>
	<script type="text/javascript">
		var interval = setInterval(function() {
			if (document.readyState === 'complete') {
				clearInterval(interval);
				done();
				timer();
			}
		}, 100);
		$(document).ready(function() {
			$('.navOption').dropdown();
			$(".noSubmit").submit(function(event) {
				event.preventDefault()
			});
		});

		function done() {
			$('.dimmer').fadeOut(500);
		}

		function showProfileModal() {
			modalTraget = $('#profileModal').modal('show');
			if (modalTraget) {
				loadPersonalData(modalTraget);
			}
		}
	</script>
</head>
<div class="noprint ui top fixed menu">
	<div class="item">
		<a href="?home"><img src="assets/ico/logo.png" style="width:70px"></a>
	</div>
	<a class="item" href="?performanceRating">Performance Review</a>
	<a class="item" href="?RatingScale">Individual Rating Scale</a>
	<!-- <?= json_encode($user->authorization) ?> -->
	<?php
	if ($user->authorization) {
		for ($index = 0; $index < count($user->authorization); $index++) {
			if (strtoupper($user->authorization[$index]) == strtoupper("reviewer")) {
	?>
				<a class="item" href="?RPC">Review Performance Commitment</a>
			<?php
			} else if (strtoupper($user->authorization[$index]) == strtoupper("Matrix")) {
			?>
				<a class="item" href="?MotherRatingScale">Rating Scale Matrix</a>
			<?php
			} else if (strtoupper($user->authorization[$index]) == strtoupper('pmt')) {
			?>
				<a class="item" href="?PMT">Performance Management Team</a>
			<?php
			} else if (strtoupper($user->authorization[$index]) == strtoupper('HR')) {
			?>
				<a class="item" href="?HR" style="color:blue;"><i class="icon id card outline"></i> HRMO Dashboard</a>
	<?php
			}
		}
	}
	?>

	<a class="item" href="?Browse">Browse Records</a>
	<div class="right menu">
		<div class="ui dropdown item navOption">
			Options <i class="dropdown icon"></i>
			<div class="menu">
				<a class="item" onclick="showProfileModal()"><span style="text-transform:capitalize"><?= $user->get_emp('firstName') ?> <?= $user->get_emp('lastName') ?></span></a>
				<div class="divider"></div>
				<a class="item" onclick="s=3000">Sleep</a>
				<div class="divider"></div>
				<a class="item" href="assets\pages\logout.php">Logout</a>
			</div>
		</div>
	</div>
</div>
<div id="appLoader" class="ui vertical segment">
	<div class="ui active page dimmer" style="height:200%">
		<div class="ui medium text loader" style="top:100px;position:fixed">Loading</div>
	</div>

	<body>
		<br class="noprint">
		<div class="ui mini modal loginPop">
			<div class="content">
				<form class="noSubmit ui form" onsubmit="timeoutForm(<?= $user->get_emp("employees_id") ?>)">
					<div class="ui red message" id='timeErrorMsg' style="display:none">Red</div>
					<h3 class="ui center aligned icon header">
						<i class="circular hourglass end icon"></i>
						<?= $user->get_emp('firstName') ?> <?= $user->get_emp('lastName') ?>
						<div class="sub header">Provide password to confirm your identity</div>
					</h3>
					<div class="field">
						<label>Password</label>
						<input type="password" placeholder="" id='timePass'>
					</div>
					<button class="ui primary fluid button" type="submit">Login</button>
				</form>
			</div>
		</div>
		<div class="ui longer large modal" id="allModal">
			<a class="ui red right corner label" onclick="$('#allModal').modal('hide')">
				<i class="close icon"></i>
			</a>
			<div class="scrolling content" id="modalContL">
				<div style="text-align: center">
					<img src="assets/img/loading.gif" style="transform: scale(.1);height:1000px">
				</div>
			</div>
		</div>
		<div class="ui longer fullscreen modal" id="allModalFull">
			<div class="content" id="contLFull">
				<div style="text-align: center">
					<img src="assets/img/loading.gif" style="transform: scale(.1);height:1000px">
				</div>
			</div>
		</div>
		<div class="ui longer fullscreen modal" id="profileModal" data-target='<?= $user->get_emp('employees_id') ?>'>
			<div class="content" id="modalContLFull">
				<div style="text-align: center">
					<img src="assets/img/loading.gif" style="transform: scale(.1);height:1000px">
				</div>
			</div>
		</div>
		<br class="noprint">
		<br class="noprint">
		<br class="noprint">
		<br class="noprint">