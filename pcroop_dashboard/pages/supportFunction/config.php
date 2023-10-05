<?php

  if(isset($_POST['supportFuntionAdd'])){
    function ser($data){
      $a = explode(',',$data);
      $a = serialize($a);
      return $a;
    }
    $mfo = $_POST['mfo'];
    $successIndicator = $_POST['successIndicator'];
    $percent = $_POST['percent'];
    $paraKay = $_POST['paraKay'];
    $arrQuality = ser($_POST['arrQuality']);
    $arrEfficiency = ser($_POST['arrEfficiency']);
    $arrTimeliness = ser($_POST['arrTimeliness']);
    $sql = "INSERT INTO `supportfunctions` (`id_suppFunc`, `mfo`, `suc_in`, `Q`, `E`, `T`, `percent`, `owner`)
            VALUES ('', '$mfo', '$successIndicator', '$arrQuality', '$arrEfficiency', '$arrTimeliness', '$percent', '$paraKay');";
    $sql = $mysqli->query($sql);
    if (!$sql) {
      echo $mysqli->error;
    }else{
      echo 1;
    }
  }
 ?>
