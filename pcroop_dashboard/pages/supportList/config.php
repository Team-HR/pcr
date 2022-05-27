<?php
if(isset($_POST['removeSupportData'])){
    $dataId = $_POST['removeSupportData'];
    $sql = "DELETE FROM `supportfunctions` WHERE `supportfunctions`.`id_suppFunc` = '$dataId'";
    $sql = $mysqli->query($sql);
    if (!$sql){
        die($mysqli->error);
    }else{
        echo 1;
    }
}else if(isset($_POST['editSupport'])){
    $editSupport = $_POST["editSupport"];
    $mfoInput = $_POST["mfoInput"];
    $succInput = $_POST["succInput"];
    $percentInput = $_POST["percentInput"];
    $Quality = serialize(explode(",",$_POST["Quality"]));
    $Efficiency = serialize(explode(",",$_POST["Efficiency"]));
    $Timeliness = serialize(explode(",",$_POST["Timeliness"]));
    $sql = "UPDATE `supportfunctions` SET
    `mfo` = '$mfoInput',
    `suc_in` = '$succInput',
    `Q` = '$Quality',
    `E` = '$Efficiency',
    `T` = '$Timeliness',
    `percent` = '$percentInput',
    `owner` = ''
    WHERE `supportfunctions`.`id_suppFunc` = '$editSupport'";
    $sql = $mysqli->query($sql);
    if(!$sql){
      echo $mysqli->error;
    }else{
      echo 1;
    }
}
?>
