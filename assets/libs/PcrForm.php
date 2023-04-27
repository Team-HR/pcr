<?php

class PcrForm
{
	private $mysqli;
	public $fileStatus;
	private $name_formatter;
	private $core_final_numerical_rating;

	public function __construct($mysqli)
	{
		require_once "NameFormatter.php";
		$this->name_formatter = new NameFormatter($mysqli);
		$this->mysqli = $mysqli;
	}

	public function set_file_status_id($id)
	{
		$mysqli = $this->mysqli;
		$file_status = [];
		$sql = "SELECT * FROM `spms_performancereviewstatus` WHERE `performanceReviewStatus_id` = '$id'";
		$res = $mysqli->query($sql);
		if ($row = $res->fetch_assoc()) {
			$file_status = $row;
		}
		$this->fileStatus = $file_status;

		// get employee department
		$department_id = $file_status["department_id"];
		$sql = "SELECT department FROM department WHERE department_id = '$department_id'";
		$res = $this->mysqli->query($sql);
		$row = $res->fetch_assoc();
		$department = $row["department"];

		$employees_id = isset($file_status["employees_id"]) ? $file_status["employees_id"] : null;

		// set employee_id for name formatting
		$this->name_formatter->set_employee_id($file_status["employees_id"]);
		$name = $this->name_formatter->getFullNameStandardUpper();

		// set employee_id for immediate name formatting
		$name_supervisor = "";
		if (is_numeric($file_status["ImmediateSup"])) {
			$this->name_formatter->set_employee_id($file_status["ImmediateSup"]);
			$name_supervisor = $this->name_formatter->getFullNameStandardUpper();
		} else {
			$name_supervisor = $file_status["ImmediateSup"];
		}

		// set employee_id for department_head name formatting
		$name_department_head = "";
		if (is_numeric($file_status["DepartmentHead"])) {
			$this->name_formatter->set_employee_id($file_status["DepartmentHead"]);
			$name_department_head = $this->name_formatter->getFullNameStandardUpper();
		} else {
			$name_supervisor = $file_status["DepartmentHead"];
		}

		$name_head_of_agency = $file_status["HeadAgency"];

		$form_type = "";

		if ($file_status["formType"] == "1") {
			$form_type = "INDIVIDUAL PERFORMANCE COMMITMENT AND REVIEW (IPCR)";
		} else if ($file_status["formType"] == "2") {
			$form_type = "SECTION PERFORMANCE COMMITMENT AND REVIEW (SPCR)";
		} else if ($file_status["formType"] == "3") {
			$form_type = "DEPARTMENT PERFORMANCE COMMITMENT AND REVIEW (DPCR)";
		} else {
			$form_type = "DIVISION PERFORMANCE COMMITMENT AND REVIEW (DIVISION PCR)";
		}
		// get position start
		$position = "";
		if ($employees_id) {
			$sql = "SELECT `employees`.`position_id`, `positiontitles`.* FROM `employees` LEFT JOIN `positiontitles` ON `employees`.`position_id` = `positiontitles`.`position_id` WHERE `employees`.`employees_id` = '$employees_id'";
			$res = $mysqli->query($sql);
			if ($row = $res->fetch_assoc()) {
				$position = $row["position"];
				if (isset($row["functional"]) && $row["functional"] != "CASUAL") {
					$position .= "- " . $row["functional"];
				}
			}
		}
		// get position end

		$this->fileStatus = [
			"name" => $name,
			"position" => $position,
			"name_supervisor" => $name_supervisor,
			"name_department_head" => $name_department_head,
			"name_head_of_agency" => $name_head_of_agency,
			"department"  => $department,
			"form_type" => $form_type,
		] + $this->fileStatus;
	}

	public function get_form_type()
	{
		return $this->fileStatus["formType"];
	}


