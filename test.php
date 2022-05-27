<?php
require_once "assets/libs/config_class.php";

function changeCount($dat){
  $dat = str_replace(")","",$dat);
  $dat = explode(".",$dat);
  $d = "";
  foreach ($dat as $a){
    $a = str_replace(' ', '', $a);
    if($a){
      if(is_numeric($a)){
        if($a<10&&strlen($a)==1){
          $d.="0".$a.".";
        }else{
          $d.=$a.".";
        }
      }else{
        $d.=$a.".";
      }
    }
  }
  return $d;
}

if(isset($_GET['config'])){
  if(isset($_GET['core'])){
    $sql = "SELECT * from spms_corefunctions";
    $sql = $mysqli->query($sql);
    $a = [];
    while($ar = $sql->fetch_assoc()){
      $a[] =  $ar;
    } 
    echo json_encode($a);
  }elseif($_GET['update']){
    $dat = $_GET['update'];
    $sql = "SELECT * from `spms_corefunctions` where `cf_ID`=$dat";
    $sql = $mysqli->query($sql);
    $sql = $sql->fetch_assoc();

    $from = $sql['cf_count'];
    $con = changeCount($from);
    $u = "UPDATE `spms_corefunctions` SET `cf_count` = '$con' WHERE `spms_corefunctions`.`cf_ID` = $dat";
    $u = $mysqli->query($u);
    $result = "SELECT * from `spms_corefunctions` where `cf_ID`=$dat";
    $result = $mysqli->query($result);
    $result = $result->fetch_assoc();
    echo $from." to ".$result['cf_count'];
  }
 


}else{
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <title></title>
</head>
<script>
  function changeCount(){
    let xml = new XMLHttpRequest();
        xml.onload = function(){
          upld(JSON.parse(xml.responseText),0);
        }
        xml.open("GET","?config&core",false);
        xml.send();
  }
  function upld(a,count){
    let xml = new XMLHttpRequest();
    xml.onload = function(){
      if(count<a.length){
        _("logs").innerHTML = _("logs").innerHTML+"<br>"+xml.responseText+" = <b>("+count+" of "+a.length+")</b>";
        count++;
        setTimeout(() => {
            upld(a,count);
          }, 100);
        }    
      }
    xml.open("GET","?config&update="+a[count]['cf_ID'],false);
    xml.send();
  }
  function _(el){
    return document.getElementById(el);
  }
</script>
<body >
  <button onclick="changeCount()">
    Start
  </button>
  <div id="logs">
    <h1>Logs</h1>
  </div>


</body>
</html>
  <?php  
  }
  ?>
