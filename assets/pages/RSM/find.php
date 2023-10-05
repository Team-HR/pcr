<?php
    $type = $_POST['type'];
    $dataId = $_POST['dataId'];
    $search = $mysqli->real_escape_string($_POST['search']);
    if($type=="Quality"){
        $dbcolname = "mi_quality";
    }elseif ($type=="Efficiency") {
        $dbcolname = "mi_eff";
    }elseif ($type=="Timeliness") {
        $dbcolname = "mi_time";
    }
    $a = [];
    $sql = "SELECT $dbcolname as result from spms_matrixindicators  where $dbcolname like '%$search%' ORDER BY mi_id DESC limit 100";
    $sql = $mysqli->query($sql);
    if($sql->num_rows){
        while($ftch = $sql->fetch_assoc()){
            $c = count($a);
            if($c>=3){
                break;
            }elseif(!$c){
                $a[] = $ftch;
            }elseif($c<3){
                $i = 0;
                $c = count($a);
                while($i<=$c){
                    if($i==$c){
                         break;  
                    }elseif($a[$i]==$ftch){
                        break;
                    }elseif($c<3){
                        $a[] = $ftch;
                        $i++;
                    }
                }
            }
        }
        $x = count($a);
        $y = 0;
        $rslt ="";
        while ($y<$x){
            $dat = unserialize($a[$y]['result']);
            $dats = "";
            $countDats = 5;
            while($countDats>=1){
                if($dat[$countDats]){
                    $dats.= "<b>".$countDats."</b> - ".$dat[$countDats]."<br>";
                }
                $countDats--;
            }
            $rslt.=  "
                <div class='card'>
                    <div class='content'>
                        <div class='description'>
                            $dats                
                        </div>
                    </div>
                    <div class='extra content'>
                    <div class='ui two buttons'>
                        <div class='ui basic green button' onclick='storeToISIinputs(\"||$dat[1]||$dat[2]||$dat[3]||$dat[4]||$dat[5]\",\"".$type.$_POST['dataId']."\")'>Use</div>
                    </div>
                    </div>
                </div>     
            ";
            $y++;
        }
        echo "
            <div class='ui cards'>
                $rslt
            </div>
            <br>
        ";
    }else{
        echo "<h3 style='text-align:center;color:#22242675;border:1px solid #22242626;padding:10px;border-radius:10px 10px 10px 10px'>No suggestions Found</h3>";
    }
?>