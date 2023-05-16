<?php
// require_once "qwerty/assets/libs/NameFormatter.php";
$nameFormatter = new NameFormatter($mysqli);

if (isset($_POST["getPeriods"])) {
    $sql = "SELECT * FROM `spms_mfo_period`";
    $res = $mysqli->query($sql);
    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
    echo  json_encode($data);
} elseif (isset($_POST["getDepartments"])) {
    $sql = "SELECT * FROM `department` ORDER BY `department`.`department` ASC";
    $res = $mysqli->query($sql);
    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
} elseif (isset($_POST["getForms"])) {
    $selPeriod = $_POST["selPeriod"];
    $selYear = $_POST["selYear"];
    $selDepartment = $_POST["selDepartment"];


    $data = [];
    // get period id first
    $sql = "SELECT * FROM `spms_mfo_period` WHERE `month_mfo` = '$selPeriod' AND `year_mfo` = '$selYear'";

    $res = $mysqli->query($sql);
    $row = $res->fetch_assoc();
    $period_id = $row["mfoperiod_id"];

    if ($selDepartment) {
        $department_id = $selDepartment["department_id"];
        $sql = "SELECT `spms_performancereviewstatus`.*, `department`.* FROM `spms_performancereviewstatus` LEFT JOIN `department` ON `spms_performancereviewstatus`.`department_id` = `department`.`department_id` WHERE `spms_performancereviewstatus`.`period_id` = '$period_id' AND `spms_performancereviewstatus`.`department_id` = '$department_id'";
    } else {
        $sql = "SELECT `spms_performancereviewstatus`.*, `department`.* FROM `spms_performancereviewstatus` LEFT JOIN `department` ON `spms_performancereviewstatus`.`department_id` = `department`.`department_id` WHERE `spms_performancereviewstatus`.`period_id` = '$period_id'";
    }

    $res = $mysqli->query($sql);
    while ($row = $res->fetch_assoc()) {
        $nameFormatter->set_employee_id($row["employees_id"]);
        $row["name"] = $nameFormatter->getFullNameStandardUpper();
        $row["department"] =  $row["department"];
        $data[] = $row;
    }

    usort($data, function ($item1, $item2) {
        return $item1['name'] <=> $item2['name'];
    });

    echo json_encode($data);
} elseif (isset($_POST["lockForm"])) {
    $performanceReviewStatus_id = $_POST["performanceReviewStatus_id"];
    $sql = "UPDATE `spms_performancereviewstatus` SET `submitted` = 'Done' WHERE `spms_performancereviewstatus`.`performanceReviewStatus_id` = '$performanceReviewStatus_id';";
    $mysqli->query($sql);
    echo json_encode($performanceReviewStatus_id);
} elseif (isset($_POST["unlockForm"])) {
    $performanceReviewStatus_id = $_POST["performanceReviewStatus_id"];
    $sql = "UPDATE `spms_performancereviewstatus` SET `submitted` = '' WHERE `spms_performancereviewstatus`.`performanceReviewStatus_id` = '$performanceReviewStatus_id';";
    $mysqli->query($sql);
    echo json_encode($performanceReviewStatus_id);
}
