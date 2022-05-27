<?php
    /* <!-- 
    // example how to use this class
        ** create an object **
                $rsm = new RsmClass($host,$user,$password,$database);

        ** assign a value to period and deprtment **
        ** the value of this properties are the id assign from the database **
            -> $rsm->set_period( period_id );
            -> $rsm->set_department( department_id );
        ** use the method get_view() to get the table rows of the rsm **
                <table border="1 ">
                ->  $rsm->get_view()
                </table>


                ** God Bless **
    --> 
    */
    class RsmClass extends mysqli{

        // the id of the department
        private $department;

        // the id of the period being selected
        private $period;

        // this ID is the ID of the MFO that you want to change the parent

        private $mfoID;
        private $noSI = false;

        private $addComment = false;


        // setting up database credentials 
        function __construct($host,$user,$password,$database){
            parent::__construct($host,$user,$password,$database);
            parent::set_charset("utf8");
        }

// public functions 
        
        public function set_department($department){
            $this->department = $department;
        }
        public function set_period($period){
            $this->period = $period;
        }
        public function set_mfoID($mfoID){
            $this->mfoID = $mfoID;
            $this->noSI = true;
        }
        public function set_addComment($dat){
            $this->addComment = $dat;
        }
        public function get_view(){
            return $this->mfoparent();
        }
        private function mfoparent(){
            $department = $this->department;
            $period  = $this->period;
            $query = "SELECT * from `spms_corefunctions` where `parent_id`='' and `dep_id`='$department' and `mfo_periodId`='$period' order by `cf_count` ASC";
            $query = parent::query($query);
            $view = "";
            while ($row = $query->fetch_assoc()){
                $btnDis = false;
                if($row["cf_ID"]==$this->mfoID){
                    $btnDis = true;
                }
                $view.=$this->indicators($row,0,$btnDis);
                $view.=$this->mfochild($row['cf_ID'],0,$btnDis);
            }
            return $view;
        }
        private function mfochild($id,$padding,$btnDis){
            $padding +=20;
            $query = "SELECT * from `spms_corefunctions` where `parent_id`='$id' order by `cf_count` ASC";
            $query = parent::query($query);
            $view = "";
            while ($row = $query->fetch_assoc()){
                if($row["cf_ID"]==$this->mfoID||$btnDis){
                    $btnDis = true;
                }
                $view.=$this->indicators($row,$padding,$btnDis);
                $view.=$this->mfochild($row['cf_ID'],$padding,$btnDis);
            }
            return $view;
        }
        private function indicators($dat,$padding,$btnDis){
            $query  = "SELECT * from `spms_matrixindicators` where `cf_ID`='$dat[cf_ID]'";
            $query = parent::query($query);
            $view = "";
            $comStyle = ""; 
            if ($this->addComment) {
                $comStyle = "style='display:none'";
            }
            if($this->noSI){
                $disable = "";
                $clickFunction = "onclick='changeParent(".$this->mfoID.",$dat[cf_ID])'";
                if($btnDis){
                    $disable = "disabled";
                    $clickFunction = "";
                }
                $view .= "
                <tr>
                <td style='padding-left:".$padding."px;'>$dat[cf_count] $dat[cf_title]</td>
                <td>
                    <button class='ui primary button $disable' $clickFunction>Assign Under This MFO</button>
                </td>   
               <tr>
                ";
            }else{
                $dataElmfo = "";
                if($dat['corrections']){
                    $dataElmfo = "data-target='showCorrectionsMFO' data-id='$dat[cf_ID]'";
                }
                $colorsMfo = $this->validaateCorrection($dat['corrections']);

                if($query->num_rows<1){
                    $view .= "
                    <tr>
                    <td style='padding-left:".$padding."px;$colorsMfo' $dataElmfo>
                    <button class='circular mini ui icon green button' data-target='mfoCorrection' data-id='$dat[cf_ID]'>
                        <i class='edit icon' data-id='$dat[cf_ID]'></i> MFO
                    </button>
                    $dat[cf_count] $dat[cf_title] 
                    </td>
                    <td></td>   
                    <td></td>   
                    <td></td>   
                    <td></td>
                    <td></td>
                    <td $comStyle></td>
                    <tr>
                    ";
                }else{   
                    $count = 1;
                    while ($row = $query->fetch_assoc()){
                        $colors = "";
                        $dataEl = "";
                        if($row['corrections']){
                            $dataEl = "data-target='showCorrections' data-id='$row[mi_id]'";
                        }            
                        $colors = $this->validaateCorrection($row['corrections']);
                        if($count==1){
                            $view .= "
                            <tr class='correctionTr'>
                            <td style='padding-left:".$padding."px;$colorsMfo' $dataElmfo>
                                <button class='circular mini ui icon green button' data-target='mfoCorrection' data-id='$dat[cf_ID]'>
                                    <i class='edit icon' data-id='$dat[cf_ID]'></i> MFO
                                </button>$dat[cf_count] $dat[cf_title]
                            </td> 
                            <td style='$colors' $dataEl>$row[mi_succIn]</td>   
                            <td style='$colors' $dataEl>".$this->get_si($row['mi_quality'])."</td>   
                            <td style='$colors' $dataEl>".$this->get_si($row['mi_eff'])."</td>   
                            <td style='$colors' $dataEl>".$this->get_si($row['mi_time'])."</td>   
                            <td >".$this->get_employee($row['mi_incharge'])."</td>
                            <td $comStyle>
                                <button class='ui primary button' data-target='correction' data-id='$row[mi_id]'>
                                    Add Correction
                                </button>
                            </td>
                            <tr>
                            ";
                        }else{
                            $view .= "
                            <tr style='$colors' class='correctionTr'>
                            <td></td>   
                            <td $dataEl>$row[mi_succIn]</td>   
                            <td $dataEl>".$this->get_si($row['mi_quality'])."</td>   
                            <td $dataEl>".$this->get_si($row['mi_eff'])."</td>   
                            <td $dataEl>".$this->get_si($row['mi_time'])."</td>   
                            <td >".$this->get_employee($row['mi_incharge'])."</td>
                            <td $comStyle>
                                <button class='ui primary button' data-target='correction' data-id='$row[mi_id]'>
                                    Add Correction
                                </button>
                            </td>
                            <tr>
                            ";
                        }
                        $count++;
                    }
                }
            }
            return $view;
        }
        private function get_si($dat){
            $ar = unserialize($dat);
            $count = 5;
            $view = "";
            while ($count>=1){
                if($ar[$count]){
                    $view .=$count." - ".$ar[$count]."<br>";
                }
                $count--;
            }
            return $view;
        }

        private function validaateCorrection($dat){
            $color = "";
            if($dat){
                $color = "color:blue";
                $count = 0;
                $dat = unserialize($dat);
                while($count<count($dat)){
                    if($dat[$count][1]==0){
                        $color = "color:red";
                        break;
                    }
                    $count++;
                }
            }
            return $color;
        }


        private function get_employee($dat){
            $dat = explode(",",$dat);
            $view = "";
            foreach ($dat as $empDataId) {
                if (!$empDataId||$empDataId == null) {
                    continue;
                }
                $sql = "SELECT * from employees where `employees_id` ='$empDataId'";
                $res = parent::query($sql);
                $sqlIncharge = $res->fetch_assoc();
                $view.= "<a class='btn btn-primary button' style='cursor:pointer' data-target='showIRM' data-id='$sqlIncharge[employees_id]||".$this->period."||".$this->department."'>".$sqlIncharge['lastName']." ".$sqlIncharge['firstName']." ".$sqlIncharge['middleName']."</a><br>";
              }
            return $view;
        }
    }
?>
