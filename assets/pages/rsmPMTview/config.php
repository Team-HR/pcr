<?php
    if(isset($_POST['getIRM'])){
        $getIRM =explode('||',$_POST['getIRM']);
        $irm = new IRM($host,$usernameDb,$password,$database);
        $irm->set_cardi($getIRM[0],$getIRM[1],$getIRM[2]);
        echo "<table class='ui celled table'>
            <thead>
                <tr>
                    <th rowspan='2' style='padding:20px'>MFO / PAP</th>
                    <th rowspan='2'>Success Indicator</th>
                    <th colspan='3' style='width:40px'>Rating Matrix</th>
                </tr>
                <tr style='font-size:12px'>
                    <th>Q</th>
                    <th>E</th>
                    <th>T</th>
                </tr>
            </thead>
            <tbody>
                ".$irm->get_view()."
            </tbody>
        </table>";

    }elseif (isset($_POST['siCorrection'])||isset($_POST['mfoCorrection'])) {
        $correction = nl2br($_POST['correction']);
        if(isset($_POST['mfoCorrection'])){
            $datID = $_POST['mfoCorrection'];
            $getMFO = "SELECT * FROM `spms_corefunctions` where `cf_ID`='$datID'";
        }elseif(isset($_POST['siCorrection'])){
            $datID = $_POST['siCorrection'];
            $getMFO = "SELECT * FROM `spms_matrixindicators` where `mi_id`='$datID'";
        }
        $a = [$correction,0];
        $getMFO = $mysqli->query($getMFO);
        $getMFO = $getMFO->fetch_assoc();
        $c = [];
        if($getMFO['corrections']){
            $c = unserialize($getMFO['corrections']);
        }
        $c[] = $a;
        $c = $mysqli->real_escape_string(serialize($c));
        $sql = "UPDATE `spms_matrixindicators` SET `corrections` = '$c' WHERE `spms_matrixindicators`.`mi_id` = $datID";
        if(isset($_POST['mfoCorrection'])){
            $sql = "UPDATE `spms_corefunctions` SET `corrections` = '$c' WHERE `spms_corefunctions`.`cf_ID` = $datID";
        }
        $sql = $mysqli->query($sql);
        if(!$sql){
            echo "error";
        }else{
            echo 1;
        }
    }else if(isset($_POST['showCorrections'])||isset($_POST['showCorrectionsMFO'])){
        $mfo = 0;
        if(isset($_POST['showCorrections'])){
            $i = $_POST['showCorrections'];
            $sql = "SELECT * FROM `spms_matrixindicators` WHERE `spms_matrixindicators`.`mi_id` ='$_POST[showCorrections]'";
        }else {
            $sql = "SELECT * FROM `spms_corefunctions` WHERE `spms_corefunctions`.`cf_ID` ='$_POST[showCorrectionsMFO]'";
            $i = $_POST['showCorrectionsMFO'];
            $mfo = 1;
        }
        $sql = $mysqli->query($sql);
        $view = "";
        $c = $sql->fetch_assoc();
        $c = unserialize($c['corrections']);
        $count = 0;
        while($count<count($c)){
            $state = "<b style='color:red'>Unaccomplished</b>";            
            $removeBTN = "
                <button class='ui icon red button' data-target='removeCorrection' data-id='$i||$count||$mfo'>
                    Remove
                </button>
            ";
            if($c[$count][1]){
                $state = "<b style='color:green'>Accomplished</b>";            
                $removeBTN = "";
            }
            $view .= "
                <tr>
                    <td>".$c[$count][0]."</td>
                    <td>$state</td>
                    <td style='text-align:center'>$removeBTN</td>
                </tr>
                ";
            $count++;
        }

        echo "<center><table class='ui celled table'>
                <thead>
                  <tr><th>Corrections</th>
                  <th>Status</th>
                  <th></th>
                </tr></thead>
                <tbody>
                $view
                </tbody>
              </table>
            </center>
              ";


    }elseif(isset($_POST['removeCorrection'])){
        $arIndex = explode('||',$_POST['arIndex']);
        if($arIndex[2]){
            $sql = "SELECT * FROM `spms_corefunctions` where `cf_ID`='$arIndex[0]'";
        }else{
            $sql = "SELECT * FROM `spms_matrixindicators` where `mi_id`='$arIndex[0]'";
        }
        $sql = $mysqli->query($sql);
        $sql = $sql->fetch_assoc();
        if($sql['corrections']!=""){
            $c = unserialize($sql['corrections']);
            
            array_splice($c,$arIndex[1],1);
            if(!$c){
                $c = "";
            }elseif(count($c)>=1){
                $c = serialize($c);
            }else{
                $c = "";
            }
            if($arIndex[2]){
                $s = "UPDATE `spms_corefunctions` SET `corrections` = '$c' WHERE `spms_corefunctions`.`cf_ID` = '$arIndex[0]'";
            }else{
                $s = "UPDATE `spms_matrixindicators` SET `corrections` = '$c' WHERE `spms_matrixindicators`.`mi_id` = '$arIndex[0]'";
            }
            $s = $mysqli->query($s);
            if($s){
                echo 1;
            }else{
                echo "something went Wrong";
            }
        }
    }
?>