<script src="assets/libs/jquery/jquery-3.3.1.min.js"></script>
<!-- <button onclick="test()">Compute</button> -->


<script>
    test();

    function test() {
        $.post('?config=rsm', {
            copy_to: true
        }, function(data, textStatus, xhr) {
            // location.reload();
            console.log(data);
        })
    }
</script>

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

$period_id = 10; //10 - July to Dec 2022

# performanceReviewStatus_id = 2434 test fomtype 3 strategic function shoul be excluded from computing final numerical rating
// $sql = "SELECT * FROM `spms_performancereviewstatus` where period_id = '$period_id' LIMIT 20 -- and performanceReviewStatus_id = '2434';";
// $res = $mysqli->query($sql);
// $data = [];
// while ($row = $res->fetch_assoc()) {
//     $row['final_numerical_rating'] = getFinalNumericalRating($mysqli, $row);
//     $data[] = $row;
// }
// print("<pre>" . print_r($data, true) . "</pre>");

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


function getFinalNumericalRating($mysqli, $fileStatus)
{

    $formType = $fileStatus['formType'];

    $strategic = strategicTr($mysqli, $fileStatus);
    $core = coreRow($mysqli, $fileStatus);
    $support = supportFunctionTr($mysqli, $fileStatus);

    $final_numerical_rating = '';

    if ($strategic > 0 && $core > 0 && $support > 0) {
        $final_numerical_rating = $strategic + $core + $support;
    }

    if ($formType == '3') {
        $final_numerical_rating = $core + $support;
    }
    // $scale = "";
    // if ($final_numerical_rating <= 5 && $final_numerical_rating > 4) {
    //     $scale = "Outstanding";
    // } elseif ($final_numerical_rating <= 4 && $final_numerical_rating > 3) {
    //     $scale = "Very Satisfactory";
    // } elseif ($final_numerical_rating <= 3 && $final_numerical_rating > 2) {
    //     $scale = "Satisfactory";
    // } elseif ($final_numerical_rating <= 2 && $final_numerical_rating > 1) {
    //     $scale = "Unsatisfactory";
    // }

    // return [
    //     "strategic" => $strategic,
    //     "core" => $core,
    //     "support" => $support,
    //     "total" => $final_numerical_rating,
    //     "scale" => $scale
    // ];
    return $final_numerical_rating;
}


function coreRow($mysqli, $fileStatus)
{
    $arr = coreAr($mysqli, $fileStatus);
    // return $arr;
    $employee_id = $fileStatus['employees_id'];
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
    return bcdiv($totalav, 1, 2);
}


function coreAr($mysqli, $fileStatus = [])
{
    # for more compact and faster query
    # ... and `dep_id` = '$department_id'

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
    if (count($ar[1]) > 0) {
        while ($inSi < count($ar[1])) {
            if ($inSi == 0) {
                $row0 = Core_siRow($mysqli, $employee_id, $ar[0], $ar[1][$inSi]);
                $count += $row0[0];
                $totalav += $row0[1];
                $cTotal += $row0[2];
            } else {
                $row1 = Core_siRow($mysqli, $employee_id, ['cf_count' => '', 'cf_title' => ''], $ar[1][$inSi]);
                $count += $row1[0];
                $totalav += $row1[1];
                $cTotal += $row1[2];
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
    $ar = [$count, $a, $cTotal];
    return $ar;
}


function strategicTr($mysqli, $fileStatus)
{

    $period_id = $fileStatus['period_id'];
    $employee_id = $fileStatus['employees_id'];

    $sql = "SELECT * from spms_strategicfuncdata where period_id = '$period_id' and emp_id = '$employee_id'";
    $sql = $mysqli->query($sql);
    $totalCount = 0;
    $totalAv = 0;
    while ($row = $sql->fetch_assoc()) {
        // $av = $row['Q']+$row['T'];
        $av = isset($row['average']) && $row['average'] > 0 ? $row['average'] : 0;
        $col = "";
        $totalAv += $av;
        $totalCount++;
    }

    if ($totalAv > 0) {
        $totalAv = $totalAv / $totalCount;
    } else {
        $totalAv = 0;
    }
    if ($totalAv > 0) {
        $totalAv = $totalAv * 0.20;
        # format only two decimal places
        // $totalAv = number_format($totalAv, 2);
        // $totalAv = bcdiv($totalAv, 1, 2);
        # prevent rounding off value
        // $totalAv = intval(($totalAv * 100)) / 100;
    } else {
        $totalAv = 0;
    }
    // $totalAv = $totalAv*0.20;
    // $totalAv = $totalAv;
    return bcdiv($totalAv, 1, 2);
}


function supportFunctionTr($mysqli, $fileStatus)
{
    $formType = $fileStatus['formType'];
    $employee_id = $fileStatus['employees_id'];
    $period_id = $fileStatus['period_id'];
    $totalAv = 0;
    if ($formType == '1' || $formType == '5') {
        $sql = "SELECT * FROM `spms_supportfunctions` where `type`=1";
    } elseif ($formType == '3') {
        $sql = "SELECT * FROM `spms_supportfunctions` where `type`=3";
    } elseif ($formType == '2' || $formType == '4') {
        $sql = "SELECT * FROM `spms_supportfunctions` where `type`=2";
    } else {
        return bcdiv($totalAv, 1, 2);
    }

    $sql = $mysqli->query($sql);

    $emp_count = 0;

    while ($tr = $sql->fetch_assoc()) {
        $sqlSelect = "SELECT * from spms_supportfunctiondata where parent_id='$tr[id_suppFunc]' and emp_id='$employee_id' and period_id='$period_id'";
        $sqlSelect = $mysqli->query($sqlSelect);
        $sqlSelectCount = $sqlSelect->num_rows;
        if ($sqlSelectCount > 0) {
            $fdata = $sqlSelect->fetch_assoc();
            $av = 0;
            $per = $fdata['percent'] / 100;
            $q = 0;
            $e = 0;
            $t = 0;

            if ($fdata['Q'] != "") {
                $q = $fdata['Q'] * $per;
            }
            if ($fdata['E'] != "") {
                $q = $fdata['E'] * $per;
            }
            if ($fdata['T'] != "") {
                $q = $fdata['T'] * $per;
            }
            $av = $q + $e + $t;
            $col = "";

            $totalAv += $av;
        } else {
            $emp_count++;
        }
    }

    return bcdiv($totalAv, 1, 2);
}
