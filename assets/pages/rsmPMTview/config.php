<?php
// require "./assets/libs/rsm_class.php";
$rsmView = new RsmClass($host, $usernameDb, $password, $database);

// echo json_encode($DATA);
if (isset($_POST['getIRM'])) {
    $getIRM = explode('||', $_POST['getIRM']);
    $irm = new IRM($host, $usernameDb, $password, $database);
    $irm->set_cardi($getIRM[0], $getIRM[1], $getIRM[2]);
    echo "<table class='ui celled table'>
            <thead>
                <tr>
                    <th rowspan='2' style='padding:20px'>MFO / PAP</th>
                    <th rowspan='2'>Success Indicator</th>
                    <th colspan='3' style='width:40px'>Rating Matrix</th>
                </tr>
                <tr style='font-size:12px'>
                    <th>Q</th>
                    <th>E</th>
                    <th>T</th>
                </tr>
            </thead>
            <tbody>
                " . $irm->get_view() . "
            </tbody>
        </table>";
} elseif (isset($_POST['siCorrection']) || isset($_POST['mfoCorrection'])) {
    $correction = nl2br($_POST['correction']);
    if (isset($_POST['mfoCorrection'])) {
        $datID = $_POST['mfoCorrection'];
        $getMFO = "SELECT * FROM `spms_corefunctions` where `cf_ID`='$datID'";
    } elseif (isset($_POST['siCorrection'])) {
        $datID = $_POST['siCorrection'];
        $getMFO = "SELECT * FROM `spms_matrixindicators` where `mi_id`='$datID'";
    }
    $a = [$correction, 0];
    $getMFO = $mysqli->query($getMFO);
    $getMFO = $getMFO->fetch_assoc();
    $c = [];
    if ($getMFO['corrections']) {
        $c = unserialize($getMFO['corrections']);
    }
    $c[] = $a;
    $c = $mysqli->real_escape_string(serialize($c));
    $sql = "UPDATE `spms_matrixindicators` SET `corrections` = '$c' WHERE `spms_matrixindicators`.`mi_id` = $datID";
    if (isset($_POST['mfoCorrection'])) {
        $sql = "UPDATE `spms_corefunctions` SET `corrections` = '$c' WHERE `spms_corefunctions`.`cf_ID` = $datID";
    }
    $sql = $mysqli->query($sql);
    if (!$sql) {
        echo "error";
    } else {
        echo 1;
    }
} else if (isset($_POST['showCorrections']) || isset($_POST['showCorrectionsMFO'])) {
    $mfo = 0;
    if (isset($_POST['showCorrections'])) {
        $i = $_POST['showCorrections'];
        $sql = "SELECT * FROM `spms_matrixindicators` WHERE `spms_matrixindicators`.`mi_id` ='$_POST[showCorrections]'";
    } else {
        $sql = "SELECT * FROM `spms_corefunctions` WHERE `spms_corefunctions`.`cf_ID` ='$_POST[showCorrectionsMFO]'";
        $i = $_POST['showCorrectionsMFO'];
        $mfo = 1;
    }
    $sql = $mysqli->query($sql);
    $view = "";
    $c = $sql->fetch_assoc();
    $c = unserialize($c['corrections']);
    $count = 0;
    while ($count < count($c)) {
        $state = "<b style='color:red'>Unaccomplished</b>";
        $removeBTN = "
                <button class='ui icon red button' data-target='removeCorrection' data-id='$i||$count||$mfo'>
                    Remove
                </button>
            ";
        if ($c[$count][1]) {
            $state = "<b style='color:green'>Accomplished</b>";
            $removeBTN = "";
        }
        $view .= "
                <tr>
                    <td>" . $c[$count][0] . "</td>
                    <td>$state</td>
                    <td style='text-align:center'>$removeBTN</td>
                </tr>
                ";
        $count++;
    }

    echo "<center><table class='ui celled table'>
                <thead>
                  <tr><th>Corrections</th>
                  <th>Status</th>
                  <th></th>
                </tr></thead>
                <tbody>
                $view
                </tbody>
              </table>
            </center>
              ";
} elseif (isset($_POST['removeCorrection'])) {
    $arIndex = explode('||', $_POST['arIndex']);
    if ($arIndex[2]) {
        $sql = "SELECT * FROM `spms_corefunctions` where `cf_ID`='$arIndex[0]'";
    } else {
        $sql = "SELECT * FROM `spms_matrixindicators` where `mi_id`='$arIndex[0]'";
    }
    $sql = $mysqli->query($sql);
    $sql = $sql->fetch_assoc();
    if ($sql['corrections'] != "") {
        $c = unserialize($sql['corrections']);

        array_splice($c, $arIndex[1], 1);
        if (!$c) {
            $c = "";
        } elseif (count($c) >= 1) {
            $c = $mysqli->real_escape_string(serialize($c));
        } else {
            $c = "";
        }
        if ($arIndex[2]) {
            $s = "UPDATE `spms_corefunctions` SET `corrections` = '$c' WHERE `spms_corefunctions`.`cf_ID` = '$arIndex[0]'";
        } else {
            $s = "UPDATE `spms_matrixindicators` SET `corrections` = '$c' WHERE `spms_matrixindicators`.`mi_id` = '$arIndex[0]'";
        }
        $s = $mysqli->query($s);
        if ($s) {
            echo 1;
        } else {
            echo "something went Wrong";
        }
    }
}

