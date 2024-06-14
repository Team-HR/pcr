<?php
require_once "assets/libs/FinalNumericalRatings.php";
require_once "config.php";

if (isset($_POST["getList"])) {
  $period = $_SESSION["periodPending"];
  $empId =  $_SESSION["emp_id"];

  $sql = "SELECT * from spms_performancereviewstatus where period_id='$period' and (`ImmediateSup`='$empId' or `DepartmentHead` = '$empId')  ";
  $res = $mysqli->query($sql);

  $data = [];

  while ($row = $res->fetch_assoc()) {
    $data[] = $row;
  }

  if (count($data) < 1) {
    echo json_encode([]);
    return false;
  }


  $finalNumericalRating = new FinalNumericalRating();

  $departments = [];

  foreach ($data as $key => $personnel) {
    $full_name = getPersonnelName($mysqli, $personnel["employees_id"]);
    $data[$key]["full_name"] = $full_name;
    $final_numerical_rating_recomp = $finalNumericalRating->getFinalNumericalRating($mysqli, $personnel);
    $data[$key]["final_numerical_rating_recomp"] = number_format($final_numerical_rating_recomp, 2);
    $data[$key]["final_numerical_rating_recomp_scale"] = getScale($final_numerical_rating_recomp);

    $department_id = $personnel["department_id"];

    if ($index = findObjectIndexByProperty($departments, "department_id", $department_id)) {
      if ($index != -1) {
        // $departments[$index]['personnel'][] = $data[$key];
        array_push($departments[$index]["personnel"], $data[$key]);
      } else {
        $departments[] = [
          "department_id" => $department_id,
          "personnel" => [
            $data[$key]
          ]
        ];
      }
    }

    // $departments[] = $data[$key]["department_id"];
  }




  // usort($data, fn ($a, $b) => strcmp($b['final_numerical_rating_recomp'], $a['final_numerical_rating_recomp']));


  // $data[] = [
  //   "full_name" => "SESSION",
  //   "final_numerical_rating" => $period
  // ];


  echo json_encode($departments);
}


function getScale($final_numerical_rating)
{
  $scale = "";
  if ($final_numerical_rating <= 5 && $final_numerical_rating > 4) {
    $scale = "Outstanding";
  } elseif ($final_numerical_rating <= 4 && $final_numerical_rating > 3) {
    $scale = "Very Satisfactory";
  } elseif ($final_numerical_rating <= 3 && $final_numerical_rating > 2) {
    $scale = "Satisfactory";
  } elseif ($final_numerical_rating <= 2 && $final_numerical_rating > 1) {
    $scale = "Unsatisfactory";
  } else {
    $scale = "---";
  }

  return $scale;
}

function getPersonnelName($mysqli, $empid)
{
  if (!$empid) return "";
  $sql = "SELECT * FROM `employees` WHERE `employees_id` = '$empid'";
  $res = $mysqli->query($sql);
  $full_name = "";
  if ($row = $res->fetch_assoc()) {
    $extName = $row["extName"] ? " " . $row["extName"] : "";
    $middleName = $row["middleName"] ? " " . $row["middleName"][0] . "." : "";
    $full_name = $row["lastName"] . ", " . $row["firstName"] . $middleName . $extName;
    $full_name = mb_convert_case($full_name, MB_CASE_UPPER);
  }

  return $full_name;
}


function findObjectIndexByProperty($array, $property, String $value)
{
  foreach ($array as $index => $object) {
    if (isset($object[$property]) && $object[$property] == $value) {
      return $index; // Return the index if a match is found
    }
  }
  return -1; // Return -1 if no match is found
}
