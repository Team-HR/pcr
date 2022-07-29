<?php

$formName = $_POST['agencyName'];
function empData($a)
{
	global $mysqli;
	$emps = "";
	$empSql = "SELECT * from `employees`";
	$empSql = $mysqli->query($empSql);
	while ($getData = $empSql->fetch_assoc()) {
		$val = $getData['employees_id'];
		if ($a) {
			$val = $getData['firstName'] . " " . $getData['middleName'] . " " . $getData["lastName"];
		}
		$emps .= "<option value='$val'>$getData[firstName] $getData[middleName] $getData[lastName]</option>";
	}
	return $emps;
}

function get_nga_signatories()
{
	$data = [];
	global $mysqli;
	$data = $_SESSION;
	return $data;
}

// function get($a)


if ($formName == "LGU") {
	echo "
		<script>

			$('.ui.dropdown').dropdown({
				forceSelection: false,
				fullTextSearch: true,
				clearable: true,
			});
			
			$('.ui.dropdown.headAgency').dropdown({
				forceSelection: false,
				fullTextSearch: true,
				clearable: true,
				allowAdditions: true
			});

		</script>
		<div class='field'>
		<label>Form Type</label>
		<select id='formType' required onchange='reviewFormType()'>
		<option value='1' >IPCR</option>
		<option value='2' >SPCR</option>~
		<option value='3' >DPCR</option>
		<option value='4' >DIVISION Performance Commitment Review</option>
		</select>
		</div>
		<div class='field' >
		<label>Immediate Supervisor</label>
		<select class='ui fluid search dropdown' id='immediateSup' >
		<option value=''></option>
		" . empData(0) . "
		</select>
		</div>
		<div class='field'>
		<label>Department Head</label>
		<select class='ui fluid search dropdown' id='departmentHead'>
		<option value=''></option>
		" . empData(0) . "
		</select>
		</div>
		<div class='field'>
		<label>Head of Agency</label>
		<input type='text' id='headAgency' palceholder='Head of Agency'>
		</div>
		<button type='submit' class='ui fluid primary button'>Next <i class='ui angle double right icon'></i></button>
	";
} else if ($formName == "NGA") {

	echo "
				<div class='field'>
		<label>Form Type</label>
		<select id='formType' required onchange='reviewFormType()'>
		<option value='5' >IPCR</option>
		</select>
		</div>
		<div class='field' >
		<label>Immediate Supervisor</label>
		<input type='text' class='ui fluid' id='immediateSup' required>
		</div>
		<div class='field'>
		<label>Department Head</label>
		<input class='ui fluid' id='departmentHead' required>
		</div>
		<div class='field'>
		<label>Head of Agency</label>
		<input type='text' style='text-transform: uppercase;' id='headAgency' placeholder='Enter Text Here' required>
		</div>
		<button type='submit' class='ui fluid primary button'>Next <i class='ui angle double right icon'></i></button>
	";
}
