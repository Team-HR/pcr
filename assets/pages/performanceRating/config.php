<?php
function year(){
	$dnow = date('Y')+1;
	$dpast = date('Y')-15;
	$view ="";
	while ($dnow>=$dpast) {
		$view .="<option value='$dnow'>$dnow</option>";
		$dnow--;
	}
	return $view;
}
function signatories($mysqli){
	function empData($mysqli,$id){
		$emps ="";
		if($id==''){
			$id = 0;
		}
		$empSql = "SELECT * from `employees`";
		$empSql = $mysqli->query($empSql);
		while($getData = $empSql->fetch_assoc()){
			if($getData['employees_id']==$id){
				$emps .= "<option value='$getData[employees_id]' selected>$getData[firstName] $getData[lastName]</option>";
			}else{
				$emps .= "<option value='$getData[employees_id]'>$getData[firstName] $getData[lastName]</option>";
			}
		}
		return $emps;
	}
	$sql = "SELECT * from spms_performancereviewstatus where period_id='$_SESSION[period_pr]' and employees_id='$_SESSION[emp_id]'";
	$sql = $mysqli->query($sql);
	$sql = $sql->fetch_assoc();
	$view = "
	<script>
	$(document).ready(function() {
		$('.ui.dropdown').dropdown();
	});
	</script>
	<div class='ui icon message' style='width:50%;padding:20px;margin:auto'>
	<i class='open book icon'></i>
	<div class='content'>
	<div class='header'>
	Agreement
	</div>
	<p>By filling Up this form from Start to Finish means that I, commit to deliver and agree to be rated on the attainment of the following targets in accordance with the indicated measures</p>
	</div>
	</div>
	<form class='ui form' style='width:50%;padding:20px;margin:auto' onsubmit='return signatoriesFunc()'>
	<div class='field'>
	<label>Immediate Supervisor</label>
	<select class='ui fluid search dropdown' id='immediateSup' required>
	<option value=''>Select your Supervisor</option>
	".empData($mysqli,$sql['ImmediateSup'])."
	</select>
	</div>
	<div class='field'>
	<label>Department Head</label>
	<select class='ui fluid search dropdown' id='departmentHead' required>
	<option value=''>Select your Supervisor</option>
	".empData($mysqli,$sql['DepartmentHead'])."
	</select>
	</div>
	<div class='field'>
	<label>Head of Agency</label>
	<input type='text' style='text-transform: uppercase;' id='headAgency' value='$sql[HeadAgency]' required>
	</div>
	<button type='submit' class='ui fluid primary button'>Next <i class='ui angle double right icon'></i></button>
	</form>
	";

	return $view;
}
?>
