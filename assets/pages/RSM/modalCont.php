<?php
if (isset($_POST['ModalSiAddCont'])) {
  $dataId = $_POST['ModalSiAddCont'];
  echo "
  <script>
    $(document).ready(function(){
      $('.ui.checkbox').checkbox();
      $('.ui.dropdown').dropdown({
				fullTextSearch:true
      });
    });
  </script>
      <div class='ui form'>
      <h2 class='ui horizontal divider'>
        <i class='blue id card outline icon'></i>
        Success Indicators
        <i class='blue id card outline icon'></i>
      </h2>
        <div class='field'>
          <label>Success Indicator</label>
          <textarea id='succIn$dataId'></textarea>
        </div>
        <div class='fields'>
          " . RatingCheckBox('Quality', $dataId) . RatingCheckBox('Efficiency', $dataId) . RatingCheckBox('Timeliness', $dataId) . "
        </div>
        <h2 class='ui horizontal divider'>
        <i class='blue chart bar icon'></i>
        Rating Matrix
        <i class='blue chart bar icon'></i>
        </h2>
        " . ratingInputs('Quality', $dataId, '', '', '', '', '') . ratingInputs('Efficiency', $dataId, '', '', '', '', '') . ratingInputs('Timeliness', $dataId, '', '', '', '', '') . "
        <label><b>Incharge</b></label>
        <div class='ui fluid multiple search selection dropdown'>
          <input type='hidden' name='emp' id='incharge$dataId'>
          <i class='dropdown icon'></i>
          <div class='default text'>Select Employees</div>
          <div class='menu'>
          " . employFunc($mysqli) . "
          </div>
        </div>
        <br>
          <button class='mini ui positive fluid button' onclick='SaveMfoSI(this,$dataId)'><i class='Save icon'></i> Save</button>
      </div>
  ";
} elseif (isset($_POST['siEditnModalCont'])) {
  $dataId = $_POST['siEditnModalCont'];
  $sql = "SELECT * from spms_matrixindicators where mi_id ='$_POST[siEditnModalCont]'";
  $sql = $mysqli->query($sql);
  $sql = $sql->fetch_assoc();
  $dataId = $sql['mi_id'];
  $mrq = mr($sql, 'mi_quality');
  $mre = mr($sql, 'mi_eff');
  $mrt = mr($sql, 'mi_time');
  $correction = "";
  if ($sql['corrections']) {
    $c = unserialize($sql['corrections']);
    $count = 0;
    $view = "";
    while ($count < count($c)) {
      $state = "<b style='color:red'>Unaccomplished</b>";
      if ($c[$count][1]) {
        $state = "<b style='color:green'>Accomplished</b>";
      }
      $view .= "
            <tr>
                <td>" . $c[$count][0] . "</td>
                <td>$state</td>
            </tr>
            ";
      $count++;
    }
    $correction = "
    <h2 class='ui horizontal divider'>
    <i class='blue id card outline icon'></i>
    Corrections
    <i class='blue id card outline icon'></i>
    </h2>
    <center><table class='ui celled table'>
      <thead>
        <tr><th>Corrections</th>
        <th>Status</th>
      </tr></thead>
      <tbody>
      $view
      </tbody>
    </table>
    </center>
    ";
  }


  echo "
  <script>
    $(document).ready(function(){
      $('.ui.checkbox').checkbox();
      $('.ui.dropdown').dropdown({
				fullTextSearch:true
      });
    });
  </script>
      <div class='ui form'>
      $correction
      <h2 class='ui horizontal divider'>
        <i class='blue id card outline icon'></i>
        Success Indicators
        <i class='blue id card outline icon'></i>
      </h2>
        <div class='field'>
          <label>Success Indicator</label>
          <textarea id='succIn$dataId'>$sql[mi_succIn]
          </textarea>
        </div>
        <div class='fields'>
          " . RatingCheckBox('Quality', $dataId) . RatingCheckBox('Efficiency', $dataId) . RatingCheckBox('Timeliness', $dataId) . "
        </div>
        <h2 class='ui horizontal divider'>
        <i class='blue chart bar icon'></i>
        Rating Matrix
        <i class='blue chart bar icon'></i>
        </h2>
        " . ratingInputs('Quality', $dataId, $mrq[1], $mrq[2], $mrq[3], $mrq[4], $mrq[5])
    . ratingInputs('Efficiency', $dataId, $mre[1], $mre[2], $mre[3], $mre[4], $mre[5])
    . ratingInputs('Timeliness', $dataId, $mrt[1], $mrt[2], $mrt[3], $mrt[4], $mrt[5]) . "
        <label><b>Incharge</b></label>
        <div class='ui fluid multiple search selection dropdown'>
          <input type='hidden' name='emp' id='incharge$dataId' value='$sql[mi_incharge]'>
          <i class='dropdown icon'></i>
          <div class='default text'>Select Employees</div>
          <div class='menu'>
          " . employFunc($mysqli) . "
          </div>
        </div>
        <br>
          <button class='mini ui positive fluid button' onclick='SaveMfoSIEdit(this,$dataId)'><i class='Save icon'></i> Save Changes</button>
      </div>
  ";
} elseif (isset($_POST['ShowIPcrModalPost'])) {
  $emp = $_POST['ShowIPcrModalPost'];
  $period = $_SESSION['period'];
  $employee = new Employee_data();
  $employee->set_emp($emp);
  $employee->set_period($period);
  // $status = $employee->fileStatus;
  // $employee->hideNextBtn();
  $irm = new IRM();
  $irm->set_cardi($emp, $period, $employee->get_emp('department_id'));
  echo "<table class='ui celled table'>
      <thead>
          <tr>
              <th colspan='5' style='padding:20px;text-align:center'>
              Indivudual Rating Scale
              <br>
              " . $employee->get_emp('firstName') . " " . $employee->get_emp('lastName') . " " . $employee->get_emp('extName') . "
              " . $employee->get_period('month_mfo') . " " . $employee->get_period('year_mfo') . "<br><br><br></h2>
              </th>
          </tr>
          <tr>
              <th rowspan='2' style='padding:20px'>MFO/ PAP</th>
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


  // if($status['approved']==""||$status==null){
  //   echo $employee->RatingScaleTable();
  // }else{
  //   echo "<center>";
  //   echo "<h2 class='noprint'>Showing the Record of ".$employee->get_emp('firstName')." ".$employee->get_emp('lastName')." ".$employee->get_emp('extName') ;
  //   echo "<br>as of";
  //   echo "<br>".$employee->get_period('month_mfo')." ".$employee->get_period('year_mfo')."<br><br><br></h2>";
  //   echo "</center>";
  //   $table_browse = new table($employee->hideCol);
  //   $table_browse->formType($employee->get_status('formType'));
  //   $table_browse->set_head($employee->tableHeader());
  //   $table_browse->set_body($employee->get_strategicView());
  //   $table_browse->set_body($employee->get_coreView());
  //   $table_browse->set_body($employee->get_supportView());
  //   $table_browse->set_foot($employee->tableFooter());
  //   echo $table_browse->_get();
  // }
} elseif (false) {
  // code...
} else {
  echo notFound();
}
function ratingInputs($type, $dataId, $input1, $input2, $input3, $input4, $input5)
{
  $view = "
    <span  id='container" . $type . $dataId . "' style='display:none'>
          <div class='ui piled segment'>
            <div class='field'>
            <label>$type</label>
              " . inputs('5', $type, $dataId, $input5) . inputs('4', $type, $dataId, $input4) . inputs('3', $type, $dataId, $input3) . inputs('2', $type, $dataId, $input2) . inputs('1', $type, $dataId, $input1) . "
            </div>
          </div>
          <div id='suggestions" . $type . $dataId . "'></div>
          <br>
    </span>
  ";
  return $view;
}
function mr($sql, $a)
{
  $arr = unserialize($sql[$a]);
  return $arr;
}
function inputs($count, $type, $dataId, $inputVal)
{
  $inputVal = htmlentities($inputVal);
  $view = '
  <div class="mini ui labeled input">
    <div class="ui label basic">
      ' . $count . ' =
    </div>
    <input type="text" placeholder="" value="' . $inputVal . '" id="input' . $count . $type . $dataId . '" onkeyup="findSuggestion(' . $type . ', ' . $dataId . ')">
  </div>
  ';
  return $view;
}
function RatingCheckBox($type, $dataId)
{
  $view = "
        <div class='inline field' style='padding:10px;width:25%;margin-left:100px'>
          <div class='ui toggle checkbox' >
            <input type='checkbox' tabindex='0' class='hidden' id='Check$type$dataId' onchange='checkbox(this,\"$type\",$dataId)'>
            <label style='font-size:20px'>$type</label>
          </div>
        </div>";
  return $view;
}
function employFunc($mysqli)
{
  $view = "";
  $dep_id = $_SESSION['emp_info']['department_id'];
  //$sql = "SELECT * from employees where department_id='$dep_id'";
  $sql = "SELECT * from `employees`";
  $sql = $mysqli->query($sql);
  while ($row = $sql->fetch_assoc()) {
    $view .= "<div class='item' data-value='$row[employees_id]'>$row[firstName] $row[lastName]</div>";
  }
  return $view;
}
