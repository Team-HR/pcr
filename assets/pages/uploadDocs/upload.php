<?php
session_start();
require_once "../../libs/config_class.php";
$dataId = explode("|",$_POST['dataId']) ;
$valid = $dataId[1]==$_SESSION['emp_id'];
if($valid){
  ?>
  <h4 class="ui horizontal divider header">
    <i class="upload icon"></i>
    Upload Form
  </h4>
  <form id="upload_form" enctype="multipart/form-data" method="post" name='upload_form' onsubmit="fileUpload()">
      <div class="ui container segment" style="background:#00000014">
      <div class="ui labeled left icon input fluid">
        <div class="ui basic label">
          Title
        </div>
        <input type="hidden" name='parentId' id='parentId' value="<?=$_POST['dataId']?>" readonly>
        <input type="text" name='fileTitle' id='fileTitle' required>
      </div>
      <br>
      <div for="" style="background:#002bff1c;width:100%;height:100px;position:relative">
        <div style="z-index:2;position:absolute;width:100%">
          <input type="file" name="file1" id="file1" style="border:1px solid green;width:95%;height:100px;top:18px;opacity:0;" required onchange="fileDisplayContent()">
        </div>
        <div style="top:0px;position:absolute;width:100%;padding:10px;z-index:1">
          <h5 class="ui center aligned icon header">
            <i class="circular upload icon"></i>
            <span id='fileNameSelectedFile'>Click or drag and drop a file</span>
          </h5>
        </div>
      </div>
      <br>
      <div class="ui labeled left icon input fluid">
        <div class="ui basic label">
          Description
        </div>
        <input type="text" name="file1_description" required>
      </div>
    </div>
    <h3 id="status"></h3>
    <p id="loaded_n_total"></p>
    <div class="ui progress" style="display:none" id='progressBar'>
      <div class="bar" id='fileBarUpload'>
        <div class="progress" id='fileTextUpload'>0%</div>
      </div>
      <div class="label">Uploading File</div>
    </div>
    <center>
      <input type="submit" name="uploadBtn" class="ui blue button" value="upload" disabled>
    </center>
  </form>
  <h2 class="ui center aligned icon header" id='doneUpload_Msg' style="display:none">
    <i class="ui green circular thumbs up outline icon"></i>
    Done!!
    <br>
    <br>
    <button type="button" name="button" class="ui positive button " onclick="uploadMore()">Upload Another File</button>
  </h2>
  <?php
}
?>
<h4 class="ui horizontal divider header">
  <i class="folder icon"></i>
  Uploaded Documents
</h4>
<div class="ui container segment">
  <div class="ui middle aligned divided list" id='listOfDocument'>
    <?php
    $sql = "SELECT * from spms_workdocumentation where employee_id='$dataId[1]' and mfoDataId='$dataId[0]'";
    $sql = $mysqli->query($sql);
    if($sql->num_rows>0){
    while($dat = $sql->fetch_assoc()){
      $cancelBtn = "";
      if($valid){
        $cancelBtn = "
        <div class='ui button red' onclick='removeDocumentationFile()'>Remove</div>
        ";
      }
      echo "
      <div class='item' data-id='$dat[workDocumentationID]' data-target='$_POST[dataId]'>
      <div class='right floated content'>
      <a href='assets/uploads/$dat[workDocumentationFile]' download>
      <div class='ui button blue'>Show</div>
      </a>
      $cancelBtn
      </div>
      <i class='file icon blue'></i>
      <div class='content'>
      <div class='header'>$dat[workDocumentationTitle]</div>
      $dat[workDocumentationDescription]
      </div>
      </div>
      ";
    }
  }else{
    echo "<center><h3 style='color:#00000052'>No Documentation Records Found</h3></center>";
  }
    ?>
  </div>
</div>
