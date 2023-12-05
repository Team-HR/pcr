<?php
require_once "assets/pages/review/config.php";

if (isset($_POST["getPersonnelHeirarchy"])) {

    $selected_period_month = $_POST["selected_period_month"];
    $selected_period_year = $_POST["selected_period_year"];
    $departmentHeadEmployeeId = $_POST["departmentHead_id"];

    $period_id = 0;

    $sql = "SELECT * FROM `spms_mfo_period` where `month_mfo` = '$selected_period_month' AND `year_mfo` = '$selected_period_year'";

    $res = $mysqli->query($sql);
    if ($row = $res->fetch_assoc()) {
        $period_id = $row["mfoperiod_id"];
    }

    // $departmentHeadEmployeeId = 0;

    // if ($period_id) {
    //     $sql = "SELECT DISTINCT `DepartmentHead` FROM `spms_performancereviewstatus` WHERE `department_id` = '$department_id' and `period_id` = '$period_id'";

    //     $res = $mysqli->query($sql);

    //     if ($row = $res->fetch_assoc()) {
    //         $departmentHeadEmployeeId = $row["DepartmentHead"];
    //     }
    // }

    // echo json_encode($departmentHeadEmployeeId);

    echo json_encode(get_subordinates($mysqli, $period_id, $departmentHeadEmployeeId, false));
}
