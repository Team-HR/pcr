<?php

if (isset($_GET['year']) && isset($_GET['period']) && $_GET['year'] != "" && $_GET['period'] != "") {
	$year = $_GET['year'];
	$period = $_GET['period'];
} else {
	$year = date('Y');
	$period = "";
	if (date('m') >= 7) {
		$period = "January - June";
	} else {
		$period = "July - December";
		$year--;
	}
}
// $period = "July - December";
$periodSql = "SELECT * from spms_mfo_period where `month_mfo`='$period' and `year_mfo`='$year'";
$periodSql = $mysqli->query($periodSql);
$periodSql = $periodSql->fetch_assoc();
$userId = $user->get_emp('employees_id');
$sql = "SELECT * FROM `spms_departmentassignedtopmt` left join `department` on 
			`spms_departmentassignedtopmt`.`department_id`=`department`.`department_id`
			where `employees_id`='$userId'";
$sql = $mysqli->query($sql);
$card = "";
while ($data = $sql->fetch_assoc()) {
	$card .= "
			  <div class='card' >
			    <div class='image'>
			      <img src='assets/img/folder.jpg' style='width:50%;margin:auto'>
			    </div>
			    <div class='content'>
			      <div class='header'>$data[department]</div>
			      <div class='meta'>
			        <a>Name of Department</a>
			      </div>
			    </div>
			     <div class='extra'>
			     	<button class='fluid ui primary button openBtn' data-target='$data[department_id]'>open</button>
					 <br>
			     	<button class='fluid ui secondary button openRsm' data-target='$data[department_id]'>Show RSM</button>
			    </div>
			  </div>
		";
}
?>
<div style="text-align:center">
	<br>
	<h1><?= $period ?> <?= $year ?></h1>
</div>
<center>
	<div id='content'>
		<form method="GET" action="index.php?PMT" style="width:20%;margin:auto" class="ui form">
			<div class="field">
				<div class="two fields">
					<div class="field">
						<input type="hidden" name="PMT">
						<select name="period">
							<option value="January - June">January - June</option>
							<option value="July - December" <?= ($period == "July - December") ? "selected" : "" ?>>
								July - December</option>
						</select>
					</div>
					<div class="field">
						<input type="number" name="year" value="<?= $year ?>" placeholder="Year">
					</div>
					<button class="ui button">Go</button>
				</div>
			</div>
		</form>
		<br>
		<br>
		<br>
		<div class="ui centered link cards">
			<?= $card ?>
		</div>
	</div>
</center>
<script type="text/javascript">
	(function() {
		'use strict';
		var openBtn = document.getElementsByClassName("openBtn");
		var openRsm = document.getElementsByClassName("openRsm");
		var countCards = 0;
		while (countCards < openBtn.length) {
			openBtn[countCards].addEventListener('click', cardsFunction);
			openRsm[countCards].addEventListener('click', openRsmFunction);
			countCards++;
		}

		function cardsFunction() {
			$('.segment').dimmer('show');
			var dataTarget = event.target.attributes['data-target'].value;
			$.post('?config=PMT', {
				showDepartmentFiles: true,
				departmentId: dataTarget,
				period: <?= $periodSql['mfoperiod_id'] ?>
			}, function(data, textStatus, xhr) {
				document.getElementById('content').innerHTML = data;
				var count = 0;
				var openFile = document.getElementsByClassName('openFile');
				console.log(openFile);
				var countBtn = 0;
				while (countBtn < openFile.length) {
					openFile[countBtn].addEventListener('click', openFileFunc);
					countBtn++; 
				}
				$('.segment').dimmer('hide');
			});
		}

		function openRsmFunction() {
			var dataTarget = event.target.attributes['data-target'].value;
			var period = <?= $periodSql['mfoperiod_id'] ?>;
			window.open("?showRsmView&period=" + period + "&department=" + dataTarget, "_blank");
		}

		function openFileFunc() {
			$('.segment').dimmer('show');
			var dataId = event.target.attributes['data-target'].value;
			$.post('?config=PMT', {
				viewFile: true,
				dataId: dataId,
			}, function(data, textStatus, xhr) {
				document.getElementById('content').innerHTML = data;
				$('.segment').dimmer('hide');
			});
		}
	})();
</script>