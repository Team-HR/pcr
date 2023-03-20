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
			"period" => $row["month_mfo"]." ".$row["year_mfo"],
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
}
