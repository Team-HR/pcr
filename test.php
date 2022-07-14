<?php
require_once "assets/libs/config_class.php";
session_start();
echo json_encode($_SESSION);
# to make queries faster
# get first array of corefunctions with dep_id and mfo_period
$sql = "SELECT `cf_ID` FROM `spms_corefunctions` WHERE `dep_id` = '32' AND `mfo_periodId` = '3';";
// $result = $mysqli->query($sql);
// $cf_IDs = [];
// while ($row = $result->fetch_assoc()) {
//   $cf_IDs[] = $row["cf_ID"];
// }
// $json = json_encode($cf_IDs);
// echo $json;

echo "<br/>";
# get core functions from spms_matrixindicators using first query above
$sql = "SELECT * FROM `spms_matrixindicators` WHERE `cf_ID` IN (SELECT `cf_ID` FROM `spms_corefunctions` WHERE `dep_id` = '32' AND `mfo_periodId` = '3')";
$result = $mysqli->query($sql);
$core_functions = [];
while ($row = $result->fetch_assoc()) {
  $core_functions[] = $row;
}
echo json_encode($core_functions);