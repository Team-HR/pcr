<?php
require_once "Db.php";

$db = new Db();
$mysqli = $db->getMysqli();
class IRM extends Db
{
    private $emp;
    private $period;
    private $department;
    function __construct()
	{
		parent::__construct();
	}

    public function set_cardi($emp, $period, $department)
    {
        $this->emp = $emp;
        $this->period = $period;
        $this->department = $department;
    }
    public function get_view()
    {
        return $this->mfoparent();
    }
    private function mfoparent()
    {
        $department = $this->department;
        $period  = $this->period;
        $query = "SELECT * from spms_pcr_mfos where parent_id='' and dep_id='$department' and mfo_periodId='$period' order by cf_count ASC";
        $query = $this->mysqli->query($query);
        $view = "";
        while ($row = $query->fetch_assoc()) {
            $a[1] = 0;
            $indicate = $this->indicators($row, 0);
            $c = $this->mfochild($row['cf_ID'], 0);
            if ($c[1] || $indicate[1]) {
                $view .= $indicate[0];
                $view .= $c[0];
            }
        }
        return $view;
    }
    private function mfochild($id, $padding)
    {
        $padding += 20;
        $query = "SELECT * from spms_pcr_mfos where parent_id='$id' order by cf_count ASC";
        $query = $this->mysqli->query($query);
        $a = [];
        $a[1] = 0;
        $view  = "";
        while ($row = $query->fetch_assoc()) {
            $indicate = $this->indicators($row, $padding);
            $c = $this->mfochild($row['cf_ID'], $padding);
            if ($c[1] || $indicate[1]) {
                $view .= $indicate[0];
                $view .= $c[0];
                $a[1] = 1;
            }
        }
        $a[0] = $view;
        return $a;
    }

    private function indicators($dat, $padding)
    {
        $query  = "SELECT * from spms_pcr_indicators where cf_ID='$dat[cf_ID]'";
        $query = $this->mysqli->query($query);
        $a = [];
        $IndiCount = 0;
        $view = "";
        if ($query->num_rows < 1) {
            $view .= "
            <tr>
            <td style='padding-left:" . $padding . "px;'>$dat[cf_count] $dat[cf_title]</td>
            <td></td>   
            <td></td>   
            <td></td>   
            <td></td>
            <tr>
            ";
        } else {
            $count = 1;
            while ($row = $query->fetch_assoc()) {
                if ($this->get_employee_by_si($row['mi_id'])) {
                    $IndiCount = 1;
                    if ($count == 1) {
                        $view .= "
                        <tr>
                        <td style='padding-left:" . $padding . "px;'>$dat[cf_count] $dat[cf_title]</td>
                        <td>$row[mi_succIn]</td>   
                        <td>" . $this->get_si($row['mi_id'], 'quality') . "</td>   
                        <td>" . $this->get_si($row['mi_id'], 'efficiency') . "</td>   
                        <td>" . $this->get_si($row['mi_id'], 'timeliness') . "</td>   
                        <tr>
                        ";
                    } else {
                        $view .= "
                        <tr>
                        <td></td>   
                        <td>$row[mi_succIn]</td>   
                        <td>" . $this->get_si($row['mi_id'], 'quality') . "</td>   
                        <td>" . $this->get_si($row['mi_id'], 'efficiency') . "</td>   
                        <td>" . $this->get_si($row['mi_id'], 'timeliness') . "</td>   
                        <tr>
                        ";
                    }
                }
                $count++;
            }
        }
        $a[0] = $view;
        $a[1] = $IndiCount;
        return $a;
    }
    private function get_si($mi_id, $measure_type)
    {
        $view = "";
        $query = "SELECT score, descriptor FROM spms_pcr_si_qet_descriptors
                  WHERE success_indicator_id = '$mi_id' AND measure_type = '$measure_type'
                  ORDER BY score DESC";
        $result = $this->mysqli->query($query);
        while ($row = $result->fetch_assoc()) {
            $view .= $row['score'] . " - " . htmlspecialchars($row['descriptor']) . "<br>";
        }
        return $view;
    }
    private function get_employee_by_si($mi_id)
    {
        $emp   = $this->emp;
        $query = "SELECT id FROM spms_pcr_si_assignments
                  WHERE success_indicator_id = '$mi_id' AND user_id = '$emp'
                  LIMIT 1";
        $result = $this->mysqli->query($query);
        return $result && $result->num_rows > 0;
    }
}
