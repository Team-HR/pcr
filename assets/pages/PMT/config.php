<?php
if (isset($_POST['showDepartmentFiles'])) {
	$tableData = "";
	$department = $_POST['departmentId'];
	$depQuery = "SELECT * from `department` where `department_id`=$department";
	$depQuery = $mysqli->query($depQuery);
	$depQuery = $depQuery->fetch_assoc();
	$period_id = $_POST['period'];
	// FETCH all validated spcr and ipcr 
	// fetch all dpcr
	// dpcr query 
	$sqlDpcr = "SELECT * FROM `spms_performancereviewstatus` where 
		`period_id` = '$period_id' and `department_id` = '$department'";
	$sqlDpcr = $mysqli->query($sqlDpcr);
	while ($data = $sqlDpcr->fetch_assoc()) {
		$formOwner = new Employee_data();
		$formOwner->set_emp($data['employees_id']);
		$fullName = $formOwner->get_emp('lastName') . " " . $formOwner->get_emp('firstName') . " " . $formOwner->get_emp('middleName');
		if ($data['formType'] == 1) {
			if ($data['submitted'] == 'Done') {
				if ($data['approved'] != '') {
					$tableData .= "<tr>
									      <td>
										    <i class='file outline icon'></i>IPCR
									      </td>
									      <td>$fullName</td>
									      <td class='right aligned'>
									      	<button class='ui primary button openFile' data-target='$data[performanceReviewStatus_id]'>Open</button>
									      </td>
									    </tr>";
				}
			}
		} else if ($data['formType'] == 2 || $data['formType'] == 4) {
			if ($data['submitted'] == 'Done') {
				if ($data['certify'] != '') {
					$tableData .= "<tr>
										<td>
											<i class='file outline icon'></i>SPCR
										</td>
										<td>$fullName</td>
										<td class='right aligned'>
											<button class='ui primary button openFile' data-target='$data[performanceReviewStatus_id]'>Open</button>
										</td>
										</tr>";
				}
			}
		} else {
			if ($data['submitted'] == 'Done') {
				$tableData .= "<tr>
								      <td>
									    <i class='file outline icon'></i>DPCR
								      </td>
								      <td>$fullName</td>
								      <td class='right aligned'>
								      	<button class='ui primary button openFile' data-target='$data[performanceReviewStatus_id]'>Open</button>
								      </td>
								    </tr>";
			}
		}
	}
?>
	<div style="text-align:center">
		<h2><?= $depQuery['department'] ?></h2>
	</div>
	<br>
	<br>
	<br>
	<div style="width:90%;margin:auto">
		<div class="ui raised segment">
			<?php
			if ($tableData == "") {
			?>
				<br>
				<br>
				<h1 style="text-align:center;color:#1515159e">No submitted files</h1>
				<br>
				<br>
			<?php
			} else {
			?>
				<table class="ui celled striped table">
					<thead>
						<tr>
							<th colspan="3">
								Files
							</th>
						</tr>
					</thead>
					<tbody>
						<?= $tableData ?>
					</tbody>
				</table>
			<?php
			}
			?>
		</div>
	</div>
<?php
} else if (isset($_POST['viewFile'])) {
	$sql = "SELECT * from `spms_performancereviewstatus` WHERE `performanceReviewStatus_id`='$_POST[dataId]'";
	$sql = $mysqli->query($sql);
	$sql = $sql->fetch_assoc();
	$_SESSION['empIdPending'] = $sql['employees_id'];
	$_SESSION['periodPending'] = $sql['period_id'];
	// $_SESSION['deptIdPending'] = $sql['department_id'];
	if ($sql['PMT'] == 0 || $sql['PMT'] == '0') {
		$empId = $user->get_emp('employees_id');
		$sqlNullRep = "UPDATE `spms_performancereviewstatus` SET `PMT` = '$empId' WHERE `spms_performancereviewstatus`.`performanceReviewStatus_id`='$sql[performanceReviewStatus_id]'";
		$mysqli->query($sqlNullRep);
	}
	$formData = new Employee_data();
	$formData->set_emp($sql['employees_id']);
	$formData->set_period($sql['period_id']);
	$formData->hideNextBtn();
	$formData->set_hide("display:none");
	$rows = $formData->get_strategicView() . $formData->get_coreView() . $formData->get_supportView();
	// create table
	$pmtTable = new table($formData->hideCol);
	$pmtTable->formType($sql['formType']);
	$pmtTable->set_head($formData->tableHeader());
	$pmtTable->set_body($rows);
	$pmtTable->set_foot($formData->tableFooter() . "<br class='noprint'>" . $formData->get_approveBTN());
	// show table
	echo "<div id='Reviewcontent'>";
	echo $pmtTable->_get();
	echo "</div>";
}
?>