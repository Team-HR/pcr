<?php
require_once "assets/pages/performanceRating/config.php";
function pendingTable($mysqli)
{
  $table = "";
  $period = $_SESSION['periodPending'];
  $gperiod = "SELECT * from spms_mfo_period where mfoperiod_id='$period'";
  $gperiod = $mysqli->query($gperiod);
  $gperiod = $gperiod->fetch_assoc();
  $empId = $_SESSION['emp_id'];
  $sql = "SELECT * from spms_performancereviewstatus where period_id='$period' and ImmediateSup='$empId'  ORDER BY `spms_performancereviewstatus`.`approved` ASC";
  $sql = $mysqli->query($sql);
  $tr = "";
  while ($data = $sql->fetch_assoc()) {
    $fsql = "SELECT * from employees where employees_id='$data[employees_id]'";
    $fsql = $mysqli->query($fsql);
    $fsql = $fsql->fetch_assoc();
    if ($data['ImmediateSup'] != $data['DepartmentHead']) {
      $color = "#BEBEBE";
      $statusText = "$data[dateAccomplished] - Accomplished";
      if ($data['panelApproved']) {
        $statusText = "$data[panelApproved] - Checked by PMT";
      } elseif ($data['certify']) {
        $statusText = "$data[certify] - Certified by Department Head";
      } elseif ($data['approved'] != "") {
        $color = "#00FA9A";
        $statusText = "$data[approved] - Approved";
      }

      if (checkIfCouncilor($mysqli, $data['employees_id'])) {
        $tr .= "
        <tr style='background:$color'>
        <td>$fsql[firstName] $fsql[lastName]</td>
        <td></td>
        </tr>";
      } else {
        $tr .= "
        <tr onclick='UncriticizedEmpIdFunc(\"$data[employees_id]\")' style='background:$color'>
        <td>$fsql[firstName] $fsql[lastName]</td>
        <td>statusText</td>
        </tr>";
      }
    }
  }
  $DepartmentHeadData = "SELECT * from `spms_performancereviewstatus` where `period_id`= '$period' and `DepartmentHead`='$empId'";
  $DepartmentHeadData = $mysqli->query($DepartmentHeadData);
  if ($DepartmentHeadData->num_rows > 0) {
    while ($getDepartmentHeadData = $DepartmentHeadData->fetch_assoc()) {
      $fsql = "SELECT * from employees where employees_id='$getDepartmentHeadData[employees_id]'";
      $fsql = $mysqli->query($fsql);
      $fsql = $fsql->fetch_assoc();
      $color = "#BEBEBE";

      # status text start
      $statusText = "Accomplished";
      if (isset($data['panelApproved'])) {
        $statusText = "$data[panelApproved] - Checked by PMT";
      } elseif ($getDepartmentHeadData['approved'] != "" || $getDepartmentHeadData['certify'] != "") {
        $color = "#00FA9A";
        $statusText = " Certified";
      }
      # status text end

      if ($getDepartmentHeadData['ImmediateSup'] == $getDepartmentHeadData['DepartmentHead'] || $getDepartmentHeadData['ImmediateSup'] == "") {
        // $tr .= "
        //     <tr onclick='UncriticizedEmpIdFunc(\"$getDepartmentHeadData[employees_id]\")' style='background:$color'>
        //     <td>$fsql[firstName] $fsql[lastName]</td>
        //     <td>$getDepartmentHeadData[dateAccomplished] - $statusText</td>
        //     </tr>
        //     ";

        if (checkIfCouncilor($mysqli, $getDepartmentHeadData['employees_id'])) {
          $tr .= "
            <tr style='background:$color'>
            <td>$fsql[firstName] $fsql[lastName]</td>
            <td></td>
            </tr>
            ";
        } else {
          $tr .= "
            <tr onclick='UncriticizedEmpIdFunc(\"$getDepartmentHeadData[employees_id]\")' style='background:$color'>
            <td>$fsql[firstName] $fsql[lastName]</td>
            <td>$getDepartmentHeadData[dateAccomplished] - $statusText</td>
            </tr>
            ";
        }



        $tr .= subordinates($getDepartmentHeadData['employees_id'], $period);
      }
    }
  }

  $data = get_subordinates($mysqli, $period, $empId);
  $json = json_encode($data, JSON_PRETTY_PRINT);
  $view =  "<div style='text-align: left; width: 100%; background-color: white'>";
  $view .=  "<pre>" . $json . "</pre>";
  $view .= "</div>";

  // return $view;

  $table .= "
  <h3> Period of $gperiod[month_mfo] $gperiod[year_mfo] </h3>
  <table class='ui basic small selectable table' style='width:95%;cursor:pointer;background:white'>
  <thead >
  <tr style='background:#0b56ff30;'>
  <th colspan='2'>
  <h2 class='ui header'>
  <i class='settings icon'></i> 
  <div class='content'>
  Raw Data
  <div class='sub header'>Manage your subordinates Data</div>
  </div>
  </h2>
  </th>
  </tr>
  <tr style='background:#2c193e0d;'>
  <th> Employee Name </th>
  <th> Date </th>
  </tr>
  </thead>
  <tbody>
  $tr
  </tbody>
  </table>
  ";
  return $table;
}



