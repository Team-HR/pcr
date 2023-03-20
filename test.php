<?php
require_once "assets/libs/FinalNumericalRatings.php";

date_default_timezone_set("Asia/Manila");
$host = "localhost";
$usernameDb = "admin";
// $password = "teamhrmo2019";
$password = "teamhrmo2019";
$database = "ihris";
$mysqli = new mysqli($host, $usernameDb, $password, $database);
$mysqli->set_charset("utf8");
#####################################################################################

$period_id = 10; //10 - July to Dec 2022
$finalNumericalRating = new FinalNumericalRating();

# performanceReviewStatus_id = 2434 test fomtype 3 strategic function shoul be excluded from computing final numerical rating
$sql = "SELECT * FROM `spms_performancereviewstatus` where `period_id` = '$period_id' AND `department_id` = 32 limit 2";
//--`performanceReviewStatus_id` = '2909'
//--`period_id` = '$period_id' AND `final_numerical_rating` IS NULL LIMIT 1
$res = $mysqli->query($sql);
$data = [];
while ($row = $res->fetch_assoc()) {
    // $row['final_numerical_rating'] = $finalNumericalRating->getFinalNumericalRating($mysqli, $row);
    $final_numerical_rating = $finalNumericalRating->getFinalNumericalRating($mysqli, $row);
    $row['final_numerical_rating'] = $final_numerical_rating;
    $fileStatusId = $row['performanceReviewStatus_id'];
    $finalNumericalRating->setFinalNumericalRating($mysqli, $fileStatusId, $final_numerical_rating);
    // setFinalNumericalRating
    $data[] = $row;
}

print("<pre>" . print_r($data, true) . "</pre>");


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
