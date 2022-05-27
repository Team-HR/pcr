<?php

if(isset($_POST['page'])){
  $page = $_POST['page'];
  if($page=="RatingScale"){
    $user->set_period($_SESSION['iMatrix_period']);
    if($user->core_countTotal>0){
      echo $user->RatingScaleTable();
    }else{
      echo "
      <br>
      <br>
      <br>
      <h2 class='ui center aligned icon header'>
      <i class='ui red exclamation triangle icon'></i>
      <div class='content'>
      Rating Scale Matrix Not Found
      <div class='sub header'>You Dont have Rating Matrix Yet. Please consult your Department Head For this Matter</div>
      </div>
      </h2>
      ";
    }
  }
}elseif(isset($_POST['period_check'])){
  $month = $_POST['period_check'];
  $year = $_POST['year'];
  $sql = "SELECT * from spms_mfo_period where month_mfo='$month' and year_mfo='$year'";
  $sql = $mysqli->query($sql);
  if(!$sql){
    die($mysqli->error);
  }else{
    $sql = $sql->fetch_assoc();
    $_SESSION['iMatrix_period'] = $sql['mfoperiod_id'];
    print(1);
  }

}



?>