	public function get_core_functions()
	{
		$fileStatus = $this->fileStatus;
		$mysqli = $this->mysqli;
		$arr = $this->coreAr($fileStatus);
		// return $arr;
		$employee_id = $fileStatus['employees_id'];
		$count0 = count($arr);
		$in0 = 0;
		$count = 0;
		$totalav = 0;
		$cTotal = 0;
		while ($in0 < $count0) {
			$a1 = $arr[$in0][2];
			$child = $this->coreRow_child($employee_id, $a1);
			$t0 = $this->Core_mfoRow($employee_id, $arr[$in0]);
			$count += $t0[0] + $child[0];
			$totalav += $t0[1] + $child[1];
			$cTotal += $t0[2] + $child[2];
			$in0++;
		}

		$final_numerical_rating = bcdiv($totalav, 1, 2);
		$data = [];
		# START transform $arr to table rows
		// top level of array is the parent
		// 0 - mfo
		// 1 - si
		// 2 - children


		foreach ($arr as $row) {
			$level = 0;
			$data = $this->get_child_row($level, $row, $data);
		}

		# END transform $arr to table rows

		$percent = 0;
		foreach ($data as $value) {
			$percent += $value["percent"];
		}


		return [
			"percent" => $percent,
			"rows" => $data,
			"final_numerical_rating" => $final_numerical_rating
		];
	}

	public function get_core_final_numerical_rating()
	{
		return $this->core_final_numerical_rating;
	}

	private function get_child_row($level, $row, $data)
	{
		$tr = $row[0];
		$count_si = count($row[1]);
		$rowspan = $count_si;
		$colspan = $count_si > 0 ? 0 : "all";
		$_tr = $tr = ["level" => $level, "colspan" => $colspan, "rowspan" => $rowspan] + $tr;
		// $_tr = $tr;
		// if have SI, only add the first to $tr
		if (isset($row[1][0])) {
			$si = $row[1][0];
			// you can explicitly define the $si properties
			$si = [
				"mi_id" => $si["mi_id"],
				"mi_succIn" => $si["mi_succIn"],
				"si_corrections" => $si["corrections"],
				"mi_quality" => unserialize($si["mi_quality"]),
				"mi_eff" => unserialize($si["mi_eff"]),
				"mi_time" => unserialize($si["mi_time"])
			];
			$tr = $tr + $si;
		}
		if (isset($row[2]) && count($row[2]) > 0) {
			$data[] = $tr; //mfo
			// iterate to get data of all children as well
			$level++;
			foreach ($row[2] as $child) {
				// $level++;
				$data = $this->get_child_row($level, $child, $data);
			}
		} else {
			$data[] = $tr;
		}

		// if SI is more than 1
		if (count($row[1]) > 1) {
			foreach ($row[1] as $key => $si) {
				// only append second to up since first SI is with the mfo 
				if ($key != 0) {
					// $level++;
					$si = [
						"mi_id" => $si["mi_id"],
						"mi_succIn" => $si["mi_succIn"],
						"si_corrections" => $si["corrections"],
						"mi_quality" => unserialize($si["mi_quality"]),
						"mi_eff" => unserialize($si["mi_eff"]),
						"mi_time" => unserialize($si["mi_time"])
					];
					$data[] = /* $_tr + */ $si;
				}
			}
		}

		// transform $data with fetched spms_corefuncdata
		foreach ($data as $key => $datum) {
			if (isset($datum["mi_id"])) {
				$mi_id = $datum["mi_id"];
				$core_function_data = $this->get_core_function_data($mi_id);
				$data[$key] = $data[$key] +  $core_function_data;
			}
		}

		return $data;
	}


	private function get_core_function_data($mi_id)
	{

		$mysqli = $this->mysqli;
		$employee_id = $this->fileStatus["employees_id"];
		$sql = "SELECT * FROM `spms_corefucndata` WHERE `p_id` = '$mi_id' AND `empId` = '$employee_id' LIMIT 1";
		$res = $mysqli->query($sql);
		$row = $res->fetch_assoc();

		if ($row) {

			$q = $row["Q"];
			$e = $row["E"];
			$t = $row["T"];
			$percent = $row["percent"];
			// get average
			$average = null;
			if ($q || $e || $t) {
				$measures = [];
				if ($q) {
					$measures[] = $q;
				}
				if ($e) {
					$measures[] = $e;
				}
				if ($t) {
					$measures[] = $t;
				}

				$average = array_sum($measures) / count($measures);

				if ($percent) {
					$average = ($percent / 100) * $average;
				}

				$average = bcdiv($average, 1, 2);
				$average = explode(".", $average);
				if ($average[1] == '00') {
					$average = $average[0];
				} else {
					$average = $average[0] . "." . $average[1];
				}

				if (strlen($average) == 4) {
					if ($average[3] == "0") {
						$average = substr($average, 0, -1);
					}
				}
			}

			// parse critics START
			$critics = null;
			if (isset($row["critics"])) {
				$critics = unserialize($row["critics"]);
				if (!$critics["IS"] and !$critics["DH"] and !$critics["PMT"]) {
					$critics = false;
				}
			}
			// parse critics END


			$row = [
				"cfd_id" => $row["cfd_id"],
				"actualAcc" => $row["actualAcc"],
				"q" => $q,
				"e" => $e,
				"t" => $t,
				"average" => $average,
				"percent" => $percent,
				"not_applicable" => $row["disable"],
				"critics" => $critics
			];
		} else {
			$row = [
				"cfd_id" => NULL,
			];
		}

		return $row;
	}


