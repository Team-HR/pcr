<?php
require_once "assets/libs/config_class.php";
session_start();

$serial = 'a:3:{i:0;a:5:{i:0;s:18:"Enginering Manager";i:1;s:0:"";i:2;s:18:"Santos Knight Park";i:3;s:10:"2022-03-18";i:4;s:0:"";}i:1;a:5:{i:0;s:18:"Technical Engineer";i:1;s:0:"";i:2;s:21:"Cebu Land Master Inc.";i:3;s:10:"2019-12-16";i:4;s:10:"2022-03-17";}i:2;a:5:{i:0;s:19:"Building Technician";i:1;s:0:"";i:2;s:30:"Primary Properties Corporation";i:3;s:9:"2016-4-13";i:4;s:10:"2019-11-25";}}';

echo json_encode(unserialize($serial));
# to make queries faster
# get first array of corefunctions with dep_id and mfo_period
// $sql = "SELECT `cf_ID` FROM `spms_corefunctions` WHERE `dep_id` = '32' AND `mfo_periodId` = '3';";
// $result = $mysqli->query($sql);
// $cf_IDs = [];
// while ($row = $result->fetch_assoc()) {
//   $cf_IDs[] = $row["cf_ID"];
// }
// $json = json_encode($cf_IDs);
// echo $json;

// echo "<br/>";
// # get core functions from spms_matrixindicators using first query above
// $sql = "SELECT * FROM `spms_matrixindicators` WHERE `cf_ID` IN (SELECT `cf_ID` FROM `spms_corefunctions` WHERE `dep_id` = '32' AND `mfo_periodId` = '3')";
// $result = $mysqli->query($sql);
// $core_functions = [];
// while ($row = $result->fetch_assoc()) {
//   $core_functions[] = $row;
// }
// echo json_encode($core_functions);