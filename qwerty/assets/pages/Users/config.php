<?php
if (isset($_POST['searchEmp'])) {
  $data = $_POST['searchEmp'];
  $data = explode(" ", $data);
  if (count($data) > 1) {
    $first = $data[0];
    $second = $data[1];
    $query = "where (`firstName` like '$first%' and `lastName` like '$second%') or (`firstName` like '$second%' and `lastName` like '$first%')";
  } else {
    $first = $data[0];
    $query = "where `firstName` like '$first%' or `lastName` like '$first%'";
  }

  $sql = "SELECT * from `employees` $query limit 5";
  $sql = $mysqli->query($sql);
  $row = "";
  if ($sql->num_rows >= 1) {
    while ($a = $sql->fetch_assoc()) {
      $row .= "<tr><td class='searchResult' data-id='$a[employees_id]'>$a[firstName] $a[lastName]</td></tr>";
    }
  } else {
    $row = "<tr class='disable'><td>No Record Found</td></tr>";
  }
?>
  <table class="table table-hover" style="position:absolute;background:white;border:1px solid #0000004d;box-shadow:5px 1px 5px #0000004d;z-index:10">
    <?= $row ?>
  </table>
<?php
} elseif (isset($_POST['showFormDetails'])) {
  $sql = "SELECT * from `employees` left join `spms_accounts` on `employees`.`employees_id`=`spms_accounts`.`employees_id`  where `employees`.`employees_id`='$_POST[showFormDetails]'";
  $info = $_ipcr->get_data($sql);
  $permit = explode(",", $info['type']);
  $pmt = "";
  $review = "";
  $matrix = "";
  $count = 0;
  //  Matrix,PMT,Reviewer
  while ($count < count($permit)) {
    if ($permit[$count] == 'PMT') {
      $pmt = 'checked';
    } elseif ($permit[$count] == 'Matrix') {
      $matrix = 'checked';
    } elseif ($permit[$count] == 'Reviewer') {
      $review = 'checked';
    }
    $count++;
  }

  $resetBtn = "";
  if ($info['password']) {
    $resetBtn = "  <div class='form-group'><button type='button' class='btn btn-info form-control' name='resetPass' data-id='$_POST[showFormDetails]'>Reset Password</button></div>";
  }

?>
  <h1>Account Information</h1>
  <div class="form-row">
    <div class="col-md-4 mb-3">
      <label>Last name</label>
      <input type="text" class="form-control" value="<?= $info['lastName'] ?>" disabled>
    </div>
    <div class="col-md-4 mb-3">
      <label>First name</label>
      <input type="text" class="form-control" value="<?= $info['firstName'] ?>" disabled>
    </div>
    <div class="col-md-4 mb-3">
      <label>MI</label>
      <input type="text" class="form-control" value="<?= $info['middleName'] ?>" disabled>
    </div>
  </div>
  <div class="form-row">
    <div class="col-md-4 mb-3">
      <label>Gender</label>
      <input type="text" class="form-control" value="<?= $info['gender'] ?>" disabled>
    </div>
    <div class="col-md-4 mb-3">
      <label>Employment Status</label>
      <input type="text" class="form-control" value="<?= $info['employmentStatus'] ?>" disabled>
    </div>
    <div class="col-md-4 mb-3">
      <label>Nature of Assignment</label>
      <input type="text" class="form-control" value="<?= $info['natureOfAssignment'] ?>" disabled>
    </div>
  </div>
  <input type="hidden" name='employeeId' value="<?= $_POST['showFormDetails'] ?>">
  <div class="form-group">
    <label>Username</label>
    <input class="form-control" name="username" aria-describedby="emailHelp" value="<?= $info['username'] ?>" required>
  </div>
  <div class="form-group">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" value="Matrix" name="matrix" <?= $matrix ?>>
      <label class="form-check-label" for="defaultCheck1">Administrative Officer-in-charge if RMS Encodinf per department</label>
    </div>
    <div class="form-check">
      <input class="form-check-input" type="checkbox" value="PMT" name="pmt" <?= $pmt ?>>
      <label class="form-check-label">PMT</label>
    </div>
    <div class="form-check">
      <input class="form-check-input" type="checkbox" value="Reviewer" name="reviewer" <?= $review ?>>
      <label class="form-check-label">Supervisor / Department Head</label>
    </div>
  </div>
  <div class="form-group">
    <button type="submit" class="btn btn-success form-control">Update</button>
  </div>
  <?= $resetBtn ?>

<?php
} elseif (isset($_POST['updateAccount'])) {
  $dataId = $_POST['updateAccount'];
  $username = $_POST['username'];
  $password = password_hash("1234", PASSWORD_DEFAULT);
  $type = $_POST['type'];
  $find = "SELECT * from `spms_accounts` where `employees_id` = '$_POST[updateAccount]'";
  $find = $mysqli->query($find);
  if ($find->num_rows) {
    $sql = "UPDATE `spms_accounts` SET `username` = '$username', `type` = '$type' WHERE `spms_accounts`.`employees_id` = '$dataId'";
  } else {
    $sql = "INSERT INTO `spms_accounts` (`acc_id`, `employees_id`, `username`, `password`, `type`)
            VALUES (NULL, '$dataId', '$username', '$password', '$type')";
  }
  $sql = $mysqli->query($sql);
  if ($sql) {
    echo 1;
  } else {
    echo $mysqli->error;
  }
} elseif (isset($_POST['resetPassword'])) {
  $dataId = $_POST['resetPassword'];
  $password = password_hash("1234", PASSWORD_DEFAULT);
  $sql = "UPDATE `spms_accounts` set `password`='$password' where `employees_id`='$dataId'";
  $sql = $mysqli->query($sql);
  if ($sql) {
    echo "Password was reset to Defualt";
  } else {
    echo $mysqli->error;
  }
} elseif (isset($_POST['searchSelectedDep'])) {
  $data = $_POST['searchSelectedDep'];
  if ($data) {
    $allPmt = "SELECT `employees`.`employees_id`, `employees`.`lastName`,`employees`.`firstName`,`employees`.`middleName`, `spms_accounts`.`type` from `employees` left join `spms_accounts` on `employees`.`employees_id`=`spms_accounts`.`employees_id` where `employees`.`department_id`='$data'";
  } else {
    $allPmt = "SELECT `employees`.`employees_id`, `employees`.`lastName`,`employees`.`firstName`,`employees`.`middleName`, `spms_accounts`.`type` from `employees` left join `spms_accounts` on `employees`.`employees_id`=`spms_accounts`.`employees_id` where `spms_accounts`.`type` like '%PMT%'";
  }
  $allPmt = $mysqli->query($allPmt);
  $view = "";
  while ($rowData = $allPmt->fetch_assoc()) {
    $view .= "
    <tr>
    <td>$rowData[lastName]</td>
    <td>$rowData[firstName]</td>
    <td>$rowData[middleName]</td>
    <td>$rowData[type]</td>
    <td>
      <a name='button' href='#gotoUserForm' class='btn btn-primary btnPmtResult' data-family='showDetails' data-id='$rowData[employees_id]'>Details</a>
    </td>
    </tr>
    ";
  }
  echo $view;
}
?>