<?php
if (isset($_POST['searchDep'])) {
  $search = addslashes($_POST['searchDep']);
  $sql = "SELECT * from `department` where `department`.`department` like '%$search%' limit 10";
  $sql = $mysqli->query($sql);
  $listItem = "";
  if ($sql->num_rows) {
    while ($arr = $sql->fetch_assoc()) {
      // $checkIfAssigned = "SELECT * from `spms_departmentassignedtopmt` where `department_id`='$arr[department_id]'";
      // $checkIfAssigned = $mysqli->query($checkIfAssigned);
      // if(!$checkIfAssigned->num_rows){
      $listItem .= "<a class='list-group-item list-group-item-action searchResult' style='cursor:pointer;' data-id='$arr[department_id]'>$arr[department]</a>";
      // }
    }
  } else {
    $listItem .= "<li class='list-group-item'>No result found</li>";
  }
?>
  <ul class="list-group" style='position:absolute;width:100%;margin-top:-15px;z-index:10; box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);'>
    <?= $listItem ?>
  </ul>
  <?php
} elseif (isset($_POST['assignDepartmentToPMT'])) {
  $sql = "INSERT INTO `spms_departmentassignedtopmt` 
          (`departmentAssignedToPMT_id`, `employees_id`, `department_id`) 
          VALUES (NULL, '$_POST[assignDepartmentToPMT]', '$_POST[depToAssign]')";
  $sql = $mysqli->query($sql);
  if ($sql) {
    echo 1;
  } else {
    echo $mysqli->error;
  }
} elseif (isset($_POST['refreshCard'])) {
  $empId = $_POST['refreshCard'];
  $sql = "SELECT  * from `spms_departmentassignedtopmt` left join `department` on `spms_departmentassignedtopmt`.`department_id`=`department`.`department_id` where `employees_id`='$empId'";
  $sql = $mysqli->query($sql);
  // die($mysqli->error);
  while ($a = $sql->fetch_assoc()) {
    echo "
      <li class='list-group-item d-flex justify-content-between align-items-center'>
        $a[department]
        <button type='button' class='close' aria-label='Close' data-id='$a[departmentAssignedToPMT_id]'><span aria-hidden='true'>×</span></button>
      </li>
    ";
  }
} elseif (isset($_POST['deleteAssignDep'])) {
  $dataId = $_POST['deleteAssignDep'];
  $sql = "DELETE FROM `spms_departmentassignedtopmt` WHERE `spms_departmentassignedtopmt`.`departmentAssignedToPMT_id` = '$dataId'";
  $sql = $mysqli->query($sql);
  echo $mysqli->error;
} elseif (isset($_POST['unassignDep'])) {
  $sql = "SELECT * from `department`";
  $sql = $mysqli->query($sql);
  echo $mysqli->error;
  $tr = "";
  while ($a = $sql->fetch_assoc()) {
    $check = "SELECT * from `spms_departmentassignedtopmt` where `department_id`='$a[department_id]'";
    $check = $mysqli->query($check);
    if (!$check->num_rows) {
      $tr .= "
      <tr>
        <td>$a[department]</td>
      </tr>
    ";
    }
  }
  if ($tr) {
  ?>
    <table class="table">
      <thead>
        <tr>
          <th>Unassigned Departments</th>
        </tr>
      </thead>
      <tbody>
        <?= $tr ?>
      </tbody>
    </table>
<?php
  }
}
?>