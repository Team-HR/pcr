<?php
require_once "assets/libs/FinalNumericalRatings.php";
// $finalNumericalRating = new FinalNumericalRating();
/*
$fileStatus = [
    'performanceReviewStatus_id' => '',
    'period_id' => 11,
    'employees_id' => 9,
    'ImmediateSup' => '',
    'DepartmentHead' => '',
    'HeadAgency' => '',
    'PMT' => '',
    'submitted' => '',
    'certify' => '',
    'approved' => '',
    'panelApproved' => '',
    'dateAccomplished' => '',
    'formType' => '1',
    'department_id' => '32',
    'assembleAll' => '',
];


$strategic = strategicTr($mysqli, $fileStatus);
$core = coreRow($mysqli, $fileStatus);
$support = supportFunctionTr($mysqli, $fileStatus);

$final_numerical_rating = '';
if ($strategic > 0 && $core > 0 && $support > 0) {
    $final_numerical_rating = $strategic + $core + $support;
}


print "strategic => " . $strategic;
print "<br/>";
print "core =>  " . $core;
print "<br/>";
print "support => " . $support;
print "<br/>";
print "final => " . $final_numerical_rating;


*/


if (isset($_POST['getDepartmentItems'])) {
	$data = [];
	$sql = "SELECT * FROM `department` WHERE department_id != 28 ORDER BY department ASC;";
	$res = $mysqli->query($sql);
	while ($row = $res->fetch_assoc()) {
		$data[] = $row;
	}
	print json_encode($data);
}

// SELECT * FROM `spms_mfo_period` WHERE `year_mfo` > 2018 ORDER BY `spms_mfo_period`.`year_mfo` DESC;

else if (isset($_POST['getPeriodItems'])) {
	$data = [];
	$sql = "SELECT * FROM `spms_mfo_period` WHERE `year_mfo` > 2018 ORDER BY `spms_mfo_period`.`year_mfo` DESC;";
	$res = $mysqli->query($sql);
	while ($row = $res->fetch_assoc()) {
		$data[] = [
			"period_id" => $row["mfoperiod_id"],
			"period" => $row["month_mfo"] . " " . $row["year_mfo"],
		];
	}
	print json_encode($data);
} else if (isset($_POST['view'])) {

	$period_id = $_POST["period_id"];
	$department_id = $_POST["department_id"];

	// $period_id = 10; //10 - July to Dec 2022
	# performanceReviewStatus_id = 2434 test fomtype 3 strategic function shoul be excluded from computing final numerical rating
	$sql = "SELECT * FROM `spms_performancereviewstatus` LEFT JOIN `employees` ON `spms_performancereviewstatus`.`employees_id` = `employees`.`employees_id` where `spms_performancereviewstatus`.`period_id` = '$period_id' AND `spms_performancereviewstatus`.`department_id` = '$department_id' AND `spms_performancereviewstatus`.`employees_id` != '432258' ORDER BY `spms_performancereviewstatus`.`final_numerical_rating` DESC";
	//--`performanceReviewStatus_id` = '2909'
	//--`period_id` = '$period_id' AND `final_numerical_rating` IS NULL LIMIT 1
	$res = $mysqli->query($sql);
	$data = [];
	while ($row = $res->fetch_assoc()) {
		// $row['final_numerical_rating'] = $finalNumericalRating->getFinalNumericalRating($mysqli, $row);
		// $final_numerical_rating = $finalNumericalRating->getFinalNumericalRating($mysqli, $row);
		// $row['final_numerical_rating'] = $final_numerical_rating;

		$full_name = "";
		$full_name = $row['lastName'];
		$full_name .= ", " . $row['firstName'];
		$row['full_name'] = $full_name;
		$fileStatusId = $row['performanceReviewStatus_id'];
		// $finalNumericalRating->setFinalNumericalRating($mysqli, $fileStatusId, $final_numerical_rating);
		// setFinalNumericalRating

		$scale = "";
		$final_numerical_rating = $row['final_numerical_rating'];
		if ($final_numerical_rating <= 5 && $final_numerical_rating > 4) {
			$scale = "Outstanding";
		} elseif ($final_numerical_rating <= 4 && $final_numerical_rating > 3) {
			$scale = "Very Satisfactory";
		} elseif ($final_numerical_rating <= 3 && $final_numerical_rating > 2) {
			$scale = "Satisfactory";
		} elseif ($final_numerical_rating <= 2 && $final_numerical_rating > 1) {
			$scale = "Unsatisfactory";
		}
		$row['adjectival'] = $scale;
		$data[] = $row;
	}

	print json_encode($data);
} elseif (isset($_POST['viewMfos'])) {
	$period_id = $_POST["period_id"];
	$department_id = $_POST["department_id"];
	print table($mysqli, $period_id, $department_id);
}











































