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

require_once "Db.php";

$db = new Db();
$mysqli = $db->getMysqli();

class RsmClass extends Db
{
    // the id of the department
    private $department;
    // the id of the period being selected
    private $period;
    // this ID is the ID of the MFO that you want to change the parent

    private $mfoID;
    private $noSI = false;

    private $addComment = false;
    public $rating_scale_matrix_rows;

    // setting up database credentials 
    // function __construct($host, $user, $password, $database)
    // {
    //     parent::__construct($host, $user, $password, $database);
    //     parent::set_charset("utf8");
    // }

    function __construct()
    {
        parent::__construct();
    }

    // public functions 

    public function set_department($department)
    {
        $this->department = $department;
    }

    public function get_department()
    {
        return $this->department;
    }

    public function set_period($period)
    {
        $this->period = $period;
    }
    public function set_mfoID($mfoID)
    {
        $this->mfoID = $mfoID;
        $this->noSI = true;
    }
    public function set_addComment($dat)
    {
        $this->addComment = $dat;
    }
    public function get_view()
    {
        return $this->mfoparent();
    }

    public function get_rating_scale_matrix()
    {
        $this->get_mfo_top();
    }

    public function get_rating_scale_matrix_rows()
    {
        $this->get_rating_scale_matrix();
        return json_encode($this->rating_scale_matrix_rows);
    }

    private function get_mfo_top()
    {
        $department = $this->department;
        $period  = $this->period;
        $data = [];
        $query = "SELECT * from `spms_corefunctions` where `parent_id`='' and `dep_id`='$department' and `mfo_periodId`='$period' order by `cf_count` ASC";
        $result = $this->mysqli->query($query);

        while ($row = $result->fetch_assoc()) {
            $item = [
                "id" => $row["cf_ID"],
                "parent_id" => $row["parent_id"],
                "level" => 0,
                "code" => $row["cf_count"],
                "title" => $row["cf_title"],
                "mfo_corrections" => unserialize($row["corrections"]),
                "correction_status" => $this->get_correction_status(unserialize($row["corrections"]))
            ];

            # get success indicators
            foreach ($this->get_success_indicators($item["id"]) as $key => $succes_indicator) {
                if ($key == 0) {
                    $this->rating_scale_matrix_rows[] = array_merge($item, $succes_indicator);
                } else {
                    $this->rating_scale_matrix_rows[] = array_merge([
                        "id" => "",
                        "parent_id" => "",
                        "level" => 0,
                        "code" => "",
                        "title" => ""
                    ], $succes_indicator);
                }
            }

            if (!$this->get_success_indicators($item["id"])) {
                $this->rating_scale_matrix_rows[] = $item;
            }


            $level = 0;
            foreach ($this->get_mfo_children($item["id"], $level) as $key => $value) {
                $this->rating_scale_matrix_rows[] = $value;
            }
        }
    }


    # get_mfo_top()
    private function mfoparent()
    {
        $department = $this->department;
        $period  = $this->period;
        $query = "SELECT * from `spms_corefunctions` where `parent_id`='' and `dep_id`='$department' and `mfo_periodId`='$period' order by `cf_count` ASC";
        $query = $this->mysqli->query($query);
        $view = "";
        while ($row = $query->fetch_assoc()) {
            $btnDis = false;
            if ($row["cf_ID"] == $this->mfoID) {
                $btnDis = true;
            }
            $view .= $this->indicators($row, 0, $btnDis);
            $view .= $this->mfochild($row['cf_ID'], 0, $btnDis);
        }
        return $view;
    }
    # get_mfo_children
    private function get_mfo_children($parent_id, $level)
    {
        $level += 1;
        $department = $this->department;
        $period  = $this->period;
        $data = [];
        # 
        # Better check on children with wrong mfo_periodId OR
        # disregard since child depends on parent_id with proper mfo_periodId?
        # and `mfo_periodId`='$period'
        #
        $sql = "SELECT * from `spms_corefunctions` where `parent_id`='$parent_id' and `dep_id`='$department' order by `cf_count` ASC";
        $result = $this->mysqli->query($sql);
        while ($row = $result->fetch_assoc()) {
            $item = [
                "id" => $row["cf_ID"],
                "parent_id" => $parent_id,
                "level" => $level,
                "code" => $row["cf_count"],
                "title" => $row["cf_title"],
                "mfo_corrections" => unserialize($row["corrections"]),
                "correction_status" => $this->get_correction_status(unserialize($row["corrections"]))
            ];

            # get success indicators
            foreach ($this->get_success_indicators($item["id"]) as $key => $succes_indicator) {
                if ($key == 0) {
                    $data[] = array_merge($item, $succes_indicator);
                } else {
                    $data[] = array_merge([
                        "id" => "",
                        "parent_id" => "",
                        "level" => $level,
                        "code" => "",
                        "title" => ""
                    ], $succes_indicator);
                }
            }

            if (!$this->get_success_indicators($item["id"])) {
                $data[] = $item;
            }
            // $data[] = $item;

            foreach ($this->get_mfo_children($item["id"], $level) as $key => $value) {
                $data[] = $value;
            }
        }
        return $data;
    }

