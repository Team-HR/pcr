<?php

/**
 *
 */
$host = "localhost";
$password = "teamhrmo2019";
// $password = "";
$username = "admin";
$database = "ihris";
class IPCR extends mysqli
{

  private $EmpId;
  private $period;
  private $department;
  private $per;
  private $matrix;
  private $totalCore;
  private $coreDev;
  private $totalStrat;
  private $totalSupp;
  /*
  ** storage  **
    strings are store here
    used to form a table
    IN table row form
*/
  private $core;
  private $support;
  private $strat;
  /*
  ** fileStatus **
  an array bieng fetched from database
  indicates the status of a file
*/
  private $fileStatus;
  /*
  ** FILE Type **
    set what kind of file you want to retrieve

  ** keywords **

    matrix - this option well show all the rating indacators 
    performance - shows all the grade of the individual employee
*/
  private $fileType;
  function __construct()
  {
    $host = "localhost";
    $password = "teamhrmo2019";
    // $password = "";
    $username = "admin";
    $database = "ihris";
    parent::__construct($host, $username, $password, $database);
    parent::set_charset("utf8");
  }
  public function get_data($sql)
  {
    $query = parent::query($sql);
    if ($query->num_rows == 1) {
      $arr = $query->fetch_assoc();
    } else {
      $arr = [];
      while ($data = $query->fetch_assoc()) {
        array_push($arr, $data);
      }
    }
    return $arr;
  }
  public function set_fileType($data)
  {
    $this->fileType = $data;
    $this->build();
  }
  public function utl_set($period, $department)
  {
    $this->period = $period;
    $this->department = $department;
    $this->build();
  }
  public function EmpId_set($EmpId)
  {
    $this->EmpId = $EmpId;
    if ($this->period && $this->department && $this->EmpId) {
      $sql = "SELECT * from `spms_performancereviewstatus` left join `spms_mfo_period` on `spms_performancereviewstatus`.`period_id`=`spms_mfo_period`.`mfoperiod_id` left join `department` on `spms_performancereviewstatus`.`department_id`=`department`.`department_id` where `spms_performancereviewstatus`.`employees_id`='$this->EmpId' and `spms_performancereviewstatus`.`department_id`='$this->department' and `spms_performancereviewstatus`.`period_id`='$this->period'";
      $sql = parent::query($sql);
      $this->fileStatus = $sql->fetch_assoc();
    }
    $this->build();
  }
  private function build()
  {
    $this->core = "";
    $this->viewController();
    $this->parent();
  }
  private function viewController()
  {
    $fileType = $this->fileType;
    if (strtoupper($this->fileType) == strtoupper("PERFORMANCE")) {
      $this->per = true;
      $this->matrix = false;
    } else {
      $this->per = false;
      $this->matrix = true;
    }
  }
  private function tableRow($indicators)
  {
    $view = "";
    if (strtoupper($this->fileType) == "PERFORMANCE") {
      $sql = "SELECT * from `spms_corefucndata` where `p_id`='$indicators[mi_id]' and `empId`='$this->EmpId'";
      $sql = parent::query($sql);
      $dev = 0;
      $av = 0;
      $empData = $sql->fetch_assoc();
      $added = "";
      if ($this->fileStatus['formType'] == 3) {
        $added .= "<td></td>";
      }
      if ($this->fileStatus['formType'] == 3 || $this->fileStatus['formType'] == 2) {
        $added .= "<td>" . $this->accountblePersons($indicators['cf_ID']) . "</td>";
      }
      if ($empData['Q']) {
        $dev++;
        $av += $empData['Q'];
      }
      if ($empData['E']) {
        $dev++;
        $av += $empData['E'];
      }
      if ($empData['T']) {
        $dev++;
        $av += $empData['T'];
      }
      $av = $av / $dev;
      $view = "
              $added
              <td>" . $empData['actualAcc'] . "</td>
              <td>" . $empData['Q'] . "</td>
              <td>" . $empData['E'] . "</td>
              <td>" . $empData['T'] . "</td>
              <td>$av</td>
            ";
      $this->totalCore += $av;
      $this->coreDev++;
    } else {
      $view = "
              <td>" . $this->convertString($indicators['mi_quality']) . "</td>
              <td>" . $this->convertString($indicators['mi_eff']) . "</td>
              <td>" . $this->convertString($indicators['mi_time']) . "</td>
              <td>" . $this->EmpFullname($indicators['mi_incharge']) . "</td>
          ";
    }
    return $view;
  }
  private function parent()
  {
    $added = "";
    if ($this->fileStatus['formType'] == 3) {
      $added .= "<td></td>";
    }
    if ($this->fileStatus['formType'] == 3 || $this->fileStatus['formType'] == 2) {
      $added .= "<td></td>";
    }

    $sql = "SELECT * FROM `spms_corefunctions` where mfo_periodId='$this->period' and dep_id='$this->department' and parent_id=''";
    $sql = parent::query($sql);
    while ($arr = $sql->fetch_assoc()) {
      $matrixindicators = "SELECT * from spms_matrixindicators where cf_ID='$arr[cf_ID]'";
      $matrixindicators = parent::query($matrixindicators);
      $view = "";
      $child = $this->child($arr['cf_ID'], 20);
      if ($matrixindicators->num_rows) {
        $count = 0;
        while ($indicators = $matrixindicators->fetch_assoc()) {
          if ($this->checker($indicators['mi_incharge']) || !$this->EmpId) {
            if (!$count) {
              $view .=  "<tr>
                                  <td>$arr[cf_title]</td>
                                  <td>" . nl2br($indicators['mi_succIn']) . "</td>
                                  " . $this->tableRow($indicators) . "
                                  <td></td>
                                </tr>";
            } else {
              $view .=  "<tr>
                                  <td></td>
                                  <td>" . nl2br($indicators['mi_succIn']) . "</td>
                                  " . $this->tableRow($indicators) . "                      
                                </tr>";
            }
          }
          $count++;
        }
      } elseif ($child || !$this->EmpId) {
        $view .=  "<tr>
                            <td>$arr[cf_title]</td>
                            <td></td>
                            $added
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                          </tr>";
      }
      $view .= $child;
      $this->core .= $view;
    }
  }
  private function child($p, $s)
  {
    $added = "";
    if ($this->fileStatus['formType'] == 3) {
      $added .= "<td></td>";
    }
    if ($this->fileStatus['formType'] == 3 || $this->fileStatus['formType'] == 2) {
      $added .= "<td></td>";
    }
    $sql1 = "SELECT * FROM `spms_corefunctions` where parent_id='$p'";
    $sql1 = parent::query($sql1);
    $padding = $s . "px";
    $view = "";
    while ($child_arr = $sql1->fetch_assoc()) {
      $matrixindicators = "SELECT * from spms_matrixindicators where cf_ID='$child_arr[cf_ID]'";
      $matrixindicators = parent::query($matrixindicators);
      $sql2 = "SELECT * FROM `spms_corefunctions` where parent_id='$child_arr[cf_ID]'";
      $sql2 = parent::query($sql2);
      $child = "";
      if ($sql2->num_rows) {
        $s += 20;
        $child .= $this->child($child_arr['cf_ID'], $s);
      }
      if ($matrixindicators->num_rows) {
        $count = 0;
        while ($indicators = $matrixindicators->fetch_assoc()) {
          if ($this->checker($indicators['mi_incharge']) || !$this->EmpId) {
            if (!$count) {
              $view .=  "<tr>
                              <td style='padding:10px'> <p style='margin-left:$padding;'> $child_arr[cf_title]</p></td> 
                              <td>" . nl2br($indicators['mi_succIn']) . "</td>
                              " . $this->tableRow($indicators) . "
                              <td></td>
                            </tr>";
            } else {
              $view .=  "<tr>
                            <td></td>
                            <td>" . nl2br($indicators['mi_succIn']) . "</td>
                            " . $this->tableRow($indicators) . "                      
                            <td></td>
                          </tr>";
            }
            $count++;
          }
        }
      } elseif ($child || !$this->EmpId) {
        $view .=  "<tr>
                      <td style='padding:10px'> <p style='margin-left:$padding;'> $child_arr[cf_title]</p></td>
                      $added
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    <tr>";
      }
      $view .= $child;
    }
    return $view;
  }
  private function supportFunction()
  {
    $added = "";
    if ($this->fileStatus['formType'] == 3) {
      $added .= "<td></td>";
    }
    if ($this->fileStatus['formType'] == 3 || $this->fileStatus['formType'] == 2) {
      $added .= "<td></td>";
    }
    $sql = "SELECT `spms_supportfunctions`.`mfo`,`spms_supportfunctions`.`percent`,`spms_supportfunctions`.`suc_in`,`spms_supportfunctiondata`.`accomplishment`,`spms_supportfunctiondata`.`Q`,`spms_supportfunctiondata`.`E`,`spms_supportfunctiondata`.`T` FROM `spms_supportfunctiondata` left join `spms_supportfunctions` on `spms_supportfunctiondata`.`parent_id`=`spms_supportfunctions`.`id_suppFunc` where `spms_supportfunctiondata`.`emp_id`='$this->EmpId' and `spms_supportfunctiondata`.`period_id`='$this->period'";
    $sql = parent::query($sql);
    $view = "";
    while ($support = $sql->fetch_assoc()) {
      $av = 0;
      $dev = 0;
      if ($support['Q']) {
        $dev++;
        $av += $support['Q'];
      }
      if ($support['E']) {
        $dev++;
        $av += $support['E'];
      }
      if ($support['T']) {
        $dev++;
        $av += $support['T'];
      }
      $av = ($support['percent'] / 100) * ($av / $dev);
      $this->totalSupp += $av;
      $view .= "
        <tr>  
          <td>$support[mfo] = $support[percent]%</td>
          <td>$support[suc_in]</td>
          $added
          <td>$support[accomplishment]</td>
          <td>$support[Q]</td>
          <td>$support[E]</td>
          <td>$support[T]</td>
          <td>$av</td>
          <td></td>
        </tr>
      ";
    }
    return $view;
  }
  private function stratFunc()
  {
    $added = "";
    if ($this->fileStatus['formType'] == 3) {
      $added .= "<td></td>";
    }
    if ($this->fileStatus['formType'] == 3 || $this->fileStatus['formType'] == 2) {
      $added .= "<td></td>";
    }
    $sql = "SELECT * from `spms_strategicfuncdata` where `period_id`='$this->period' and `emp_id`='$this->EmpId'";
    $sql = parent::query($sql);
    $view = "";
    $total = 0;
    $count = 0;
    while ($strat = $sql->fetch_assoc()) {
      $av = 0;
      $dev = 0;
      if ($strat['Q']) {
        $dev++;
        $av += $strat['Q'];
      }
      if ($strat['T']) {
        $dev++;
        $av += $strat['T'];
      }
      $av = $av / $dev;
      $total += $av;
      $view .= "
        <tr>
          <td>$strat[mfo]</td>
          <td>$strat[succ_in]</td>
          $added
          <td>$strat[acc]</td>
          <td>$strat[Q]</td>
          <td></td>
          <td>$strat[T]</td>
          <td>$av</td>
          <td></td>
        </tr>
      ";
      $count++;
    }
    $this->totalStrat += $total;
    return $view;
  }
  private function checker($dataId)
  {
    $EmpId = explode(',', $dataId);
    $status = false;
    $count = 0;
    while ($count < count($EmpId)) {
      if ($this->EmpId == $EmpId[$count]) {
        $status = true;
        break;
      }
      $count++;
    }
    return $status;
  }
  private function EmpFullname($data)
  {
    $EmpId = explode(',', $data);
    $count = 0;
    $view = '';
    while ($count < count($EmpId)) {
      $sql = "SELECT * FROM `employees` where employees_id='$EmpId[$count]'";
      $sql = parent::query($sql);
      $sql = $sql->fetch_assoc();
      $view .= $sql['firstName'] . " " . $sql['middleName'] . " " . $sql['lastName'] . "<br><br>";
      $count++;
    }
    return $view;
  }
  private function convertString($ser)
  {
    $view = "";
    $ser = unserialize($ser);
    $count = 0;
    while ($count < count($ser)) {
      if ($ser[$count]) {
        $view .= "$count - $ser[$count] <br>";
      }
      $count++;
    }
    return $view;
  }