function table($mysqli, $period_id, $department_id)
{
	# $dep = $_SESSION['emp_info']['department_id']; 
	# get department_id from formstatus of selected period
	# return $_SESSION['emp_info']['department_id'] if no formstatus exists
	$period_id = $period_id;
	$department_id  = $department_id;


	$dep = "SELECT * from `department` where department_id='$department_id'";
	$dep = $mysqli->query($dep);
	$dep = $dep->fetch_assoc();
	$dep = $dep['department'];


	$period = "SELECT * from `spms_mfo_period` where `mfoperiod_id`='$period_id'";
	$period = $mysqli->query($period);
	$period = $period->fetch_assoc();
	// $period_id = $period['mfoperiod_id'];
	echo "
  <script>
  $('.ui.dropdown').dropdown({
    fullTextSearch:true
  });
  </script>
  <table class='tablepr' border='1px' style='border-collapse:collapse;width:100%;font-size:13px'>
  <thead>
  <tr class='noprint'>
  <th colspan='8' style='font-size:20px'>
  <br>$dep
  <br>$period[month_mfo] $period[year_mfo]
  </th>
  <tr>
  <tr>
  <th rowspan='2'>MFO/PAP</th>
  <th rowspan='2'>SUCCESS Indicator</th>
  <th rowspan='2'>Performance Measure</th>
  <th colspan='3'>Rating</th>
  <th rowspan='2'>IN-CHARGE</th>
  </tr>
  <tr>
  <th>Q</th>
  <th>E</th>
  <th>T</th>
  </tr>
  </thead>
  <tbody>" . tbody($mysqli) . "
  </tbody>
  </table>
  ";
}
function tbody($mysqli)
{
	$view = "";

	# $dep_id = $_SESSION['emp_info']['department_id'];

	$employee_id = $_SESSION['emp_info']['employees_id'];
	$period_id = $_SESSION['period'];

	$sql = "SELECT * FROM `spms_performancereviewstatus` WHERE `employees_id` = '$employee_id' AND `period_id` = '$period_id'";
	$res = $mysqli->query($sql);
	if ($row = $res->fetch_assoc()) {
		$dep_id = $row['department_id'];
	} else {
		$dep_id = $_SESSION['emp_info']['department_id'];
	}

	$sql = "SELECT * from spms_corefunctions where parent_id='' and mfo_periodId='$period_id' and dep_id='$dep_id' ORDER BY `spms_corefunctions`.`cf_count` ASC ";
	$sql = $mysqli->query($sql);
	$tr = "";
	while ($row1 = $sql->fetch_assoc()) {
		$view .= trows($mysqli, $row1, '10px', '');
		$view .= tbodyChild($row1['cf_ID'], 10);
	}
	$view .= "<tr class='noprint' >
  <td colspan='8' style='padding:10px'>
  " . AddInputs($mysqli, '') . "
  </td>
  </tr>";
	return $view;
}

function tbodyChild($dataId, $padding)
{
	$view = "";
	$mysqli = $GLOBALS['mysqli'];
	$sql2 = "SELECT * from spms_corefunctions where parent_id='$dataId' ORDER BY `spms_corefunctions`.`cf_count` ASC";
	$sql2 = $mysqli->query($sql2);
	$padding += 15;
	while ($row2 = $sql2->fetch_assoc()) {
		$sql3 = "SELECT * from spms_corefunctions where parent_id='$row2[cf_ID]' ORDER BY `spms_corefunctions`.`cf_count` ASC";
		$sql3 = $mysqli->query($sql3);
		$pad = $padding . "px";
		$view .= trows($mysqli, $row2, $pad, '');
		$view .= tbodyChild($row2['cf_ID'], $padding);
	}
	return $view;
}