/*
    pmt corrections
    controller    
*/

// remove_mfo_correction: true,
//                             index: index,
//                             cf_ID: this.mfo_edit_item.id
elseif (isset($_GET["get_rating_scale_matrix"])) {
    $data = [];
    $period_id = $_GET["period_id"];
    $department_id = $_GET["department_id"];
    $rsmView->set_period($period_id);
    $rsmView->set_department($department_id);
    // $rsmView->get_rating_scale_matrix();
    $data = $rsmView->get_rating_scale_matrix_rows();
    echo json_encode($data);
} elseif (isset($_POST["add_correction"])) {
    $cf_ID = $_POST["cf_ID"];
    $correction = $_POST["correction"];
    if (!$correction) {
        return false;
    }
    $pmt_name = $_SESSION["emp_info"]["lastName"] . ", " . $_SESSION["emp_info"]["firstName"];
    $timestamp = date("Y-m-d H:i:s", time());

    $correction = "<b>$pmt_name</b> - <i>$timestamp</i>:<br>" . $correction;
    // $correction = $mysqli->real_escape_string($correction);

    $corrections = [];

    # check if there are existing corrections
    $sql = "SELECT * FROM `spms_corefunctions` WHERE `spms_corefunctions`.`cf_ID` = '$cf_ID';";
    $result = $mysqli->query($sql);
    $row = $result->fetch_assoc();
    $corrections = $row["corrections"] ? unserialize($row["corrections"]) : [];

    foreach ($corrections as $corr) {
        if ($corr[1] == 0) {
            echo json_encode(false);
            return false;
        }
    }

    array_unshift($corrections, [$correction, 0]);
    $corrections = serialize($corrections);
    $corrections = $mysqli->real_escape_string($corrections);


    $sql = "UPDATE `spms_corefunctions` SET `corrections` = '$corrections' WHERE `spms_corefunctions`.`cf_ID` = '$cf_ID';";
    $mysqli->query($sql);

    echo json_encode(true);
} elseif (isset($_POST["remove_mfo_correction"])) {
    $index = $_POST["index"]; //though no need since its always the index 0 to be deleted
    $cf_ID = $_POST["cf_ID"];

    # get the corrections first
    $sql = "SELECT * FROM `spms_corefunctions` WHERE `cf_ID` = '$cf_ID'";
    $result = $mysqli->query($sql);
    $row = $result->fetch_assoc();
    $corrections = unserialize($row["corrections"]);
    array_splice($corrections, $index, 1);

    # save new corrections to db
    $corrections = $mysqli->real_escape_string(serialize($corrections));

    $sql = "UPDATE `spms_corefunctions` SET `corrections` = '$corrections' WHERE `cf_ID` = '$cf_ID'";
    $mysqli->query($sql);
    echo json_encode(true);
}

// add_si_correction: true,
// mi_id: this.si_edit_item.mi_id,
// correction: this.si_correction

elseif (isset($_POST["add_si_correction"])) {
    $mi_id = $_POST["mi_id"];
    $correction = $_POST["correction"];
    if (!$correction) {
        return false;
    }
    $pmt_name = $_SESSION["emp_info"]["lastName"] . ", " . $_SESSION["emp_info"]["firstName"];
    $timestamp = date("Y-m-d H:i:s", time());

    $correction = "<b>$pmt_name</b> - <i>$timestamp</i>:<br>" . $correction;
    // $correction = $mysqli->real_escape_string($correction);

    $corrections = [];

    # check if there are existing corrections SELECT * FROM `spms_matrixindicators` WHERE `mi_id` = '10103';

    $sql = "SELECT * FROM `spms_matrixindicators` WHERE `spms_matrixindicators`.`mi_id` = '$mi_id';";
    $result = $mysqli->query($sql);
    $row = $result->fetch_assoc();
    $corrections = $row["corrections"] ? unserialize($row["corrections"]) : [];

    foreach ($corrections as $corr) {
        if ($corr[1] == 0) {
            echo json_encode(false);
            return false;
        }
    }

    array_unshift($corrections, [$correction, 0]);
    $corrections = serialize($corrections);
    $corrections = $mysqli->real_escape_string($corrections);


    $sql = "UPDATE `spms_matrixindicators` SET `corrections` = '$corrections' WHERE `spms_matrixindicators`.`mi_id` = '$mi_id';";
    $mysqli->query($sql);

    echo json_encode(true);
} elseif (isset($_POST["remove_si_correction"])) {
    $index = $_POST["index"]; //though no need since its always the index 0 to be deleted
    $mi_id = $_POST["mi_id"];

    # get the corrections first
    $sql = "SELECT * FROM `spms_matrixindicators` WHERE `mi_id` = '$mi_id'";
    $result = $mysqli->query($sql);
    $row = $result->fetch_assoc();
    $corrections = unserialize($row["corrections"]);
    array_splice($corrections, $index, 1);

    # save new corrections to db
    $corrections = $mysqli->real_escape_string(serialize($corrections));

    $sql = "UPDATE `spms_matrixindicators` SET `corrections` = '$corrections' WHERE `mi_id` = '$mi_id'";
    $mysqli->query($sql);
    echo json_encode(true);
}