	private function coreAr($fileStatus = [])
	{
		$mysqli = $this->mysqli;
		# for more compact and faster query
		# ... and `dep_id` = '$department_id'
		# department_id from spms_performancereviewstatus
		$department_id = isset($fileStatus["department_id"]) ? $fileStatus["department_id"] : "";
		$period_id = $fileStatus["period_id"];
		$employee_id = $fileStatus["employees_id"];
		# not recommended department_id from employees table
		$main_Arr = [];
		$sql = "SELECT * from spms_corefunctions where parent_id='' and mfo_periodId='$period_id' and `dep_id` = '$department_id' ORDER BY `spms_corefunctions`.`cf_count` ASC";
		$sql = $mysqli->query($sql);
		$parent = [[], [], []];
		while ($core = $sql->fetch_assoc()) {
			$parent[0] = $core;
			$si = $this->si($employee_id, $core['cf_ID']);
			$child = $this->q($core['cf_ID']);
			if ($child->num_rows) {
				$parent[2] = $this->coreAr_Child($employee_id, $core['cf_ID']);
			}
			if (count($si)) {
				$parent[1] = $si;
			}
			if (count($si) || $parent[2]) {
				array_push($main_Arr, $parent);
				$parent = [[], [], []];
			}
		}
		return $main_Arr;
	}

	private function coreAr_Child($employee_id,  $dataId)
	{
		$mysqli = $this->mysqli;
		$main_Arr = [];
		$sql = $this->q($dataId);
		$parent = [[], [], []];
		while ($childCore = $sql->fetch_assoc()) {
			$parent[0] = $childCore;
			$si = $this->si($employee_id, $childCore['cf_ID']);
			$child = $this->q($childCore['cf_ID']);
			if ($child->num_rows) {
				$parent[2] = $this->coreAr_Child($employee_id, $childCore['cf_ID']);
			}
			if (count($si)) {
				$parent[1] = $si;
			}
			if (count($si) || $parent[2]) {
				array_push($main_Arr, $parent);
				$parent = [[], [], []];
			}
		}
		return $main_Arr;
	}

	private function si($employee_id, $siId)
	{
		$mysqli = $this->mysqli;
		$i = [];
		if (!$siId || $siId == null) {
			return $i;
		}
		$sqlSi1 = "SELECT * from spms_matrixindicators where cf_ID='$siId'";
		$sqlSi1 = $mysqli->query($sqlSi1);
		if ($sqlSi1->num_rows > 0) {
			while ($a = $sqlSi1->fetch_assoc()) {
				$incharge = explode(',', $a['mi_incharge']);
				$cIn = 0;
				while ($cIn < count($incharge)) {
					if ($incharge[$cIn] == $employee_id) {
						array_push($i, $a);
					}
					$cIn++;
				}
			}
		} else {
			$i = [];
		}
		return $i;
	}


	private function q($siId)
	{
		$mysqli = $this->mysqli;
		$sql = "SELECT * from spms_corefunctions where parent_id='$siId' ORDER BY `spms_corefunctions`.`cf_count` ASC";
		$sql = $mysqli->query($sql);
		return $sql;
	}

	private function coreRow_child($employee_id, $arr)
	{
		$mysqli = $this->mysqli;
		$index = 0;
		$childData = ["", "", ""];
		$count = 0;
		$totalav = 0;
		$cTotal = 0;
		while ($index < count($arr)) {
			$a2 = $arr[$index][2];
			$child = $this->coreRow_child($employee_id, $a2);
			$data = $this->Core_mfoRow($employee_id, $arr[$index]);
			$count += $data[0] + $child[0];
			$totalav += $data[1] + $child[1];
			$cTotal += $data[2] + $child[2];
			$index++;
		}
		$childData = [$count, $totalav, $cTotal];
		return $childData;
	}

