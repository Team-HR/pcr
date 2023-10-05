<?php
if(isset($_POST['rsmGetTableData'])){
    $period = $_POST['period'];
    $year = $_POST['year'];

    $sql = "SELECT * from `spms_mfo_period` where `month_mfo`='$period' and `year_mfo`='$year'";
    $sql = $mysqli->query($sql);
    // 
    $mfoperiod = $sql->fetch_assoc();
    // 
    $sql = "SELECT * from department";
    $sql = $mysqli->query($sql);

    $tr = "";
    while ($dep = $sql->fetch_assoc()){
        $rsm = "SELECT * from `spms_rsmstatus` where `period_id`='$mfoperiod[mfoperiod_id]' and `department_id`='$dep[department_id]'";
        $rsm =  $mysqli->query($rsm);
        if($rsm->num_rows){
            $rsm = $rsm->fetch_assoc();
            $cBox = "";
            if($rsm['edit']){
                $cBox = 'checked';
            }
            $tr .="<tr>
                        <td><a href='?viewRsm&period=$mfoperiod[mfoperiod_id]&department=$dep[department_id]' target='_blank'>$dep[department]</a></td>
                        <td>$rsm[done]</td>
                        <td style='text-align: center'>
                            <input type='checkbox' class='form-check-input' name='editdatRsm' $cBox data-element='$rsm[rsmStatus_id]'>
                        </td>
                   </tr>
            ";
        }else{
            $tr .="<tr>
                        <td><a href='?viewRsm&period=$mfoperiod[mfoperiod_id]&department=$dep[department_id]' target='_blank'>$dep[department]</a></td>
                        <td>0</td>
                        <td style='text-align: center'>
                            <input type='checkbox' class='form-check-input' name='nodatSaveRsm' data-element='$mfoperiod[mfoperiod_id]||$dep[department_id]'>
                        </td>
                   </tr>
            ";
        }
    }
    echo $tr;
}elseif(isset($_POST['rsmAdd'])){
    $view = "";
    if(isset($_POST['edit'])){
        $dataID =$_POST['dat'];
        $edit = $_POST['edit'];
            $sql = "UPDATE `spms_rsmstatus` SET `edit` = '$edit' WHERE `spms_rsmstatus`.`rsmStatus_id` = '$dataID'";
            $sql = $mysqli->query($sql);
            if(!$sql){
                $view = $mysqli->error;
            }else{
                $getRsm = "SELECT * FROM `spms_rsmstatus`  left join `department` on `spms_rsmstatus`.`department_id`=`department`.`department_id` where `rsmStatus_id`='$dataID'";
                $getRsm = $mysqli->query($getRsm);
                $getRsm = $getRsm->fetch_assoc();
                $check = '';
                if($getRsm['edit']){
                    $check = 'checked';
                }
                $view = "
                <td><a href='?viewRsm&period=$getRsm[period_id]&department=$getRsm[department_id]' target='_blank'>$getRsm[department]</a></td>
                <td>$getRsm[done]</td>
                <td style='text-align: center'>
                <input type='checkbox' class='form-check-input' name='editdatRsm' $check data-element='$getRsm[rsmStatus_id]'>
                </td>
                ";
            }
        echo $view; 
    }else{
        $period = $_POST['period'];
        $department = $_POST['department'];
        $check = "SELECT * from `spms_rsmstatus` where `period_id`='$period' and `department_id`='$department'";
        $check = $mysqli->query($check);
        if($check->num_rows){
            $check = $check->fetch_assoc();
            $sql = "UPDATE `spms_rsmstatus` SET `edit` = '1' WHERE `spms_rsmstatus`.`rsmStatus_id` = '$check[rsmStatus_id]'";
            $sql = $mysqli->query($sql);
            $dataId = $check['rsmStatus_id'];
        }else{
            $sql = "INSERT INTO `spms_rsmstatus` (`rsmStatus_id`, `period_id`, `department_id`, `done`, `edit`, `alter_logs`) VALUES (NULL, '$period', '$department', '0', '1', '')";
            $sql = $mysqli->query($sql);
            $dataId = $mysqli->insert_id;
        }

        $getInputedDat = "SELECT * from `spms_rsmstatus` left join `department` on `spms_rsmstatus`.`department_id`=`department`.`department_id` where `rsmStatus_id`='$dataId'";
        $getInputedDat = $mysqli->query($getInputedDat);
        $getInputedDat = $getInputedDat->fetch_assoc();
        $view = "
            <td><a href='?viewRsm&period=$period&department=$getInputedDat[department_id]' target='_blank'>$getInputedDat[department]</a></td>
            <td>$getInputedDat[done]</td>
            <td style='text-align: center'>
                <input type='checkbox' class='form-check-input' name='editdatRsm' checked data-element='$getInputedDat[rsmStatus_id]'>
            </td>
        ";
        echo $view;

    }



}elseif (isset($_POST['enableAllRsm'])){
    $year = $_POST['year'];
    $period = $_POST['period'];
    $periodId = "SELECT * from `spms_mfo_period` where `month_mfo`='$period' and `year_mfo`='$year'";
    $periodId = $mysqli->query($periodId);
    $periodId = $periodId->fetch_assoc();
    $allDepartment  = "SELECT * from `department`";
    $allDepartment = $mysqli->query($allDepartment);
    $w = [];
    $wo = [];
    while($dep = $allDepartment->fetch_assoc()){
        $check = "SELECT * from `spms_rsmstatus` where `period_id`='$periodId[mfoperiod_id]' and `department_id`='$dep[department_id]'";
        $check = $mysqli->query($check);
        if($check->num_rows){
            $getW = $check->fetch_assoc();
            $w[] = [$getW['rsmStatus_id'],$dep['department']];
        }else{
            $wo[] = [$periodId['mfoperiod_id'],$dep['department_id'],$dep['department']];
        }
    }
    $a = array('with'=>$w,'without'=>$wo);
    echo json_encode($a);
}elseif(isset($_POST['editAllWith'])){
     $dataId = $_POST['dataid'];
     $edit = $_POST['edit'];
     $sql = "UPDATE `spms_rsmstatus` SET `edit` = '$edit' WHERE `spms_rsmstatus`.`rsmStatus_id` = '$dataId'";
     $mysqli->query($sql);
     if($mysqli->error){
        echo $mysqli->error;
     }else{
        echo 1;
     }
}elseif(isset($_POST['editAllWithout'])){
    $period = $_POST['period'];
    $department = $_POST['department'];
    $edit = $_POST['edit'];
    $sql = "INSERT INTO `spms_rsmstatus` (`rsmStatus_id`, `period_id`, `department_id`, `done`, `edit`, `alter_logs`) VALUES (NULL, '$period', '$department', '0', '$edit', '')";
    $mysqli->query($sql);
    if($mysqli->error){
       echo $mysqli->error;
    }else{
       echo 1;
    }
}
?>