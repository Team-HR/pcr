<?php
date_default_timezone_set("Asia/Manila");
$host = "localhost";
$usernameDb = "admin";
// $password = "teamhrmo2019";
$password = "teamhrmo2019";
$database = "ihris";
$mysqli = new mysqli($host, $usernameDb, $password, $database);
$mysqli->set_charset("utf8");
#####################################################################################


echo json_encode(coreRow($mysqli));

function coreRow($mysqli)
{
    $arr = coreAr($mysqli);
    return $arr;
    $employee_id = 9;
    $count0 = count($arr);
    $in0 = 0;
    $count = 0;
    $totalav = 0;
    $cTotal = 0;
    while ($in0 < $count0) {
        $a1 = $arr[$in0][2];
        $child = coreRow_child($mysqli, $employee_id, $a1);
        $t0 = Core_mfoRow($mysqli, $employee_id, $arr[$in0]);
        $count += $t0[0] + $child[0];
        $totalav += $t0[1] + $child[1];
        $cTotal += $t0[2] + $child[2];
        $in0++;
    }
    // if($cTotal>0){
    // 	// $totalav = $totalav/$cTotal;
    // }else{
    // 	$totalav=0;
    // }
    // $totalav = $totalav*0.60;
    // $a = [$view,$count,count($arr),$totalav];
    $core_countEmpty = $count;
    $core_countTotal = count($arr);
    return $totalav;
}


function coreAr($mysqli)
{
    # for more compact and faster query
    # ... and `dep_id` = '$department_id'
    $fileStatus = [
        'performanceReviewStatus_id' => '',
        'period_id' => 10,
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
        'formType' => '',
        'department_id' => 32,
        'assembleAll' => '',
    ];

    # department_id from spms_performancereviewstatus
    $department_id = isset($fileStatus["department_id"]) ? $fileStatus["department_id"] : "";
    $period_id = $fileStatus["period_id"];
    $employee_id = $fileStatus["employees_id"];

    # not recommended department_id from employees table
    $main_Arr = [];
    $sql = "SELECT * from spms_corefunctions where parent_id='' and mfo_periodId='$period_id' and `dep_id` = '$department_id' ORDER BY `spms_corefunctions`.`cf_count` ASC";
    $sql = $mysqli->query($sql);
    $parent = [[], [], []];
    while ($core = $sql->fetch_assoc()) {
        $parent[0] = $core;
        $si = si($mysqli, $employee_id, $core['cf_ID']);
        $child = q($mysqli, $core['cf_ID']);
        if ($child->num_rows) {
            $parent[2] = coreAr_Child($mysqli, $employee_id, $core['cf_ID']);
        }
        if (count($si)) {
            $parent[1] = $si;
        }

        if (count($si) || $parent[2]) {
            array_push($main_Arr, $parent);
            $parent = [[], [], []];
        }
    }
    return $main_Arr;
}

function coreAr_Child($mysqli, $employee_id,  $dataId)
{
    $main_Arr = [];
    $sql = q($mysqli, $dataId);
    $parent = [[], [], []];
    while ($childCore = $sql->fetch_assoc()) {
        $parent[0] = $childCore;
        $si = si($mysqli, $employee_id, $childCore['cf_ID']);
        $child = q($mysqli, $childCore['cf_ID']);
        if ($child->num_rows) {
            $parent[2] = coreAr_Child($mysqli, $employee_id, $childCore['cf_ID']);
        }
        if (count($si)) {
            $parent[1] = $si;
        }
        if (count($si) || $parent[2]) {
            array_push($main_Arr, $parent);
            $parent = [[], [], []];
        }
    }
    return $main_Arr;
}

function si($mysqli, $employee_id, $siId)
{
    $i = [];
    if (!$siId || $siId == null) {
        return $i;
    }
    $sqlSi1 = "SELECT * from spms_matrixindicators where cf_ID='$siId'";
    $sqlSi1 = $mysqli->query($sqlSi1);
    if ($sqlSi1->num_rows > 0) {
        while ($a = $sqlSi1->fetch_assoc()) {
            $incharge = explode(',', $a['mi_incharge']);
            $cIn = 0;
            while ($cIn < count($incharge)) {
                if ($incharge[$cIn] == $employee_id) {
                    array_push($i, $a);
                }
                $cIn++;
            }
        }
    } else {
        $i = [];
    }
    return $i;
}


function q($mysqli, $siId)
{
    $sql = "SELECT * from spms_corefunctions where parent_id='$siId' ORDER BY `spms_corefunctions`.`cf_count` ASC";
    $sql = $mysqli->query($sql);
    return $sql;
}

function coreRow_child($mysqli, $employee_id, $arr)
{
    $index = 0;
    $childData = ["", "", ""];
    $count = 0;
    $totalav = 0;
    $cTotal = 0;
    while ($index < count($arr)) {
        $a2 = $arr[$index][2];
        $child = coreRow_child($mysqli, $employee_id, $a2);
        $data = Core_mfoRow($mysqli, $employee_id, $arr[$index]);
        $count += $data[0] + $child[0];
        $totalav += $data[1] + $child[1];
        $cTotal += $data[2] + $child[2];
        $index++;
    }
    $childData = [$count, $totalav, $cTotal];
    return $childData;
}

function Core_mfoRow($mysqli, $employee_id, $ar)
{
    $cTotal = 0;
    $count = 0;
    $totalav = 0;
    $inSi = 0;
    $view = "";
    if (count($ar[1]) > 0) {
        while ($inSi < count($ar[1])) {
            if ($inSi == 0) {
                $row0 = Core_siRow($mysqli, $employee_id, $ar[0], $ar[1][$inSi]);
                $view .= $row0[0];
                $count += $row0[1];
                $totalav += $row0[2];
                $cTotal += $row0[3];
            } else {
                $row1 = Core_siRow($mysqli, $employee_id, ['cf_count' => '', 'cf_title' => ''], $ar[1][$inSi]);
                $view .= $row1[0];
                $count += $row1[1];
                $totalav += $row1[2];
                $cTotal += $row1[3];
            }
            $inSi++;
        }
    }
    $a = [$count, $totalav, $cTotal];
    return $a;
}

function Core_siRow($mysqli, $employee_id, $ar, $si)
{
    $count = 0;
    $cTotal = 0;
    $a = 0;
    if ($si != "") {
        $check = "SELECT * from spms_corefucndata where p_id='$si[mi_id]' and empId='$employee_id'";
        $check = $mysqli->query($check);
        if ($check->num_rows > 0) {
            $SiData = $check->fetch_assoc();
            $div = 0;
            if (!$SiData['disable']) {
                if ($SiData['Q'] != "") {
                    $a += $SiData['Q'];
                    $div += 1;
                }
                if ($SiData['E'] != "") {
                    $a += $SiData['E'];
                    $div += 1;
                }
                if ($SiData['T'] != "") {
                    $a += $SiData['T'];
                    $div += 1;
                }
                $a = ($a / $div) * ($SiData['percent'] / 100);
                $a = mb_substr($a, 0, 4);
            }
            $cTotal++;
        } else {
            $count++;
        }
    }
    $ar = ["", $count, $a, $cTotal];
    return $ar;
}