	private function Core_mfoRow($employee_id, $ar)
	{
		$mysqli = $this->mysqli;
		$cTotal = 0;
		$count = 0;
		$totalav = 0;
		$inSi = 0;
		if (count($ar[1]) > 0) {
			while ($inSi < count($ar[1])) {
				if ($inSi == 0) {
					$row0 = $this->Core_siRow($employee_id, $ar[0], $ar[1][$inSi]);
					$count += $row0[0];
					$totalav += $row0[1];
					$cTotal += $row0[2];
				} else {
					$row1 = $this->Core_siRow($employee_id, ['cf_count' => '', 'cf_title' => ''], $ar[1][$inSi]);
					$count += $row1[0];
					$totalav += $row1[1];
					$cTotal += $row1[2];
				}
				$inSi++;
			}
		}
		$a = [$count, $totalav, $cTotal];
		return $a;
	}

	private function Core_siRow($employee_id, $ar, $si)
	{
		$mysqli = $this->mysqli;
		$count = 0;
		$cTotal = 0;
		$a = 0;
		if ($si != "") {
			$check = "SELECT * from spms_corefucndata where p_id='$si[mi_id]' and empId='$employee_id'";
			$check = $mysqli->query($check);
			if ($check->num_rows > 0) {
				$SiData = $check->fetch_assoc();
				$div = 0;
				if (!$SiData['disable']) {
					if ($SiData['Q'] != "") {
						$a += $SiData['Q'];
						$div += 1;
					}
					if ($SiData['E'] != "") {
						$a += $SiData['E'];
						$div += 1;
					}
					if ($SiData['T'] != "") {
						$a += $SiData['T'];
						$div += 1;
					}
					$a = ($a / $div) * ($SiData['percent'] / 100);
					$a = mb_substr($a, 0, 4);
				}
				$cTotal++;
			} else {
				$count++;
			}
		}
		$ar = [$count, $a, $cTotal];
		return $ar;
	}


	public function get_strategic_function()
	{
		$mysqli = $this->mysqli;
		$fileStatus = $this->fileStatus;
		$period_id = $fileStatus['period_id'];
		$employee_id = $fileStatus['employees_id'];

		$sql = "SELECT * from spms_strategicfuncdata where period_id = '$period_id' and emp_id = '$employee_id'";
		$sql = $mysqli->query($sql);
		$totalCount = 0;
		$totalAv = 0;
		$final_numerical_rating = 0;
		$major_function = "";
		$success_indicator = "";
		$acctual_accomplishment = "";
		if ($row = $sql->fetch_assoc()) {
			// $av = $row['Q']+$row['T'];
			$av = isset($row['average']) && $row['average'] > 0 ? $row['average'] : 0;
			$major_function = $row["mfo"];
			$success_indicator = $row["succ_in"];
			$acctual_accomplishment = $row["acc"];
			$noStrat = isset($row["noStrat"]) && $row["noStrat"] == 1 ? true : false;
			$remark = $row["remark"];
			$totalAv += $av;
			$totalCount++;
		}

		if ($totalAv > 0) {
			$final_numerical_rating = $totalAv;
			$totalAv = $totalAv / $totalCount;
		} else {
			$totalAv = 0;
		}
		if ($totalAv > 0) {
			$totalAv = $totalAv * 0.20;
			# format only two decimal places
			// $totalAv = number_format($totalAv, 2);
			// $totalAv = bcdiv($totalAv, 1, 2);
			# prevent rounding off value
			// $totalAv = intval(($totalAv * 100)) / 100;
		} else {
			$totalAv = 0;
		}
		// $totalAv = $totalAv*0.20;
		// $totalAv = $totalAv;

		$final_average_rating =  bcdiv($totalAv, 1, 2);

		return [
			"row" => $row,
			"mfo" => $major_function,
			"success_indicator" => $success_indicator,
			"acctual_accomplishment" => $acctual_accomplishment,
			"final_numerical_rating" => $final_numerical_rating,
			"final_average_rating" => $final_average_rating,
			"noStrat" => $noStrat,
			"remark" => $remark,
		];
	}