  public function _get()
  {
    $added = "";
    $headerCol = 8;
    if ($this->fileStatus['formType'] == 3) {
      $added .= "<th rowspan='2'>  Alloted Budget for " . $this->fileStatus['year_mfo'] . " (whole year)</th>";
      $headerCol++;
    }
    if ($this->fileStatus['formType'] == 3 || $this->fileStatus['formType'] == 2) {
      $added .= "<th rowspan='2'> Individual/s or Division Accountable </th>";
      $headerCol++;
    }

    if ($this->per) {
      $headerString = "
              <thead style='text-align:center;background:#00c4ff36'>
                <tr>
                  <th rowspan='2'>MFO / PAP</th>
                  <th rowspan='2'>Success indicators</th>
                  $added
                  <th rowspan='2'>Actual Accomplishments</th>
                  <th colspan='4'> Rating Matrix </th>
                  <th rowspan='2'>Remarks</th>
                </tr>
                <tr>
                  <th width='50px'>Q</th>
                  <th width='50px'>E</th>
                  <th width='50px'>T</th>
                  <th width='50px'>A</th>
                </tr>
            </thead>
      ";
    } elseif ($this->matrix) {
      $headerString = "
              <thead style='text-align:center;background:#00c4ff36'>
                <tr>
                  <th rowspan='2'>MFO / PAP</th>
                  <th rowspan='2'>Success indicators</th>
                  <th rowspan='2'>Success indicators</th>
                  <th colspan='3'> Rating Matrix </th>
                  <th rowspan='2'>Incharge</th>
                  <th rowspan='2'>Remarks</th>
                </tr>
                <tr>
                  <th>Q</th>
                  <th>E</th>
                  <th>T</th>
                </tr>
            </thead>
      ";
    }
    $view = $this->formHeader($this->fileStatus['formType']) . "
          <table class='table-bordered borderColor' width='100%'>
            $headerString
            <tbody>
              <tr>
                <td colspan='$headerCol' style='font-size:13px;background:#f7f70026'><b>Strategic Functions</b></td>
              </tr>
               " . $this->stratFunc() . "
              <tr>
                <td colspan='$headerCol' style='font-size:13px;background:#f7f70026'><b>Core Functions</b></td>
              </tr>
               " . $this->core . " 
              <tr>
                <td colspan='$headerCol' style='font-size:13px;background:#f7f70026'><b>Support Functions</b></td>
              </tr>
               " . $this->supportFunction() . "
            </tbody>
          </table>
    " . $this->formFooter();
    return $view;
  }
  private function formHeader($type)
  {
    $employee = $this->empDetails($this->EmpId);
    $title = "";
    $cell1 = "";
    $cell2 = "
            <p style='font-size:12px'>Date:</p>
              <div style='text-align:center'>
              <span style='border-bottom:1px solid black;font-size:14px'>&nbsp;</span>
              <p style='font-size:12px'>&nbsp;</p>
            </td>
            ";
    if ($type == 1) {
      $title = "INDIVIDUAL PERFORMANCE COMMITMENT AND REVIEW (IPCR)";
      $cell1 = "
              <p style='font-size:12px'>Reviewed by:</p>
                <div style='text-align:center'>
                <span style='border-bottom:1px solid black;font-size:14px'>" . $this->empDetails($this->fileStatus['ImmediateSup'])['firstName'] . " " . $this->empDetails($this->fileStatus['ImmediateSup'])['lastName'] . "</span>
              <p style='font-size:12px'>Immediate Superior</p>
            ";
      $cell2 = "
              <p style='font-size:12px'>Note by:</p>
                <div style='text-align:center'>
                <span style='border-bottom:1px solid black;font-size:14px'>" . $this->empDetails($this->fileStatus['DepartmentHead'])['firstName'] . " " . $this->empDetails($this->fileStatus['DepartmentHead'])['lastName'] . "</span>
              <p style='font-size:12px'>Department Head</p>
            ";
    } elseif ($type == 2) {
      $title = "SECTION PERFORMANCE COMMITMENT AND REVIEW (SPCR)";
      $cell1 = "
          <p style='font-size:12px'>Reviewed by:</p>
            <div style='text-align:center'>
            <span style='border-bottom:1px solid black;font-size:14px'>" . $this->empDetails($this->fileStatus['DepartmentHead'])['firstName'] . " " . $this->empDetails($this->fileStatus['DepartmentHead'])['lastName'] . "</span>
          <p style='font-size:12px'>Immediate Superior</p>
        ";
    } elseif ($type == 3) {
      $title = "DEPARTMENT PERFORMANCE COMMITMENT AND REVIEW (DPCR)";
      $cell1 = "
          <p style='font-size:12px'>Prepared by:</p>
            <div style='text-align:center'>
            <span style='border-bottom:1px solid black;font-size:14px'>$employee[firstName] $employee[lastName]</span>
          <p style='font-size:12px'>Department Head</p>
        ";
    } elseif ($type == 5) {
      $title = "INDIVIDUAL PERFORMANCE COMMITMENT AND REVIEW (IPCR)";
      $cell1 = "
              <p style='font-size:12px'>Reviewed by:</p>
                <div style='text-align:center'>
                <span style='border-bottom:1px solid black;font-size:14px'>" . $this->fileStatus['ImmediateSup'] . "</span>
              <p style='font-size:12px'>Immediate Superior</p>
            ";
      $cell2 = "
              <p style='font-size:12px'>Note by:</p>
                <div style='text-align:center'>
                <span style='border-bottom:1px solid black;font-size:14px'>" . $this->fileStatus['DepartmentHead'] . "</span>
              <p style='font-size:12px'>Department Head</p>
            ";
    }
    $view = "
        <table class='table-bordered borderColor' style='width:100%;'>
          <tr>
            <td colspan='4'>
              <p style='font-weight:bolder;text-align:center'>$title</p>
              <p style='padding:10px;font-size:14px'>
                 I, <b> $employee[firstName] $employee[lastName] </b>, $employee[position] of the " . $this->fileStatus['department'] . "commit to deliver and agree to be rated on the attainment of the following targets in accordance with the indicated measures for the period " . $this->fileStatus['month_mfo'] . " " . $this->fileStatus['year_mfo'] . "
              </p>
              <div style='width:500px;text-align:center;float:right'>
              <span style='border-bottom:1px solid black;font-size:14px'>$employee[firstName] $employee[lastName]</span>
              <p style='font-size:10px'>
               Ratee
              </p>
              </div>
            </td>  

          </tr>
          <tr style='background:#0080003d'>
            <td>
              $cell1
            </td>
            <td>
              $cell2
            <td>
              <p style='font-size:12px'>Approved By:</p>
              <div style='text-align:center'>
              <span style='border-bottom:1px solid black;font-size:14px'>" . $this->fileStatus['HeadAgency'] . "</span>
              <p style='font-size:12px'>Department Head</p>
            </td>
            <td>
              <p style='font-size:12px'>Date:</p>
              <div style='text-align:center'>
              <span style='border-bottom:1px solid black;font-size:14px'>&nbsp;</span>
              <p style='font-size:12px'>&nbsp;</p>
            </td>
          </tr>
        </table>
    ";
    return $view;
  }

