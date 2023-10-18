<?php

require_once "assets/libs/RatingScaleMatrixDestroyer.php";

date_default_timezone_set("Asia/Manila");
$host = "localhost";
$usernameDb = "admin";
$password = "teamhrmo2019";
$database = "ihris";
$mysqli = new mysqli($host, $usernameDb, $password, $database);
$mysqli->set_charset("utf8");
#####################################################################################

$sql = "SELECT * FROM `spms_matrixindicators`";

$res = $mysqli->query($sql);
$data = [];
while ($row = $res->fetch_assoc()) {

    $mi_quality = unserialize($row['mi_quality']);
    $mi_eff = unserialize($row['mi_eff']);
    $mi_time = unserialize($row['mi_time']);
    $mi_quality = convertSerial($mi_quality);
    $mi_eff = convertSerial($mi_eff);
    $mi_time = convertSerial($mi_time);
    $data[] = [
        "id" => $row['mi_id'],
        "pms_rsm_id" => $row['cf_ID'],
        "index" => 0,
        "success_indicator" => $row['mi_succIn'],
        "quality" => $mi_quality,
        "efficiency" => $mi_eff,
        "timeliness" => $mi_time
    ];
}

$json = json_encode($data, JSON_PRETTY_PRINT);
echo "<pre>$json</pre>";

function convertSerial($metrics)
{
    if (is_array($metrics)) {
        $null_count = 0;
        array_shift($metrics);
        foreach ($metrics as $key => $metric) {
            if ($metric == "") {
                $metrics[$key] = null;
                $null_count += 1;
            }
        }

        $metrics = array_reverse($metrics);

        if ($null_count  == 5) {
            $metrics = [];
        }
    } else {
        $metrics = [];
    }
    return $metrics;
}