function get_employee_name($mysqli, $employee_id)
{
  $sql = "SELECT * from employees where employees_id='$employee_id'";
  $res = $mysqli->query($sql);

  $name = "";

  if ($row = $res->fetch_assoc()) {
    $name = "$row[lastName], $row[firstName]";
    if ($row['extName']) {
      $name .= " " . $row['extName'];
    }
    $name = mb_strtoupper($name);
  }

  return $name;
}


function get_subordinates($mysqli, $period_id, $employee_id)
{
  $sql = "SELECT * FROM `spms_performancereviewstatus` where (ImmediateSup = '$employee_id' OR DepartmentHead = '$employee_id') AND period_id = '$period_id' ORDER BY `spms_performancereviewstatus`.`dateAccomplished` ASC
  ";

  $res = $mysqli->query($sql);
  $personnel = [];

  while ($row = $res->fetch_assoc()) {
    $personnel[] = $row;
  }

  // $tr = "";

  $data = [];
  foreach ($personnel as $key => $pcr) {
    $datum = [
      "employee_id" => $pcr['employees_id'],
      "employee_id_supervisor" => $pcr['ImmediateSup'],
      "name" => get_employee_name($mysqli, $pcr['employees_id']),
      "status" => [
        "submitted" => $pcr['submitted'],
        "date_submitted" => $pcr['dateAccomplished'],
        "date_approved" => $pcr['approved'],
        "date_certified" => $pcr['certify'],
        "date_pmt_approved" => $pcr['panelApproved']
      ]
    ];


    $data[] = $datum;

    // $status = json_encode($status);
    // $tr .= "<tr onclick='UncriticizedEmpIdFunc(\"$employee_id\")' style='background:_'>";
    // $tr .= "<td>" . get_employee_name($mysqli, $employee_id) . "</td>";
    // $tr .= "<td>$status</td>";
    // $tr .= "</tr>";
  }


  return $data;
}





