function editInputs($dataId, $count, $title)
{
	$view = "
  <div class=' field' >
  <div class='ui right labeled input' >
  <textarea  type='text' style='width:50px;height:50px' id='EditcountRsm$dataId'>$count</textarea>
  <textarea  type='text' style='width:250px;height:50px'  id='EdittitleRsm$dataId'>$title</textarea>
  <div class='mini green ui basic icon button' onclick='EditRsmTitle($dataId)'><i class='edit icon'></i></div>
  </div>
  </div>";
	return $view;
}
function unserData($ser_arr)
{
	$count = 5;
	$data = "";
	$arr = unserialize($ser_arr);
	while ($count >= 1) {
		if ($arr[$count]) {
			$data .= "<b>" . $count . "</b> - " . $arr[$count] . "<br>";
		}
		$count--;
	}



	// foreach ($arr as $unser) {
	//   if($unser!=""){
	//     $data.=$count." - ". $unser."<br>";
	//   }
	//   $count++;
	// }
	return $data;
}

function validaateCorrection($dat)
{
	$color = false;
	if ($dat) {
		$count = 0;
		$dat = unserialize($dat);
		while ($count < count($dat)) {
			if ($dat[$count][1] == 0) {
				$color = true;
				break;
			}
			$count++;
		}
	}
	return $color;
}

