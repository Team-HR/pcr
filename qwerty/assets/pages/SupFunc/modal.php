<?php
if (isset($_POST['updateData'])) {
  $dataId = $_POST['updateData'];
  $data = "SELECT * FROM `spms_supportfunctions` where `id_suppFunc`='$dataId'";
  $data = $_ipcr->get_data($data);

  function indicationInputs($type,$data){
    $count = 5;
    $view = "";
    $data = unserialize($data);
    while($count>=1){
      $view .= "
      <div class='form-group'>
      <div class='input-group'>
      <div class='input-group-prepend'>
      <span class='input-group-text'>$count =</span>
      </div>
      <textarea class='form-control' aria-label='With textarea' name='$type$count'>$data[$count]</textarea>
      </div>
      </div>
      ";
      $count--;
    }
    return $view;
  }
  ?>
    <h2 style="color:#00000038">Support Function Edit Form</h2>
    <hr>
    <div class="form-group">
      <input type="hidden" name="supEdit" value="<?=$data['id_suppFunc']?>">
      <label>MFO</label>
      <input type="text" class="form-control" name="mfo" placeholder="MFO" value="<?=$data['mfo']?>" required>
    </div>
    <div class="form-group">
      <label>Success Indicators</label>
      <input type="text" class="form-control" name="successIndicator" placeholder="Success Indicators" value="<?=$data['suc_in']?>" required>
    </div>
    <div class="form-group">
      <label>Percentage</label>
      <input type="number" class="form-control" name="percent" placeholder="Percentage" value="<?=$data['percent']?>" required>
    </div>
    <div class="form-group">
        <label>For:</label>
        <select class="form-control" name="pcrType" value="<?=$data['ipcr']?>" required>
          <option></option>
          <option value="1">IPCR</option>
          <option value="2">SPMS / DIVISION</option>
          <option value="3">DPCR</option>
        </select>
      </div>
    <div class="row">
      <div class="col-sm" style="padding:5px">
        <div style="border:1px solid #00000038;padding:5px;border-radius:10px">
          <h4 style="padding:10px">Quality</h4>
          <?=indicationInputs('quality',$data['Q'])?>
        </div>
      </div>
      <div class="col-sm" style="padding:5px">
        <div style="border:1px solid #00000038;padding:5px;border-radius:10px">
          <h4 style="padding:10px">Efficiency</h4>
          <?=indicationInputs('efficiency',$data['E'])?>
        </div>
      </div>
      <div class="col-sm" style="padding:5px">
        <div style="border:1px solid #00000038;padding:5px;border-radius:10px">
          <h4 style="padding:10px">Timeliness</h4>
          <?=indicationInputs('timeliness',$data['T'])?>
        </div>
      </div>
    </div>
    <br>
    <button class="btn btn-primary btn-lg form-control" name="submitBtn" type="submit"> Update </button>
    <?php
}


?>