	public function get_support_functions()
	{
		$rows = [];
		$mysqli = $this->mysqli;
		$fileStatus = $this->fileStatus;
		$formType = $fileStatus['formType'];
		$employee_id = $fileStatus['employees_id'];
		$period_id = $fileStatus['period_id'];
		$totalAv = 0;

		if ($formType == '1' || $formType == '5') {
			$sql = "SELECT * FROM `spms_supportfunctions` where `type`=1";
		} elseif ($formType == '3') {
			$sql = "SELECT * FROM `spms_supportfunctions` where `type`=3";
		} elseif ($formType == '2' || $formType == '4') {
			$sql = "SELECT * FROM `spms_supportfunctions` where `type`=2";
		} else {
			// return bcdiv($totalAv, 1, 2);
		}

		$sql = $mysqli->query($sql);

		$emp_count = 0;
		$total_percentage = 0;

		while ($tr = $sql->fetch_assoc()) {
			$sqlSelect = "SELECT * from spms_supportfunctiondata where parent_id='$tr[id_suppFunc]' and emp_id='$employee_id' and period_id='$period_id'";
			$sqlSelect = $mysqli->query($sqlSelect);
			$sqlSelectCount = $sqlSelect->num_rows;
			if ($sqlSelectCount > 0) {
				$fdata = $sqlSelect->fetch_assoc();

				$av = 0;
				$total_percentage += $fdata['percent'];
				$per = $fdata['percent'] / 100;
				$q = 0;
				$e = 0;
				$t = 0;

				if ($fdata['Q'] != "") {
					$q = $fdata['Q'] * $per;
				}
				if ($fdata['E'] != "") {
					$q = $fdata['E'] * $per;
				}
				if ($fdata['T'] != "") {
					$q = $fdata['T'] * $per;
				}
				$av = $q + $e + $t;

				$fdata["critics"] = isset($fdata["critics"]) ? unserialize($fdata["critics"]) : null;

				$rows[] = [
					"mi_quality" => isset($tr["Q"]) ? unserialize($tr["Q"]) : null,
					"mi_eff" => isset($tr["E"]) ? unserialize($tr["E"]) : null,
					"mi_time" => isset($tr["T"]) ? unserialize($tr["T"]) : null,
					"q" => isset($fdata["Q"]) ? $fdata["Q"] : null,
					"e" => isset($fdata["E"]) ? $fdata["E"] : null,
					"t" => isset($fdata["T"]) ? $fdata["T"] : null,
				] + $fdata + $tr + ["average_rating" => bcdiv($av, 1, 2)];

				$totalAv += $av;
			} else {
				$emp_count++;
			}
		}

		return [
			"rows" => $rows,
			"final_numerical_rating" => bcdiv($totalAv, 1, 2),
			"percent" => $total_percentage
		];
	}

	public function get_comments_and_reccomendations()
	{
		$period_id = $this->fileStatus["period_id"];
		$employees_id = $this->fileStatus["employees_id"];
		$comments_and_reccomendations = "";
		$sql = "SELECT * FROM `spms_commentrec` WHERE `period_id` = '$period_id' AND `emp_id` = '$employees_id'";
		$res = $this->mysqli->query($sql);
		if ($row = $res->fetch_assoc()) {
			$comments_and_reccomendations = $row["comment"];
		}
		return $comments_and_reccomendations;
	}


	public function get_overall_final_rating()
	{
		$final_numerical_rating = 0;
		$final_adjectival_rating = null;

		$strategic_function = $this->get_strategic_function();
		$core_functions = $this->get_core_functions();
		$support_functions = $this->get_support_functions();

		$final_numerical_rating += isset($strategic_function["final_average_rating"]) ? $strategic_function["final_average_rating"] : 0;
		$final_numerical_rating += isset($core_functions["final_numerical_rating"]) ? $core_functions["final_numerical_rating"] : 0;
		$final_numerical_rating += isset($support_functions["final_numerical_rating"]) ? $support_functions["final_numerical_rating"] : 0;
		$final_numerical_rating = bcdiv($final_numerical_rating, 1, 2);

		if ($final_numerical_rating <= 5 && $final_numerical_rating > 4) {
			$final_adjectival_rating = "OUTSTANDING";
		} elseif ($final_numerical_rating <= 4 && $final_numerical_rating > 3) {
			$final_adjectival_rating = "VERY SATISFACTORY";
		} elseif ($final_numerical_rating <= 3 && $final_numerical_rating > 2) {
			$final_adjectival_rating = "SATISFACTORY";
		} elseif ($final_numerical_rating <= 2 && $final_numerical_rating > 1) {
			$final_adjectival_rating = "UNSATISFACTORY";
		}

		return [
			"final_numerical_rating" => $final_numerical_rating,
			"final_adjectival_rating" => $final_adjectival_rating
		];
	}
}
