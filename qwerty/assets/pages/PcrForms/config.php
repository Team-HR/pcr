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
        $employees_id = $row["employees_id"];
        $nameFormatter->set_employee_id($employees_id);
        $row["name"] = $nameFormatter->getFullNameStandardUpper();
        $row["username"] = getUsername($mysqli, $employees_id);
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
    $sql = "UPDATE `spms_performancereviewstatus` SET `submitted` = '', `panelApproved` = ''  WHERE `spms_performancereviewstatus`.`performanceReviewStatus_id` = '$performanceReviewStatus_id';";
    $mysqli->query($sql);
    echo json_encode($performanceReviewStatus_id);
} elseif (isset($_POST["convertForm"])) {
    $fileToConvert  = $_POST["fileToConvert"];
    $currFormType = $fileToConvert["formType"];
    $selFormType =  $_POST["selFormType"];

    $performanceReviewStatus_id = $fileToConvert["performanceReviewStatus_id"];

    $employees_id = $fileToConvert["employees_id"];
    $period_id = $fileToConvert["period_id"];


    $sql = "UPDATE `spms_performancereviewstatus` SET `formType` = '$selFormType' WHERE `spms_performancereviewstatus`.`performanceReviewStatus_id` = '$performanceReviewStatus_id';
    ";
    $mysqli->query($sql);

    # check if file has support function data existing

    // $sql = "SELECT * FROM `spms_supportfunctiondata` WHERE emp_id = '$employees_id' AND period_id = '$period_id';";
    // $res = $mysqli->query($sql);
    // $support_function_data = [];
    // while ($row = $res->fetch_assoc()) {
    //     $support_function_data[] =  $row;
    // }
    if ($selFormType == '1') {
        if ($currFormType == '2') {
            $conversion = [
                "22" => [
                    "parent_id" => 6,
                    "percent" => 2,
                ],
                "21" => [
                    "parent_id" => 7,
                    "percent" => 1,
                ],
                "23" => [
                    "parent_id" => 8,
                    "percent" => 1,
                ],
                "24" => [
                    "parent_id" => 9,
                    "percent" => 2,
                ],
                "25" => [
                    "parent_id" => 10,
                    "percent" => 5,
                ],
                "26" => [
                    "parent_id" => 11,
                    "percent" => 5,
                ],
                "19" => [
                    "parent_id" => 3,
                    "percent" => 2,
                ],
                "20" => [
                    "parent_id" => 5,
                    "percent" => 2,
                ],

            ];
            foreach ($conversion as $key => $func) {
                $sql = "UPDATE `spms_supportfunctiondata` SET `parent_id` = '$func[parent_id]', `percent` = '$func[percent]' WHERE `parent_id` = '$key' AND `emp_id` = '$employees_id' AND `period_id` = '$period_id';";
                $mysqli->query($sql);
            }
        } elseif ($currFormType == '3') {
            $conversion = [
                "14" => [
                    "parent_id" => 6,
                    "percent" => 2,
                ],
                "12" => [
                    "parent_id" => 7,
                    "percent" => 1,
                ],
                "13" => [
                    "parent_id" => 8,
                    "percent" => 1,
                ],
                "17" => [
                    "parent_id" => 10,
                    "percent" => 5,
                ],
                "18" => [
                    "parent_id" => 11,
                    "percent" => 5,
                ],
            ];
            foreach ($conversion as $key => $func) {
                $sql = "UPDATE `spms_supportfunctiondata` SET `parent_id` = '$func[parent_id]', `percent` = '$func[percent]' WHERE `parent_id` = '$key' AND `emp_id` = '$employees_id' AND `period_id` = '$period_id';";
                $mysqli->query($sql);
            }
        }
    } elseif ($selFormType == '2') {
        if ($currFormType == '1') {
            $conversion = [
                "6" => [
                    "parent_id" => 22,
                    "percent" => 2,
                ],
                "7" => [
                    "parent_id" => 21,
                    "percent" => 2,
                ],
                "8" => [
                    "parent_id" => 23,
                    "percent" => 2,
                ],
                "9" => [
                    "parent_id" => 24,
                    "percent" => 2,
                ],
                "10" => [
                    "parent_id" => 25,
                    "percent" => 4,
                ],
                "11" => [
                    "parent_id" => 26,
                    "percent" => 4,
                ],
                "3" => [
                    "parent_id" => 19,
                    "percent" => 2,
                ],
                "5" => [
                    "parent_id" => 20,
                    "percent" => 2,
                ],

            ];
            foreach ($conversion as $key => $func) {
                $sql = "UPDATE `spms_supportfunctiondata` SET `parent_id` = '$func[parent_id]', `percent` = '$func[percent]' WHERE `parent_id` = '$key' AND `emp_id` = '$employees_id' AND `period_id` = '$period_id';";
                $mysqli->query($sql);
            }
        } elseif ($currFormType == '3') {
            $conversion = [
                "14" => [
                    "parent_id" => 22,
                    "percent" => 2,
                ],
                "12" => [
                    "parent_id" => 21,
                    "percent" => 2,
                ],
                "13" => [
                    "parent_id" => 23,
                    "percent" => 2,
                ],
                "17" => [
                    "parent_id" => 25,
                    "percent" => 4,
                ],
                "18" => [
                    "parent_id" => 26,
                    "percent" => 4,
                ],
            ];
            foreach ($conversion as $key => $func) {
                $sql = "UPDATE `spms_supportfunctiondata` SET `parent_id` = '$func[parent_id]', `percent` = '$func[percent]' WHERE `parent_id` = '$key' AND `emp_id` = '$employees_id' AND `period_id` = '$period_id';";
                $mysqli->query($sql);
            }
        }
    } elseif ($selFormType = '3') {
        if ($currFormType == '1') {
            $conversion = [
                "6" => [
                    "parent_id" => 14,
                    "percent" => 5,
                ],
                "7" => [
                    "parent_id" => 12,
                    "percent" => 2,
                ],
                "8" => [
                    "parent_id" => 13,
                    "percent" => 2,
                ],
                "10" => [
                    "parent_id" => 17,
                    "percent" => 2,
                ],
                "11" => [
                    "parent_id" => 18,
                    "percent" => 2,
                ],
            ];
            foreach ($conversion as $key => $func) {
                $sql = "UPDATE `spms_supportfunctiondata` SET `parent_id` = '$func[parent_id]', `percent` = '$func[percent]' WHERE `parent_id` = '$key' AND `emp_id` = '$employees_id' AND `period_id` = '$period_id';";
                $mysqli->query($sql);
            }
        } elseif ($currFormType == '2') {
            $conversion = [
                "22" => [
                    "parent_id" => 14,
                    "percent" => 5,
                ],
                "21" => [
                    "parent_id" => 12,
                    "percent" => 2,
                ],
                "23" => [
                    "parent_id" => 13,
                    "percent" => 2,
                ],
                "25" => [
                    "parent_id" => 17,
                    "percent" => 2,
                ],
                "26" => [
                    "parent_id" => 18,
                    "percent" => 2,
                ],
            ];
            foreach ($conversion as $key => $func) {
                $sql = "UPDATE `spms_supportfunctiondata` SET `parent_id` = '$func[parent_id]', `percent` = '$func[percent]' WHERE `parent_id` = '$key' AND `emp_id` = '$employees_id' AND `period_id` = '$period_id';";
                $mysqli->query($sql);
            }
        }
    }

    echo json_encode("success");
}

function getUsername($mysqli, $employees_id)
{
    $username = "N/A";
    $res = $mysqli->query("SELECT * FROM `spms_accounts` WHERE employees_id = '$employees_id'");
    $username = $res->fetch_assoc()["username"];
    return $username;
}