  private function formFooter()
  {
    $totalCore = .6 * ($this->totalCore / $this->coreDev);
    $totalStrat = .2 * $this->totalStrat;
    $total = $totalCore + $totalStrat + $this->totalSupp;

    $view = "<table class='table-bordered borderColor' style='width:100%'>
              <thead>
                <tr>
                  <th style='font-size:10px;background:#f7f70026' colspan='2'>SUMMARY OF RATING</th>
                  <th style='font-size:10px;background:#f7f70026'>TOTAL</th>
                  <th style='font-size:10px;background:#f7f70026'>FINAL NUMERICAL RATING</th>
                  <th style='font-size:10px;background:#f7f70026'>FINAL ADJECTIVAL RATING</th>
                </tr>
              </thead>
              <tbody>
              <tr style='font-size:13px;font-weight:bold'>
                <td>SO</td>
                <td>Formula: ( Total of all average ratings / no. of entries ) x 20%</td>
                <td style='text-align:center'>$totalStrat</td>
                <td rowspan='3' style='text-align:center'>$total</td>
                <td rowspan='3' style='text-align:center'>3.33</td>
              </tr>
              <tr style='font-size:13px;font-weight:bold'>
                <td>CF</td>
                <td>Formula: ( Total of all ratings / no. of entries ) x 60%</td>
                <td style='text-align:center'>$totalCore</td>
              </tr>
              <tr style='font-size:13px;font-weight:bold'>
                <td>SF</td>
                <td>Formula: Total of all ratings</td>
                <td style='text-align:center'>$this->totalSupp</td>
              </tr>
              <tr>
                <td colspan='5' style='font-size:13px;'>
                  <b>Comments and Recommendation:</b>
                  " . $this->comments() . "
                  <br>
                  <br>
                </td>
              </tr>
              </tbody>
            </table>
            <table class='table-bordered borderColor' style='width:100%;background:#0080003d'>
              <tbody>
                <tr style='font-size:10px'>
                  <td style='width:18%'>Discussed Date:</td>
                  <td style='width:18%'>Assessed Date</td>
                  <td style='width:18%'></td>
                  <td style='width:18%'>Reviewed Date:</td>
                  <td style='width:18%'>Final Rating by:</td>
                  <td>Date:</td>
                </tr>
                <tr style='font-size:13px;text-align:center'>
                  <td><b>" . $this->empDetails($this->EmpId)['firstName'] . " " . $this->empDetails($this->EmpId)['lastName'] . "</b></td>
                  <td>
                      <p style='font-size:10px'>
                        I certified that I discussed my assessment of the performance with the employee
                      </p>
                      <b>" . $this->empDetails($this->fileStatus['ImmediateSup'])['firstName'] . " " . $this->empDetails($this->fileStatus['ImmediateSup'])['lastName'] . "</b>
                  </td>
                  <td>
                      <p style='font-size:10px'>
                        I certified that I discussed with the employee how they are rated:
                      </p>
                      <b>" . $this->empDetails($this->fileStatus['DepartmentHead'])['firstName'] . " " . $this->empDetails($this->fileStatus['DepartmentHead'])['lastName'] . "</b>

                  </td>
                  <td>
                  </td>
                  <td><b>" . $this->fileStatus['HeadAgency'] . "</b></td>
                  <td></td>
                </tr>
                <tr style='font-size:10px;text-align:center'>
                  <td>Ratee</td>
                  <td>Superior</td>
                  <td>Department Head</td>
                  <td>( all PMT will sign )</td>
                  <td>Head of Agency</td>
                  <td></td>
                </tr>
              </tbody>
            </table>
            ";
    return $view;
  }

