<?php
require_once "assets/libs/PcrForm.php";

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
} elseif (isset($_POST["initLoad"])) {
	$tableData = "";
	$department_id = $_POST['department_id'];
	$query = "SELECT * from `department` where `department_id`='$department_id'";
	$res = $mysqli->query($query);
	$row = $res->fetch_assoc();
	$department = $row["department"];

	$period_id = $_POST['period_id'];

	// get period information
	$period_info = getPeriodInformation($mysqli, $period_id);
	// FETCH all validated spcr and ipcr 
	// fetch all dpcr
	// dpcr query 
	$query = "SELECT * FROM `spms_performancereviewstatus` where `period_id` = '$period_id' and `department_id` = '$department_id'";
	$sqlDpcr = $mysqli->query($query);

	$data = [];

	while ($row = $sqlDpcr->fetch_assoc()) {
		$formOwner = new Employee_data();
		$formOwner->set_emp($row['employees_id']);
		$fullName = $formOwner->get_emp('lastName') . " " . $formOwner->get_emp('firstName') . " " . $formOwner->get_emp('middleName');

		$formType = "";
		$form_type = $row["formType"];
		if ($form_type == 1) {
			$formType = "IPCR";
		} elseif ($form_type == 2) {
			$formType = "SPCR";
		} elseif ($form_type == 3) {
			$formType = "DPCR";
		} elseif ($form_type == 4) {
			$formType = "DIVISION SPCR";
		} else {
			$formType = "IPCR (NGA)";
		}
		$item = [
			"id" => $row["performanceReviewStatus_id"],
			"name" => $fullName,
			"form_type" => $row["formType"],
			"formType" => $formType,
			"date_submitted" => $row["dateAccomplished"],
			"date_approved" => $row["approved"],
			"date_certified" => $row["certify"],
		];

		$data[] = $item;
	}

	echo json_encode([
		"data" => $data,
		"department" => $department,
		"period" => $period_info["period"],
		"year" => $period_info["year"]
	]);
} else if (isset($_POST["viewFile"])) {
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
} elseif (isset($_POST['getDepartments'])) {
	// $period = $_POST["period"];
	// $year = $_POST["year"];
	$data = [];
	// $sql = "SELECT * from spms_mfo_period where `month_mfo`='$period' and `year_mfo`='$year'";
	// $res = $mysqli->query($sql);
	// $periodSql = $res->fetch_assoc();
	$employee_id_auth = $user->get_emp('employees_id');
	$sql = "SELECT * FROM `spms_departmentassignedtopmt` left join `department` on 
			`spms_departmentassignedtopmt`.`department_id`=`department`.`department_id`
			where `employees_id`='$employee_id_auth'";
	$res = $mysqli->query($sql);
	// echo json_encode($employee_id_auth);
	while ($row = $res->fetch_assoc()) {
		$data[] = $row;
	}
	echo json_encode($data);
} elseif (isset($_POST['getPeriodId'])) {
	$period = $_POST["period"];
	$year = $_POST["year"];
	$sql = "SELECT * from spms_mfo_period where `month_mfo`='$period' and `year_mfo`='$year'";
	$res = $mysqli->query($sql);
	$row = $res->fetch_assoc();
	$period_id = $row['mfoperiod_id'];
	$_SESSION['PMT'] = [
		'period' => $period,
		'year' => $year,
		'period_id' => $period_id
	];
	echo json_encode($period_id);
} elseif (isset($_POST["initSession"])) {
	$data = isset($_SESSION['PMT']) ? $_SESSION['PMT'] : null;
	echo json_encode($data);
}
// show form 
elseif (isset($_POST["initLoadForm"])) {
	$id = $_POST["id"];
	$sql = "SELECT * FROM `spms_performancereviewstatus` WHERE `performanceReviewStatus_id` = '$id'";
	$res = $mysqli->query($sql);
	$row = $res->fetch_assoc();
	$period_id = $row["period_id"];
	$period_info = getPeriodInformation($mysqli, $period_id);

	$pcr_form = new PcrForm($mysqli);
	$pcr_form->set_file_status_id($id);

	echo json_encode([
		"file_status" => $pcr_form->fileStatus,
		"strategic_function" => $pcr_form->get_strategic_function(),
		"period" => $period_info["period"],
		"year" => $period_info["year"],
		"form_type" => $pcr_form->get_form_type(),
		"core_functions" => $pcr_form->get_core_functions(),
		"support_functions" => $pcr_form->get_support_functions(),
		"comments_and_reccomendations" => $pcr_form->get_comments_and_reccomendations(),
		"overall_final_rating" => $pcr_form->get_overall_final_rating(),
		"isApproved" => $pcr_form->get_is_approved(),
	]);
} elseif (isset($_POST["setCommentSupport"])) {

	$sfd_id = $_POST["sfd_id"];
	$commentor = $_POST["commentor"];
	$comments = $_POST["comments"];

	// array (
	// 	'IS' => '',
	// 	'DH' => '',
	// 	'PMT' => '',
	//   )

	// check first if sfd_id exists 
	$sql = "SELECT * FROM `spms_supportfunctiondata` WHERE `sfd_id` = '$sfd_id'";
	$res = $mysqli->query($sql);

	// if none return null
	if ($res->num_rows == 0) {
		echo json_encode(null);
		return null;
	}
	// if exists insert/updatAe
	$row = $res->fetch_assoc();
	$critics = $row["critics"];

	// check if IS DH PMT serial exist
	// if exist unserialize and update existing
	if ($critics) {
		$critics = unserialize($critics);
		// if ($comments) {
		// $critics["IS"] = $comments;
		// $critics["DH"] = $comments;
		$critics["PMT"] = $comments;
		$critics = serialize($critics);
		$critics = $mysqli->real_escape_string($critics);
		$sql = "UPDATE `spms_supportfunctiondata` SET `critics` = '$critics' WHERE `spms_supportfunctiondata`.`sfd_id` = '$sfd_id';";
		$mysqli->query($sql);
		echo  json_encode($critics);
		return null;
		// }
	}
	// if none create with new commentor and comment object then serialize it
	else {
		$critics = [
			"IS" => "",
			"DH" => "",
			"PMT" => $comments
		];
		$critics = serialize($critics);
		$critics = $mysqli->real_escape_string($critics);
		$sql = "UPDATE `spms_supportfunctiondata` SET `critics` = '$critics' WHERE `spms_supportfunctiondata`.`sfd_id` = '$sfd_id';";
		$mysqli->query($sql);
		echo  json_encode($critics);
		return null;
	}
} elseif (isset($_POST["setComment"])) {

	$cfd_id = $_POST["cfd_id"];
	$commentor = $_POST["commentor"];
	$comments = $_POST["comments"];

	// array (
	// 	'IS' => '',
	// 	'DH' => '',
	// 	'PMT' => '',
	//   )

	// check first if cfd_id exists 
	$sql = "SELECT * FROM `spms_corefucndata` WHERE `cfd_id` = '$cfd_id'";
	$res = $mysqli->query($sql);

	// if none return null
	if ($res->num_rows == 0) {
		echo json_encode(null);
		return null;
	}
	// if exists insert/updatAe
	$row = $res->fetch_assoc();
	$critics = $row["critics"];

	// check if IS DH PMT serial exist
	// if exist unserialize and update existing
	if ($critics) {
		$critics = unserialize($critics);
		// if ($comments) {
		// $critics["IS"] = $comments;
		// $critics["DH"] = $comments;
		$critics["PMT"] = $comments;
		$critics = serialize($critics);
		$critics = $mysqli->real_escape_string($critics);
		$sql = "UPDATE `spms_corefucndata` SET `critics` = '$critics' WHERE `spms_corefucndata`.`cfd_id` = '$cfd_id';";
		$mysqli->query($sql);
		echo  json_encode($critics);
		return null;
		// }
	}
	// if none create with new commentor and comment object then serialize it
	else {
		$critics = [
			"IS" => "",
			"DH" => "",
			"PMT" => $comments
		];
		$critics = serialize($critics);
		$critics = $mysqli->real_escape_string($critics);
		$sql = "UPDATE `spms_corefucndata` SET `critics` = '$critics' WHERE `spms_corefucndata`.`cfd_id` = '$cfd_id';";
		$mysqli->query($sql);
		echo  json_encode($critics);
		return null;
	}
} elseif (isset($_POST["doApprove"])) {
	$performanceReviewStatus_id = $_POST["id"];
	$current_date = date("d-m-Y");
	$sql = "UPDATE `spms_performancereviewstatus` SET `panelApproved` = '$current_date' WHERE `performanceReviewStatus_id` = '$performanceReviewStatus_id'";
	echo json_encode($mysqli->query($sql));
}



function getPeriodInformation($mysqli, $period_id)
{
	$sql = "SELECT * FROM `spms_mfo_period` WHERE `mfoperiod_id` = '$period_id'";
	$res = $mysqli->query($sql);
	$row = $res->fetch_assoc();
	$period = $row["month_mfo"];
	$year = $row["year_mfo"];
	return [
		"period" => $period,
		"year" => $year,
	];
}


function getFormData($mysqli, $id)
{
	$data = [];



	return $data;
}


?>