function trows($mysqli, $row, $padding, $addDisplay)
{
	$sql2 = "SELECT * from spms_corefunctions where parent_id='$row[cf_ID]'";
	$sql2 = $mysqli->query($sql2);
	$sql2count = $sql2->num_rows;
	if ($sql2count > 0) {
		$set_drop = settingDrop($mysqli, $row, '', $addDisplay, 'display:none');
	} else {
		$set_drop = settingDrop($mysqli, $row, '', $addDisplay, '');
	}
	$view = "";
	$siData1 = "SELECT * from spms_matrixindicators where cf_ID='$row[cf_ID]'";
	$siData1 = $mysqli->query($siData1);
	$siDatacount1 = $siData1->num_rows;
	$count = 1;
	$correctionColorMFO = "";
	$correctionMFO = validaateCorrection($row['corrections']);
	if ($correctionMFO) {
		$correctionColorMFO = "color:red;";
	}

	if ($siDatacount1 > 0) {
		while ($siDataRow1 = $siData1->fetch_assoc()) {
			// $mi_id = $siDataRow1['mi_id'];
			$correctionColor = "";
			$correction = validaateCorrection($siDataRow1['corrections']);
			if ($correction) {
				$correctionColor = "color:red;";
			}
			$empincharge = "";
			$incharge = explode(',', $siDataRow1['mi_incharge']);
			#iterate employees
			foreach ($incharge as $empDataId) {
				if (!$empDataId || $empDataId == null) {
					continue;
				}
				$sqlIncharge = "SELECT * from employees where employees_id='$empDataId'";
				$sqlIncharge = $mysqli->query($sqlIncharge);
				$sqlIncharge = $sqlIncharge->fetch_assoc();

				// $empincharge .= "<br><a onclick='ShowIPcrModal(\"$sqlIncharge[employees_id]\")' style='cursor:pointer;'>$sqlIncharge[firstName] $sqlIncharge[lastName]</a><br>";

				$empincharge = "";
				if (isset($siDataRow1['mi_id'])) {
					$mi_id = $siDataRow1['mi_id'];
					$sql = "SELECT * FROM `spms_corefucndata`where `p_id` = '$mi_id' AND `empId` = '$empDataId';";
					$res = $mysqli->query($sql);
					if ($rowdata = $res->fetch_assoc()) {
						// $empincharge .= " -- " . json_encode($rowdata);
						# Rehabilitation Leave Benefits
						if ($rowdata['disable'] != 1) {
							# code...
							$score = 0;
							$q = "";
							$e = "";
							$t = "";
							$color = "#ff000091";
							$qColor = "";
							$eColor = "";
							$tColor = "";
							$ratingColor = "";
							$count_scales = 0;
							if ($rowdata['Q']) {
								$score += $rowdata['Q'];
								$q = $rowdata['Q'];

								if ($q < 4) {
									$qColor = $color;
								}
								$count_scales++;
							}
							if ($rowdata['E']) {
								$score += $rowdata['E'];
								$e = $rowdata['E'];

								if ($e < 4) {
									$eColor = $color;
								}
								$count_scales++;
							}
							if ($rowdata['T']) {
								$score += $rowdata['T'];
								$t = $rowdata['T'];

								if ($t < 4) {
									$tColor = $color;
								}
								$count_scales++;
							}

							// $empincharge .= "<br/>";
							$score = bcdiv($score, $count_scales, 1);
							$score = explode(".", $score);
							if ($score[1] == 0) {
								$score = $score[0];
							} else {
								$score = implode(".", $score);
							}
							if ($score < 3) {
								$ratingColor = $color;
							}
							$final_mfo_rating = $score . "/5";

							$empincharge .= "<table class='ui mini compact structured celled table'>
				        <thead>
				            <tr style='text-align: left;'>
				              <th colspan='4'><a onclick='ShowIPcrModal(\"$sqlIncharge[employees_id]\")' style='cursor:pointer;'>$sqlIncharge[firstName] $sqlIncharge[lastName]</a></th>
				            </tr>
				            <tr style='text-align: center;'>
				              <th>Q</th>
				              <th>E</th>
				              <th>T</th>
				              <th>FINAL</th>
				            </tr>
				        </thead>
				        <tbody>
				            <tr style='text-align: center;'>
				              <td style='background-color:$qColor;'>$q</td>
				              <td style='background-color:$eColor;'>$e</td>
				              <td style='background-color:$tColor;'>$t</td>
				              <td style='background-color:$ratingColor;'>$final_mfo_rating</td>
				            </tr>
				        </tbody>
				      </table>";
							// $empincharge .= $final_mfo_rating;
						} #else not applicable
						else {
							$empincharge .= "<a onclick='ShowIPcrModal(\"$sqlIncharge[employees_id]\")' style='cursor:pointer;'>$sqlIncharge[firstName] $sqlIncharge[lastName]</a><br/>";
							$empincharge .= "N/A (" . $rowdata['remarks'] . ")";
						}
					} else {
						$empincharge .= "<a onclick='ShowIPcrModal(\"$sqlIncharge[employees_id]\")' style='cursor:pointer;'>$sqlIncharge[firstName] $sqlIncharge[lastName]</a><br/>";
						$empincharge .= "NOT ACCOMPLISHED";
					}
				}
			}
			$Qdata = "";
			$Edata = "";
			$Tdata = "";
			$performanceMeasure = "";
			if (unserData($siDataRow1['mi_quality']) != "") {
				$performanceMeasure .= "Quality<br>";
			}
			if (unserData($siDataRow1['mi_eff']) != "") {
				$performanceMeasure .= "Efficiency<br>";
			}
			if (unserData($siDataRow1['mi_time']) != "") {
				$performanceMeasure .= "Timeliness<br>";
			}
			if ($count == 1) {
				$view .= "
        <tr >
        <td style='padding-left:$padding;width:25%;$correctionColorMFO'>
        " . $set_drop . "
        $row[cf_count]) $row[cf_title] " .  ""/*json_encode($row)*/ . "
        </td>
        <td style='width:25%;$correctionColor'>" . nl2br($siDataRow1['mi_succIn']) . ""/*json_encode($siDataRow1)*/ . "</td>
        <td>$performanceMeasure</td>
        <td style='width:150px;padding-bottom:10px;$correctionColor'>" . unserData($siDataRow1['mi_quality']) . "</td>
        <td style='width:150px;padding-bottom:10px;$correctionColor'>" . unserData($siDataRow1['mi_eff']) . "</td>
        <td style='width:150px;padding-bottom:10px;$correctionColor'>" . unserData($siDataRow1['mi_time']) . "</td>
        <td>$empincharge</td>
        </tr>
        ";
			} else {
				$view .= "
        <tr >
        <td></td>
        <td style='width:25%;$correctionColor'>" . nl2br($siDataRow1['mi_succIn']) . "</td>
        <td>$performanceMeasure</td>
        <td style='width:150px;padding-bottom:10px;$correctionColor'>" . unserData($siDataRow1['mi_quality']) . "</td>
        <td style='width:150px;padding-bottom:10px;$correctionColor'>" . unserData($siDataRow1['mi_eff']) . "</td>
        <td style='width:150px;padding-bottom:10px;$correctionColor'>" . unserData($siDataRow1['mi_time']) . "</td>
        <td>$empincharge</td>
        </tr>
        ";
			}
			$count++;
		}
	} else {
		$view .= "
    <tr >
    <td style='padding-left:$padding;width:500px;$correctionColorMFO'>
    " . $set_drop . "
    $row[cf_count]) $row[cf_title] " . ""/*json_encode($row)*/ . "
    </td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td class='noprint'></td>
    </tr>
    ";
	}
	return $view;
}
function rsmEditStatus($dat)
{
	$mysqli = $GLOBALS['mysqli'];
	$department_id = $GLOBALS['user'];
	$department_id = $department_id->get_emp('department_id');
	$period = $_SESSION['period'];
	$enable = false;

	$sql = "SELECT * from `spms_rsmstatus` where `period_id`='$period' and `department_id`='$department_id'";
	$sql = $mysqli->query($sql);
	$sql = $sql->fetch_assoc();
	if ($sql['edit']) {
		$enable = true;
	}
	if ($dat == "id") {
		return $sql['rsmStatus_id'];
	} else {
		return $enable;
	}
}


