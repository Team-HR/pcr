<?php
  if(isset($_POST['showModal'])){
?>
  <form class="ui form" name='reassignForm'>
    <div class="field">
      <label>Report</label>
      <textarea rows="2" name="comment" required></textarea>
    </div>
    <button class="ui fluid button blue" name="submitReport">Submit Report</button>
  </form>
<script>
  (function(){
      'use strict';
      document.forms.reassignForm.addEventListener('submit',function(){
        event.preventDefault();
        var btn = this.submitReport;
        btn.disabled=true;
        btn.innerHTML='proccessing...';
        var xml = new XMLHttpRequest();
        var fd = new FormData();
        fd.append("storeData",true);
        fd.append("comment",this.comment.value);
        fd.append("dataId",<?=$_POST['dataId']?>);
        xml.onload = function(){
          if(this.status==200||this.readState==4){
            console.log(this.responseText.length);
            if(this.responseText.length==0){
              $("#allModal").modal('hide');
              showPr("coreFunction","core");
            }else{
              btn.disabled= false;
              btn.innerHTML='Submit Report';
              alert("Something went wrong!ERROR: "+this.responseText);
            }
          }
        } 
        xml.open('post','?config=reassign',false);
        xml.send(fd);
      });
  })();
</script>
<?php
  }else if(isset($_POST['storeData'])){
    $dataId = $_POST['dataId'];
    $userId = $user->get_emp('employees_id');
    $period = $_SESSION['period_pr'];
    $comment = addslashes($_POST['comment']);
    $sql = "INSERT INTO `spms_corefucndata` (`cfd_id`, `type`, `p_id`, `empId`, `actualAcc`, `Q`, `E`, `T`, `remarks`,`disable`)
		VALUES (NULL, '', '$dataId', '$userId', '', '0', '0', '0', '$comment','1');";
    $sql = $mysqli->query($sql);
    if(!$sql){
      echo $mysqli->error;
    }
  } 
?>