<?php

require_once "assets/libs/RatingScaleMatrixDestroyer.php";

date_default_timezone_set("Asia/Manila");
$host = "db";
$usernameDb = "admin";
$password = "teamhrmo2019";
$database = "ihris";
$mysqli = new mysqli($host, $usernameDb, $password, $database);
$mysqli->set_charset("utf8");
#####################################################################################

$rsm = new RatingScaleMatrixDestroyer($mysqli);

$rsm->set_period_id(22);
$rsm->set_department_id(10);
// $data = $rsm->delete_rating_scale_matrix();

$json = json_encode($data, JSON_PRETTY_PRINT);


echo "<pre>$json</pre>";