  private function empDetails($DataId)
  {

    $sql = "SELECT * from `employees` left join `positiontitles` on `employees`.`position_id`=`positiontitles`.`position_id` where `employees_id`='$DataId'";
    $sql = parent::query($sql);
    $employee = $sql->fetch_assoc();
    return $employee;
    // $empDetails = $employee['firstName']." ".$employee['lastName'];
    // return $empDetails;

  }

  private function accountblePersons($perId)
  {
    $emp = $this->EmpId;
    $core = mysqli::query("SELECT * FROM `spms_corefunctions` where parent_id='$perId'");
    while ($coreId = $core->fetch_assoc()) {
      $indicators = mysqli::query("SELECT * FROM `spms_matrixindicators` where cf_ID='$coreId[cf_ID]'");
      while ($empId = $indicators->fetch_assoc()) {
        $emp .= "," . $empId['mi_incharge'];
      }
    }
    $emp  = explode(",", $emp);
    $emp = array_unique($emp);
    $view = "<br>";
    while (list($i, $val) = each($emp)) {
      $view .= $this->empDetails($val)['firstName'] . " " . $this->empDetails($val)['lastName'] . "<br><br>";
      // $view .=$val."<br>";
    }
    return $view;
  }

  private function comments()
  {
    $sql = "SELECT * from `spms_commentrec` where `emp_id`='$this->EmpId' and `period_id`='$this->period'";
    $sql = parent::query($sql);
    $sql = $sql->fetch_assoc();
    return $sql['comment'];
  }
}
