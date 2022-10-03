<?php
/**
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
*/
    class RsmClass extends mysqli{

        // the id of the department
        private $department;

        // the id of the period being selected
        private $period;

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
                $view.=$this->indicators($row,0);
                $view.=$this->mfochild($row['cf_ID'],0);
            }
            return $view;
        }
        private function mfochild($id,$padding){
            $padding +=20;
            $query = "SELECT * from `spms_corefunctions` where `parent_id`='$id' order by `cf_count` ASC";
            $query = parent::query($query);
            $view = "";
            while ($row = $query->fetch_assoc()){
                $view.=$this->indicators($row,$padding);
                $view.=$this->mfochild($row['cf_ID'],$padding);
            }
            return $view;
        }
        private function indicators($dat,$padding){
            $query  = "SELECT * from `spms_matrixindicators` where `cf_ID`='$dat[cf_ID]'";
            $query = parent::query($query);
            $view = "";
            if($query->num_rows<1){
                $view .= "
                <tr>
                <td style='padding-left:".$padding."px;'>$dat[cf_count] $dat[cf_title]</td>
                <td></td>   
                <td></td>   
                <td></td>   
                <td></td>
                <td></td>
                <tr>
                ";
            }else{   
                $count = 1;
                while ($row = $query->fetch_assoc()){
                    if($count==1){
                        $view .= "
                            <tr>
                                <td style='padding-left:".$padding."px;'>$dat[cf_count] $dat[cf_title]</td>
                                <td>$row[mi_succIn]</td>   
                                <td>".$this->get_si($row['mi_quality'])."</td>   
                                <td>".$this->get_si($row['mi_eff'])."</td>   
                                <td>".$this->get_si($row['mi_time'])."</td>   
                                <td>".$this->get_employee($row['mi_incharge'])."</td>
                            <tr>
                        ";
                    }else{
                        $view .= "
                            <tr>
                                <td></td>   
                                <td>$row[mi_succIn]</td>   
                                <td>".$this->get_si($row['mi_quality'])."</td>   
                                <td>".$this->get_si($row['mi_eff'])."</td>   
                                <td>".$this->get_si($row['mi_time'])."</td>   
                                <td>".$this->get_employee($row['mi_incharge'])."</td>
                            <tr>
                        ";
                    }
                    $count++;
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

        private function get_employee($dat){
            $dat = explode(",",$dat);
            $view = "";
            foreach ($dat as $empDataId) {
                $sqlIncharge = "SELECT * from employees where employees_id='$empDataId'";
                $sqlIncharge = parent::query($sqlIncharge);
                $sqlIncharge = $sqlIncharge->fetch_assoc();
                $view.= $sqlIncharge['lastName']." ".$sqlIncharge['firstName']." ".$sqlIncharge['middleName']."<br>";
              }
            return $view;
        }
    }
    ?>