####
####
function subordinates($dat, $period)
{
  $mysqli = $GLOBALS['mysqli'];
  $sql = "SELECT * from `spms_performancereviewstatus` where `ImmediateSup`='$dat' and `period_id`='$period'";
  $sql = $mysqli->query($sql);
  $tr = "";
  while ($ipcr = $sql->fetch_assoc()) {
    $fsql = "SELECT * from employees where employees_id='$ipcr[employees_id]'";
    $fsql = $mysqli->query($fsql);
    $fsql = $fsql->fetch_assoc();
    if ($ipcr['panelApproved'] != "") {
      // $tr .= "
      // <tr onclick='UncriticizedEmpIdFunc(\"$ipcr[employees_id]\")' style='background:#00FA9A'>
      // <td style='padding-left:50px'><i class='minus icon'></i>
      // $fsql[firstName] $fsql[lastName]</td>
      // <td>$ipcr[panelApproved] - Checked by PMT</td>
      // </tr>
      // ";



      if (checkIfCouncilor($mysqli, $ipcr['employees_id'])) {
        $tr .= "
        <tr style='background:#00FA9A'>
        <td style='padding-left:50px'><i class='minus icon'></i>
        $fsql[firstName] $fsql[lastName]</td>
        <td></td>
        </tr>
        ";
      } else {
        $tr .= "
        <tr onclick='UncriticizedEmpIdFunc(\"$ipcr[employees_id]\")' style='background:#00FA9A'>
        <td style='padding-left:50px'><i class='minus icon'></i>
        $fsql[firstName] $fsql[lastName]</td>
        <td>$ipcr[panelApproved] - Checked by PMT </td>
        </tr>
        ";
      }

      $tr .= subordinates($ipcr['employees_id'], $period);
      // $tr .= "
      //   <tr style='background:red'>
      //   <td style='padding-left:50px'><i class='minus icon'></i>
      //  TEST</td> 
      //   <td>TEST</td>
      //   </tr>
      //   ";
    } elseif ($ipcr['certify'] != "") {
      // $tr .= "
      // <tr onclick='UncriticizedEmpIdFunc(\"$ipcr[employees_id]\")' style='background:#00FA9A'>
      // <td style='padding-left:50px'><i class='minus icon'></i>
      // $fsql[firstName] $fsql[lastName]</td>
      // <td>$ipcr[certify] - Certified by Department Head</td>
      // </tr>
      // ";

      if (checkIfCouncilor($mysqli, $ipcr['employees_id'])) {
        $tr .= "
        <tr style='background:#00FA9A'>
        <td style='padding-left:50px'><i class='minus icon'></i>
        $fsql[firstName] $fsql[lastName]</td>
        <td></td>
        </tr>
        ";
      } else {
        $tr .= "
        <tr onclick='UncriticizedEmpIdFunc(\"$ipcr[employees_id]\")' style='background:#00FA9A'>
        <td style='padding-left:50px'><i class='minus icon'></i>
        $fsql[firstName] $fsql[lastName]</td>
        <td>$ipcr[certify] - Certified by Department Head</td>
        </tr>
        ";
      }
      $tr .= subordinates($ipcr['employees_id'], $period);
      // $tr .= "
      //   <tr style='background:red'>
      //   <td style='padding-left:50px'><i class='minus icon'></i>
      //  TEST</td> 
      //   <td>TEST</td>
      //   </tr>
      //   ";
    } elseif ($ipcr['approved'] != "") {
      // $tr .= "
      // <tr onclick='UncriticizedEmpIdFunc(\"$ipcr[employees_id]\")' style='background:#E8E8E8'>
      // <td style='padding-left:50px'><i class='minus icon'></i>
      // $fsql[firstName] $fsql[lastName]</td> 
      // <td>$ipcr[approved] - Approved by supervisor</td>
      // </tr>
      // ";


      if (checkIfCouncilor($mysqli, $ipcr['employees_id'])) {
        $tr .= "
        <tr style='background:#E8E8E8'>
        <td style='padding-left:50px'><i class='minus icon'></i>
        $fsql[firstName] $fsql[lastName]</td> 
        <td></td>
        </tr>
        ";
      } else {
        $tr .= "
        <tr onclick='UncriticizedEmpIdFunc(\"$ipcr[employees_id]\")' style='background:#E8E8E8'>
        <td style='padding-left:50px'><i class='minus icon'></i>
        $fsql[firstName] $fsql[lastName]</td> 
        <td>$ipcr[approved] - Approved by supervisor</td>
        </tr>
        ";
      }
      $tr .= subordinates($ipcr['employees_id'], $period);
      
      // $tr .= "
      //   <tr style='background:red'>
      //   <td style='padding-left:50px'><i class='minus icon'></i>
      //  TEST</td> 
      //   <td>TEST</td>
      //   </tr>
      //   ";
    } else {
      // - Unapproved (Needs supervisor's developmental comments/recommendations)
      $tr .= "
      <tr style='background:#f1dbd4'>
      <td style='padding-left:50px'><i class='minus icon'></i>
      $fsql[firstName] $fsql[lastName]</td>
      <td>$ipcr[dateAccomplished] - Unapproved (Form not submitted or needs supervisor's approval)</td>
      </tr>
      ";
      $tr .= subordinates($ipcr['employees_id'], $period);

    }
    
  }
  return $tr;
}


function checkIfCouncilor($mysqli, $employees_id)
{
  // return true;
  // if (!$employees_id) {
  //   return false;
  // }
  $isCouncilor = false;
  $department_id = 34; //department_id of vice mayor's office

  $sql = "SELECT * FROM `employees` WHERE employmentStatus = 'ELECTIVE' AND department_id = '$department_id' AND employees_id='$employees_id'";
  $res = $mysqli->query($sql);
  if ($res->num_rows > 0) {
    $isCouncilor = true;
  }
  return $isCouncilor;
}


function uncriticizedTable($employee)
{
  $emp = $_SESSION['empIdPending'];
  $period = $_SESSION['periodPending'];
  $employee->set_emp($emp);
  $employee->set_period($period);
  $employee->hideNextBtn();
  $employee->set_hide("display:none");
  $body = $employee->get_strategicView() . $employee->get_coreView() . $employee->get_supportView();
  $table_r = new table($employee->hideCol);
  $table_r->formType($employee->get_status('formType'));
  $table_r->set_head($employee->tableHeader());
  $table_r->set_body($body);
  $table_r->set_foot($employee->tableFooter() . "<br class='noprint'>" . $employee->get_approveBTN());
  return $table_r->_get();
}