    # get_correction_status
    private function get_correction_status($corrections)
    {
        if (!$corrections) return false;
        foreach ($corrections as $key => $correction) {
            if ($correction[1] == 0) {
                return "red";
            } else {
                return "blue";
            }
        }
    }

    # get_success_indicators
    private function get_success_indicators($cf_ID)
    {
        $query = "SELECT * FROM `spms_matrixindicators` WHERE `cf_ID` = '$cf_ID' ORDER BY `mi_id` ASC";
        $result = $this->mysqli->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $item = [
                "mi_id" => $row["mi_id"],
                "cf_id" => $row["cf_ID"],
                "success_indicator" => $row["mi_succIn"],
                "qualities" => $this->parse_ratings($row["mi_quality"]),
                "efficiencies" => $this->parse_ratings($row["mi_eff"]),
                "timelinesses" => $this->parse_ratings($row["mi_time"]),
                "incharges" => $this->get_incharges($row["mi_incharge"]),
                "si_corrections" => unserialize($row["corrections"]),
                "si_correction_status" => $this->get_correction_status(unserialize($row["corrections"]))
            ];
            $data[] = $item;
        }
        return $data;
    }

    private function get_incharges($mi_incharge)
    {
        if (!$mi_incharge) return [];
        $incharges = explode(",", $mi_incharge);
        $data = [];
        foreach ($incharges as $employee_id) {
            $query = "SELECT `employees_id`, `lastName`, `firstName`, `middleName`, `extName` FROM `employees` WHERE `employees_id` = '$employee_id';";
            $result = $this->mysqli->query($query);
            $row = $result->fetch_assoc();
            if ($row) {
                # code...
                $data[] = [
                    "id" => $row["employees_id"],
                    "name" => $row["lastName"] . ", " . $row["firstName"] //. $row["middleName"] ? " " . $row["middleName"][0] : "" . $row["extName"] ? " " . $row["extName"] : ""
                ];
            }
        }
        return $data;
    }

    private function parse_ratings($rating)
    {

        $empty = true;
        $scales = unserialize($rating);
        if (!is_array($scales)) {
            return [];
        }
        foreach ($scales as $scale) {
            if ($scale) {
                $empty = false;
            }
        }
        if ($empty) {
            return  [];
        }

        $data = [];
        foreach ($scales as $score => $description) {
            if ($description) {
                $data[] = [
                    "score" => $score,
                    "description" => $description
                ];
            }
        }

        usort($data, fn($a, $b) => strcmp($b["score"], $a["score"]));


        return $data;
    }

    private function mfochild($id, $padding, $btnDis)
    {
        $padding += 20;
        $query = "SELECT * from `spms_corefunctions` where `parent_id`='$id' order by `cf_count` ASC";
        $query = $this->mysqli->query($query);
        $view = "";
        while ($row = $query->fetch_assoc()) {
            if ($row["cf_ID"] == $this->mfoID || $btnDis) {
                $btnDis = true;
            }
            $view .= $this->indicators($row, $padding, $btnDis);
            $view .= $this->mfochild($row['cf_ID'], $padding, $btnDis);
        }
        return $view;
    }
    private function indicators($dat, $padding, $btnDis)
    {
        $query  = "SELECT * from `spms_matrixindicators` where `cf_ID`='$dat[cf_ID]'";
        $query = $this->mysqli->query($query);
        $view = "";
        $comStyle = "";
        if ($this->addComment) {
            $comStyle = "style='display:none'";
        }
        if ($this->noSI) {
            $disable = "";
            $clickFunction = "onclick='changeParent(" . $this->mfoID . ",$dat[cf_ID])'";
            if ($btnDis) {
                $disable = "disabled";
                $clickFunction = "";
            }
            $view .= "
                <tr>
                <td style='padding-left:" . $padding . "px;'>$dat[cf_count] $dat[cf_title]</td>
                <td>
                    <button class='ui primary button $disable' $clickFunction>Assign Under This MFO</button>
                </td>   
               <tr>
                ";
        } else {
            $dataElmfo = "";
            if ($dat['corrections']) {
                $dataElmfo = "data-target='showCorrectionsMFO' data-id='$dat[cf_ID]'";
            }
            $colorsMfo = $this->validaateCorrection($dat['corrections']);

            if ($query->num_rows < 1) {
                $view .= "
                    <tr>
                    <td style='padding-left:" . $padding . "px;$colorsMfo' $dataElmfo>
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
            } else {
                $count = 1;
                while ($row = $query->fetch_assoc()) {
                    $colors = "";
                    $dataEl = "";
                    if ($row['corrections']) {
                        $dataEl = "data-target='showCorrections' data-id='$row[mi_id]'";
                    }
                    $colors = $this->validaateCorrection($row['corrections']);
                    if ($count == 1) {
                        $view .= "
                            <tr class='correctionTr'>
                            <td style='padding-left:" . $padding . "px;$colorsMfo' $dataElmfo>
                                <button class='circular mini ui icon green button' data-target='mfoCorrection' data-id='$dat[cf_ID]'>
                                    <i class='edit icon' data-id='$dat[cf_ID]'></i> MFO
                                </button>$dat[cf_count] $dat[cf_title]
                            </td> 
                            <td style='$colors' $dataEl>$row[mi_succIn]</td>   
                            <td style='$colors' $dataEl>" . $this->get_si($row['mi_quality']) . "</td>   
                            <td style='$colors' $dataEl>" . $this->get_si($row['mi_eff']) . "</td>   
                            <td style='$colors' $dataEl>" . $this->get_si($row['mi_time']) . "</td>   
                            <td >" . $this->get_employee($row['mi_incharge']) . "</td>
                            <td $comStyle>
                                <button class='ui primary button' data-target='correction' data-id='$row[mi_id]'>
                                    Add Correction
                                </button>
                            </td>
                            <tr>
                            ";
                    } else {
                        $view .= "
                            <tr style='$colors' class='correctionTr'>
                            <td></td>   
                            <td $dataEl>$row[mi_succIn]</td>   
                            <td $dataEl>" . $this->get_si($row['mi_quality']) . "</td>   
                            <td $dataEl>" . $this->get_si($row['mi_eff']) . "</td>   
                            <td $dataEl>" . $this->get_si($row['mi_time']) . "</td>   
                            <td >" . $this->get_employee($row['mi_incharge']) . "</td>
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
    private function get_si($dat)
    {
        $ar = unserialize($dat);
        $count = 5;
        $view = "";
        while ($count >= 1) {
            if ($ar[$count]) {
                $view .= $count . " - " . $ar[$count] . "<br>";
            }
            $count--;
        }
        return $view;
    }

    private function validaateCorrection($dat)
    {
        $color = "";
        if ($dat) {
            $color = "color:blue";
            $count = 0;
            $dat = unserialize($dat);
            while ($count < count($dat)) {
                if ($dat[$count][1] == 0) {
                    $color = "color:red";
                    break;
                }
                $count++;
            }
        }
        return $color;
    }


    private function get_employee($dat)
    {
        $dat = explode(",", $dat);
        $view = "";
        foreach ($dat as $empDataId) {
            if (!$empDataId || $empDataId == null) {
                continue;
            }
            $sql = "SELECT * from employees where `employees_id` ='$empDataId'";
            $res = $this->mysqli->query($sql);
            $sqlIncharge = $res->fetch_assoc();
            $view .= "<a class='btn btn-primary button' style='cursor:pointer' data-target='showIRM' data-id='$sqlIncharge[employees_id]||" . $this->period . "||" . $this->department . "'>" . $sqlIncharge['lastName'] . " " . $sqlIncharge['firstName'] . " " . $sqlIncharge['middleName'] . "</a><br>";
        }
        return $view;
    }
}