function AddInputs($mysqli, $dataId)
{
	return "";
	# check first if rating scale matrix has already existing data
	# also check if previous rsm exist for copying
	// period
	$view = "";
	$period_id = $_SESSION["period"];
	$employee_id = $_SESSION['emp_info']['employees_id'];

	$sql = "SELECT * FROM `spms_performancereviewstatus` WHERE `employees_id` = '$employee_id' AND `period_id` = '$period_id'";
	$res = $mysqli->query($sql);
	if ($row = $res->fetch_assoc()) {
		$department_id = $row['department_id'];
	} else {
		$department_id = $_SESSION['emp_info']['department_id'];
	}


	$curr_rsm_exists = false;
	$prev_rsm_exists = false;
	$previous_period_id = getPreviousPeriodId($mysqli);
	$sql = "SELECT * FROM `spms_corefunctions` WHERE `mfo_periodId` = '$period_id' AND `dep_id` = '$department_id' LIMIT 1;";
	$result = $mysqli->query($sql);
	if ($result->num_rows > 0) {
		$curr_rsm_exists = true;
	}

	$sql = "SELECT * FROM `spms_corefunctions` WHERE `mfo_periodId` = '$previous_period_id' AND `dep_id` = '$department_id' LIMIT 1;";
	$result = $mysqli->query($sql);
	if ($result->num_rows > 0) {
		$prev_rsm_exists = true;
	}

	if (!$curr_rsm_exists && $prev_rsm_exists) {
		$view .= "<button class='ui green large button' onclick='copyRSM()'>Copy Previous RSM</button>";
	} else {
		$view = "
    <div class='ui mini form'>
    <div class='fields'>
    <input type='hidden' value='$dataId' id='mfo_pid$dataId'>
    <div class='field'>
    <label>Category. No.</label>
    <input type='text' style='width:90px' placeholder='ex: I,II,1,1.0,1.1.0' id='rsmcount$dataId'>
    </div>
    <div class=' field' >
    <label>Title</label>
    <div class='ui right labeled input'>
    <input type='text' style='width:200px' placeholder='Type Here.....' id='titleRsm$dataId'>
    <div class='mini ui primary basic icon button' onclick='addMFoRsm(\"$dataId\")'><i class='save icon'></i></div>
    </div>
    </div>
    </div>
    </div>";
	}
	if (!rsmEditStatus("")) {
		$view = "";
	}
	return $view;
}
function settingDrop($mysqli, $row, $edit, $add, $delete)
{
	$correction = "";
	if ($row['corrections']) {
		$c = unserialize($row['corrections']);
		$count = 0;
		$crt = "";
		while ($count < count($c)) {
			$state = "<b style='color:red'>Unaccomplished</b>";
			if ($c[$count][1]) {
				$state = "<b style='color:green'>Accomplished</b>";
			}
			$crt .= $c[$count][0] . " - $state <br>";
			$count++;
		}
		$correction = "
    <div class='header'>
    <p class='ui horizontal divider'>
    <i class='indent icon'></i>
    <span style='font-size:10px'>Corrections</span>
    </p>
    </div>
    <div class='header'>
      $crt
    </div>
    ";
	}
	$view = "
  $correction
  <div class='header' style='$edit'>

  </div>
  ";
	$correctionMFO = validaateCorrection($row['corrections']);
	if (!rsmEditStatus("") && !$correctionMFO) {
		$view = "";
	}
	return $view;
}
function changeCount($dat)
{
	$dat = str_replace(")", "", $dat);
	$dat = explode(".", $dat);
	$d = "";
	foreach ($dat as $a) {
		$a = str_replace(' ', '', $a);
		if ($a) {
			if (is_numeric($a)) {
				if ($a < 10 && strlen($a) == 1) {
					$d .= "0" . $a . ".";
				} else {
					$d .= $a . ".";
				}
			} else {
				$d .= $a . ".";
			}
		}
	}
	return $d;
}

