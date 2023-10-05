<?php
  if(isset($_POST['saveSup'])){
    $mfo = $_POST['mfo'];
    $successIndicator = $_POST['successIndicator'];
    $percent = $_POST['percent'];
    $pcrType = $_POST['pcrType'];
    $quality = serialize($_POST['quality']);
    $efficiency = serialize($_POST['efficiency']);
    $timeliness = serialize($_POST['timeliness']);
    $supEdit = $_POST['supEdit'];
  if($supEdit){
    $sql = "UPDATE `spms_supportfunctions` SET `mfo` = '$mfo', `suc_in` = '$successIndicator', `Q` = '$quality', `E` = '$efficiency', `T` = '$timeliness', `percent` = '$percent', `type` = '$pcrType' WHERE `spms_supportfunctions`.`id_suppFunc` = '$supEdit'";
  }else{
    $sql = "INSERT INTO `spms_supportfunctions` (`id_suppFunc`, `mfo`, `suc_in`, `Q`, `E`, `T`, `percent`,`type`)
    VALUES (NULL, '$mfo', '$successIndicator', '$quality', '$efficiency', '$timeliness', '$percent', '$pcrType')";
  }
    $sql = $mysqli->query($sql);
    if($sql){
      echo 1;
    }else{
      echo $mysqli->error;
    }
  }elseif (isset($_POST['deleteData'])) {
    if($_POST['deleteData']){
      $sql = "DELETE FROM `spms_supportfunctions` WHERE `spms_supportfunctions`.`id_suppFunc`='$_POST[deleteData]'";
      $sql = $mysqli->query($sql);
      if($sql){
        echo 1;
      }else{
        echo $mysqli->error;
      }
    }else{
      echo "Null";
    }
  }elseif(isset($_POST['getSupportFunction'])){
    $sql = "SELECT * from `spms_supportfunctions`";
    $tableRow = "";
    $tableRowIndi = "";
    $tableRowSupervisor = "";
    foreach($mysqli->get_data($sql) as $data){
      if($data['type']=='1'){
        $tableRowIndi.= "
        <tr>
          <td>$data[mfo]</td>
          <td>$data[suc_in]</td>
          <td>".rebuildSerializedData($data['Q'])."</td>
          <td>".rebuildSerializedData($data['E'])."</td>
          <td>".rebuildSerializedData($data['T'])."</td>
          <td>$data[percent]%</td>
          <td style='text-align:center;vertical-align: middle;'>
            <button type='button' class='btn btn-success update' data-bs-toggle='modal'  data-umbra-action='update' data-bs-target='#Supmodal' data-id='$data[id_suppFunc]'>Update</button>
            <br>
            <br>
            <button type='button' class='btn btn-danger delete' data-id='$data[id_suppFunc]' data-umbra-action='delete'>Delete</button>
          </td>
        </tr>
      ";
      }elseif($data['type']=='3'){
        $tableRow.= "
        <tr>
        <td>$data[mfo]</td>
        <td>$data[suc_in]</td>
        <td>".rebuildSerializedData($data['Q'])."</td>
        <td>".rebuildSerializedData($data['E'])."</td>
        <td>".rebuildSerializedData($data['T'])."</td>
        <td>$data[percent]%</td>
        <td style='text-align:center;vertical-align: middle;'>
        <button type='button' class='btn btn-success update' data-bs-toggle='modal' data-umbra-action='update' data-bs-target='#Supmodal' data-id='$data[id_suppFunc]'>Update</button>
        <br>
        <br>
        <button type='button' class='btn btn-danger delete' data-id='$data[id_suppFunc]' data-umbra-action='delete'>Delete</button>
        </td>
        </tr>
        ";
      }elseif($data['type']=='2') {
        $tableRowSupervisor.= "
                <tr>
                <td>$data[mfo]</td>
                <td>$data[suc_in]</td>
                <td>".rebuildSerializedData($data['Q'])."</td>
                <td>".rebuildSerializedData($data['E'])."</td>
                <td>".rebuildSerializedData($data['T'])."</td>
                <td>$data[percent]%</td>
                <td style='text-align:center;vertical-align: middle;'>
                <button type='button' class='btn btn-success update' data-bs-toggle='modal' data-umbra-action='update' data-bs-target='#Supmodal' data-id='$data[id_suppFunc]'>Update</button>
                <br>
                <br>
                <button type='button' class='btn btn-danger delete' data-id='$data[id_suppFunc]' data-umbra-action='delete'>Delete</button>
                </td>
                </tr>
        ";
      }
    }

    if($tableRow==""){
      $tableRow.= "
      <tr>
        <th colspan='7' style='color:#00000038;text-align:center'>
          <h1>
          No Data Found
          </h1>  
        </th>
      </tr>
      ";
    }
    if($tableRowIndi==""){
      $tableRowIndi= "
      <tr>
        <th colspan='7' style='color:#00000038;text-align:center'>
          <h1>
          No Data Found
          </h1>  
        </th>
      </tr>";
    }
    if($tableRowSupervisor==""){
      $tableRowSupervisor.= "
      <tr>
        <th colspan='7' style='color:#00000038;text-align:center'>
          <h1>
          No Data Found
          </h1>  
        </th>
      </tr> ";
    }
    ?>
    <h2 style="color:#00000038;padding:20px">Support Function Table Department Head</h2>
    <div class="table-responsive">
      <table class="table table-hover" id='suptable'>
        <thead>
          <tr>
            <th scope="col">MFO</th>
            <th scope="col">Success Indicators</th>
            <th scope="col">Quality</th>
            <th scope="col">Efficiency</th>
            <th scope="col">Timeliness</th>
            <th scope="col">Percentage</th>
            <th scope="col">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?=$tableRow?>
        </tbody>
      </table>
    </div>
    <br>
    <hr>
    <br>
    <h2 style="color:#00000038;padding:20px">Support Function Table Supervisory</h2>
    <div class="table-responsive">
      <table class="table table-hover" id='suptable'>
        <thead>
          <tr>
            <th scope="col">MFO</th>
            <th scope="col">Success Indicators</th>
            <th scope="col">Quality</th>
            <th scope="col">Efficiency</th>
            <th scope="col">Timeliness</th>
            <th scope="col">Percentage</th>
            <th scope="col">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?=$tableRowSupervisor?>
        </tbody>
      </table>
    </div>
    <br>
    <hr>
    <br>
    <h2 style="color:#00000038;padding:20px">Support Function Table Individual</h2>
    <div class="table-responsive">
      <table class="table table-hover" id='suptable'>
        <thead>
          <tr>
            <th scope="col">MFO</th>
            <th scope="col">Success Indicators</th>
            <th scope="col">Quality</th>
            <th scope="col">Efficiency</th>
            <th scope="col">Timeliness</th>
            <th scope="col">Percentage</th>
            <th scope="col">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?=$tableRowIndi?>
        </tbody>
      </table>
    </div>


    <?php
  
  
  }

  function rebuildSerializedData($data){
    $data = unserialize($data);
    $content = "";
    $a = 1;
    while ($a <=5) {
      if($data[$a]){
        $content .= $a." - ".$data[$a]."<br>";
      }
      $a++;
    }
    return $content;
  }
?>