function get_children($mysqli, $cf_ID)
{
	$data = [];
	$sql = "SELECT * FROM `spms_corefunctions` WHERE `parent_id` ='$cf_ID'";
	$result = $mysqli->query($sql);
	while ($row = $result->fetch_assoc()) {
		$row["children"] = get_children($mysqli, $row["cf_ID"]);
		$data[] = $row;
	}
	return $data;
}

function start_duplicating($mysqli, $data, $selected_period_id, $parent_id, $department_id = null)
{
	// $department_id = 3;
	foreach ($data as $key => $core_function) {
		// $department_id = $core_function['dep_id'];
		if (!$department_id) {
			$department_id = $core_function['dep_id'];
		}
		$parent_id = $parent_id ? $parent_id : NULL;
		$cf_title = $mysqli->real_escape_string($core_function['cf_title']);
		$cf_count = $mysqli->real_escape_string($core_function['cf_count']);
		$sql = "INSERT INTO `spms_corefunctions`(`mfo_periodId`, `parent_id`, `dep_id`, `cf_count`, `cf_title`, `corrections`) VALUES ('$selected_period_id','$parent_id','$department_id','$cf_count','$cf_title','')";
		$mysqli->query($sql);
		$insert_id = $mysqli->insert_id;

		#get success indicators
		$success_idicators = get_success_indicators($mysqli, $core_function["cf_ID"]);
		foreach ($success_idicators as $success_idicator) {

			$mi_succIn = $mysqli->real_escape_string($success_idicator['mi_succIn']);

			$mi_quality = $mysqli->real_escape_string($success_idicator['mi_quality']);
			$mi_eff = $mysqli->real_escape_string($success_idicator['mi_eff']);
			$mi_time = $mysqli->real_escape_string($success_idicator['mi_time']);

			$sql = "INSERT INTO `spms_matrixindicators`(`cf_ID`, `mi_succIn`, `mi_quality`, `mi_eff`, `mi_time`, `mi_incharge`, `corrections`) VALUES ('$insert_id','$mi_succIn','$mi_quality','$mi_eff','$mi_time','$success_idicator[mi_incharge]','')";
			$mysqli->query($sql);
		}

		$data[$key]["children"] = start_duplicating($mysqli, $core_function["children"], $selected_period_id, $insert_id);
	}

	return $data;
}

function get_success_indicators($mysqli, $cf_ID)
{
	$data = [];
	$sql = "SELECT * FROM `spms_matrixindicators` WHERE `cf_ID` = '$cf_ID'";
	$result = $mysqli->query($sql);
	while ($row = $result->fetch_assoc()) {
		$data[] = $row;
	}
	return $data;
}
