<?php

//	Apache/2.4.39 (Win64) OpenSSL/1.0.2s PHP/7.1.31
//	DevelopmentYear 2019 -
//	@Author
//	** pascual Tomulto
// 	** message me @https://www.facebook.com/pascual.tomulto.54
//  ** messageCode "PCR Help"
//	heart the program miss with it carefully
//	to the next programmer
//  your better then I am ; I know nothing
//  we only need logic That is our power
//	great achievements start from useless ideas  " God Bless "
//	crazy is what we are
date_default_timezone_set("Asia/Manila");
$host = "localhost";
$usernameDb = "admin";
// $password = "teamhrmo2019";
$password = "teamhrmo2019";
$database = "ihris";
$mysqli = new mysqli($host, $usernameDb, $password, $database);
$mysqli->set_charset("utf8");
class Employee_data extends mysqli
{
	public $accountStatus;
	private $rsmStatus;
	private $emp_ID;
	private $department_id;
	private $per_ID;
	private $coreData;
	private $period;
	private $EmpInfo;
	public $hide;
	public $hideCol;
	public $fileStatus;
	public $budgetView;
	public $accountableView;
	public $authorization;
	private $percent;
	private $supportPercent;
	private $strtPercent;


	// properties made for core function

	public $core_countEmpty;
	public $core_countTotal;
	public $core_totalAv;
	private $coreView;

	// properties made for support function

	private $supportView;
	public $support_countEmpty;
	public $support_totalAv;

	// properties made for strategic function

	private $strategicView;
	private $strtBtn;
	public $strategic_totalAv;
	public $strategic_count;

	//properties for the comment

	private $commentData;
	private $comment;
	public $commentCount;

	//signatories

	private $signa_form;
	public $signatoriesCount;

	// pmt 

	private $pmt = false;


	// method for my objects

	function __construct()
	{
		$host = "localhost";
		$usernameDb = "admin";
		// $password = "teamhrmo2019";
		$password = "teamhrmo2019";
		$database = "ihris";
		parent::__construct($host, $usernameDb, $password, $database);
		parent::set_charset("utf8");

		# usefull later in compacting queries for faster application loading
		$this->department_id = $_SESSION["emp_info"]["department_id"];
		// echo json_encode($this->department_id);
	}
	private function load()
	{
		$this->file_status();
		$this->comment();
		$this->signatories();
		$this->coreRow();
		$this->supportFunctionTr();
		$this->strategicTr();
	}
	public function set_hide($h)
	{
		$this->hide = $h;
		$this->load();
	}
	public function set_emp($emp)
	{
		$this->emp_ID = $emp;
		// getting all the data of the employee
		$sql = "SELECT * from employees
		left join department on employees.department_id=department.department_id
		left join positiontitles on employees.position_id=positiontitles.position_id
		where employees_id='$this->emp_ID'";
		$sql = mysqli::query($sql);
		if (!$sql) {
			die($this->error);
		}
		$this->EmpInfo = $sql->fetch_assoc();
		$authSql = "SELECT * FROM `spms_accounts` where employees_id='$this->emp_ID'";
		$authSql = mysqli::query($authSql);
		$authSql = $authSql->fetch_assoc();
		if ($authSql['type'] == "") {
			$this->authorization = "";
		} else {
			$this->authorization = explode(',', $authSql['type']);
		}
	}
	public function set_period($per)
	{
		$this->per_ID = $per;
		//retriving all the data of period
		$sql = "SELECT  * from spms_mfo_period where mfoperiod_id='$this->per_ID'";
		$sql = mysqli::query($sql);
		if (!$sql) {
			die($this->error);
		}
		$this->period = $sql->fetch_assoc();
		$this->coreData = $this->coreAr();
		$this->load();
	}
	public function set_periodMY($m, $y)
	{
		//retriving all the data of period
		$sql = "SELECT  * from spms_mfo_period where month_mfo='$m' and year_mfo='$y'";
		$sql = mysqli::query($sql);
		if (!$sql) {
			die($this->error);
		}
		$data = $sql->fetch_assoc();
		$this->per_ID = $data['mfoperiod_id'];
		$this->period = $data;
		$this->coreData = $this->coreAr();
		$this->load();
	}

	private function get_percent()
	{
		return $this->percent;
	}

	private function get_supportPercent()
	{
		return $this->supportPercent;
	}

	private function get_strtPercent()
	{
		$dat = $this->strtPercent;
		if ($dat == "20") {
			$dat .= "%";
		}
		return $dat;
	}

	public function get_emp($i)
	{
		$emp = $this->EmpInfo;
		return $emp[$i];
	}
	public function get_period($i)
	{
		$per = $this->period;
		return $per[$i];
	}
	private function get_fullname($id)
	{
		if (!is_numeric($id)) {
			return $id;
		}
		$sql = "SELECT * from employees where employees_id='$id'";
		$sql = mysqli::query($sql);
		$sql = $sql->fetch_assoc();

		$firstName = isset($sql["firstName"]) ? $sql["firstName"] : "";
		$lastName = isset($sql["lastName"]) ? $sql["lastName"] : "";

		$middleName = " ";
		if (isset($sql["middleName"])) {
			$middleName = $sql["middleName"];
			if ($middleName == ".") {
				$middleName = " ";
			} else {
				if (strlen($middleName) > 0) {
					$middleName = " " . $middleName[0] . ". ";
				} else $middleName = " ";
			}
		}

		$extName = "";
		if (isset($sql["extName"])) {
			$extName = $sql["extName"];
			if ($extName) {
				$extName = strtoupper($extName);
				$extName = ", " . $extName . ".";
			}
		}


		$fullname =  mb_convert_case("$firstName$middleName$lastName$extName", MB_CASE_UPPER, "UTF-8");

		return $fullname;
	}

	// method made for reviewing status of the file

	private function file_status()
	{
		$perStatus = "SELECT * from spms_performancereviewstatus where period_id='$this->per_ID' and employees_id='$this->emp_ID'";
		$perStatus = mysqli::query($perStatus);
		$countData = $perStatus->num_rows;
		$perStatus = $perStatus->fetch_assoc();
		# put values in $perStatus to prevent null errors
		if (!$perStatus) {
			$perStatus = [
				'performanceReviewStatus_id' => '',
				'period_id' => $this->per_ID,
				'employees_id' => $this->emp_ID,
				'ImmediateSup' => '',
				'DepartmentHead' => '',
				'HeadAgency' => '',
				'PMT' => '',
				'submitted' => '',
				'certify' => '',
				'approved' => '',
				'panelApproved' => '',
				'dateAccomplished' => '',
				'formType' => '',
				'department_id' => '',
				'assembleAll' => '',
			];
		}


		// var_dump(json_encode($perStatus));
		$this->fileStatus = $perStatus;
		$this->signatoriesCount = $countData;
		$accountId = $_SESSION['emp_id'];
		if (!isset($perStatus['panelApproved']) || $perStatus['panelApproved'] != "") {
			$this->hideCol = true;
		} else {
			// if employee 
			if ($accountId == $perStatus['employees_id']) {
				if (strtoupper($perStatus['submitted']) == "DONE") {
					$this->hideCol = true;
				} else {
					$this->hideCol = false;
				}
			}
			// for reviewers 
			// 1 - ipcr |2 - spcr |3 - dpcr |4 - division 
			elseif ($perStatus['formType'] == 1) {
				if ($accountId == $perStatus['PMT']) {
					if ($perStatus['panelApproved'] != "") {
						$this->hideCol = true;
					} else {
						$this->hideCol = false;
					}
				} elseif ($accountId == $perStatus['ImmediateSup']) {
					if ($perStatus['approved'] != "" || $perStatus['certify'] != "") {
						$this->hideCol = true;
					} else {
						$this->hideCol = false;
					}
				} elseif ($accountId == $perStatus['DepartmentHead']) {
					if ($perStatus['certify'] != "") {
						$this->hideCol = true;
					} else {
						$this->hideCol = false;
					}
				} elseif ($accountId == $perStatus['employees_id']) {
					if ($perStatus['approved'] != "" || $perStatus['certify'] != "") {
						$this->hideCol = true;
					}
				}
			} elseif ($perStatus['formType'] == 2 || $perStatus['formType'] == 4) {
				if ($accountId == $perStatus['DepartmentHead'] || $accountId == $perStatus['ImmediateSup']) {
					if ($perStatus['approved'] != "" || $perStatus['certify'] != "") {
						$this->hideCol = true;
					} elseif ($accountId == $perStatus['PMT']) {
						if ($perStatus['panelApproved'] != "") {
							$this->hideCol = true;
						} else {
							$this->hideCol = false;
						}	//pmt
					} else {
						$this->hideCol = false;
					}
				} elseif ($accountId == $perStatus['employees_id']) {
					if ($perStatus['approved'] != "" || $perStatus['certify'] != "") {
						$this->hideCol = true;
					}
				} else {
				}
			}
		}

		if (isset($perStatus['formType'])) {
			if ($perStatus['formType'] == 2) {
				$this->budgetView	 = 'display:none';
			} else if ($perStatus['formType'] == 1) {
				$this->budgetView = 'display:none';
				$this->accountableView = 'display:none';
			}
		}
	}
	public function get_status($i)
	{
		$dat = $this->fileStatus;
		return $dat[$i];
	}

	public function validate_rsm()
	{
		$period = $this->period;
		$department = $this->get_emp('department_id');
		$sql = "SELECT * `spms_rsmstatus` where `period_id`='$period[mfoperiod_id]' and `department_id`='$department'";
		$sql = mysql::query($sql);
		$this->rsmStatus = $sql->fetch_assoc();
	}

	private function coreAr()
	{
		# for more compact and faster query
		$department_id = $this->department_id;
		$main_Arr = [];
		$sql = "SELECT * from spms_corefunctions where parent_id='' and mfo_periodId='$this->per_ID' and `dep_id` = '$department_id' ORDER BY `spms_corefunctions`.`cf_count` ASC";
		$sql = mysqli::query($sql);
		$parent = [[], [], []];
		while ($core = $sql->fetch_assoc()) {
			$parent[0] = $core;
			$si = $this->si($core['cf_ID']);
			$child = $this->q($core['cf_ID']);
			if ($child->num_rows) {
				$parent[2] = $this->coreAr_Child($core['cf_ID']);
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
	private function coreAr_Child($dataId)
	{
		$main_Arr = [];
		$sql = $this->q($dataId);
		$parent = [[], [], []];
		while ($childCore = $sql->fetch_assoc()) {
			$parent[0] = $childCore;
			$si = $this->si($childCore['cf_ID']);
			$child = $this->q($childCore['cf_ID']);
			if ($child->num_rows) {
				$parent[2] = $this->coreAr_Child($childCore['cf_ID']);
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

	private function q($i)
	{

		$sql = "SELECT * from spms_corefunctions where parent_id='$i' ORDER BY `spms_corefunctions`.`cf_count` ASC";
		$sql = mysqli::query($sql);
		if (!$sql) {
			die($this->error);
		}
		return $sql;
	}

	private function si($siId)
	{
		$i = [];

		if (!$siId || $siId == null) {
			return $i;
		}

		$sqlSi1 = "SELECT * from spms_matrixindicators where cf_ID='$siId'";
		$sqlSi1 = mysqli::query($sqlSi1);
		if (!$sqlSi1) {
			die($this->error);
		}
		if ($sqlSi1->num_rows > 0) {
			while ($a = $sqlSi1->fetch_assoc()) {
				$incharge = explode(',', $a['mi_incharge']);
				$cIn = 0;
				while ($cIn < count($incharge)) {
					if ($incharge[$cIn] == $this->emp_ID) {
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
	//methods for core function
	private function coreRow()
	{
		$this->percent = 0;
		$arr = $this->coreAr();
		$col = "";
		if (!$this->hideCol) {
			$col = "<td class='noprint' ></td>";
		}
		$count0 = count($arr);
		$in0 = 0;
		$count = 0;
		$totalav = 0;
		$cTotal = 0;
		$view = "";
		while ($in0 < $count0) {
			$a1 = $arr[$in0][2];
			$child = $this->coreRow_child(5, $a1);
			$t0 = $this->Core_mfoRow(5, $arr[$in0]);
			$view .= $t0[0] . $child[0];
			$count += $t0[1] + $child[1];
			$totalav += $t0[2] + $child[2];
			$cTotal += $t0[3] + $child[3];
			$in0++;
		}
		// if($cTotal>0){
		// 	// $totalav = $totalav/$cTotal;
		// }else{
		// 	$totalav=0;
		// }
		// $totalav = $totalav*0.60;
		// $a = [$view,$count,count($arr),$totalav];
		$view1 = "<tr style='background:#f7f70026'><td colspan='8'><b>Core Function</b> (<b style='color:blue'>" . $this->get_percent() . " %</b>)</td><td class='noprint'></td>
		<td style='$this->budgetView'></td>
		<td style='$this->accountableView'></td>
		$col</tr>";
		$this->core_countEmpty = $count;
		$this->core_countTotal = count($arr);
		$this->core_totalAv	= $totalav;
		$this->coreView = $view1 . $view;
	}
	private function coreRow_child($padding, $arr)
	{
		$index = 0;
		$childData = ["", "", "", ""];
		$view = "";
		$count = 0;
		$totalav = 0;
		$cTotal = 0;
		$padding += 15;
		while ($index < count($arr)) {
			$a2 = $arr[$index][2];
			$child = $this->coreRow_child($padding, $a2);
			$data = $this->Core_mfoRow($padding, $arr[$index]);
			$view .= $data[0] . $child[0];
			$count += $data[1] + $child[1];
			$totalav += $data[2] + $child[2];
			$cTotal += $data[3] + $child[3];
			$index++;
		}
		$childData = [$view, $count, $totalav, $cTotal];
		return $childData;
	}
	private function accountblePersons($perId)
	{
		$emp = $this->emp_ID;
		$indicators = mysqli::query("SELECT * FROM `spms_matrixindicators` where cf_ID='$perId'");
		while ($empId = $indicators->fetch_assoc()) {
			$emp .= "," . $empId['mi_incharge'];
		}
		$emp  = explode(",", $emp);
		$emp = array_unique($emp);
		$view = "<br>";
		$emp_length = count($emp);
		foreach ($emp as $i => $employee_id) {
			$view .= $this->get_fullname($employee_id);
			if ($i < $emp_length && $emp_length > 1) {
				$view .= ";<br>";
			}
		}
		return $view;
	}
	private function Core_siRow($padding, $ar, $si)
	{
		$count = 0;
		$cTotal = 0;
		$a = 0;
		if ($si != "") {
			$check = "SELECT * from spms_corefucndata where p_id='$si[mi_id]' and empId='$this->emp_ID'";
			$check = mysqli::query($check);
			$accountableNames = "";
			if ($this->get_status('formType') > 1) {
				if (isset($ar['cf_ID'])) {
					$accountableNames =	$this->accountblePersons($ar['cf_ID']);
				}
			}
			if ($check->num_rows > 0) {
				$SiData = $check->fetch_assoc();
				$div = 0;
				$tableRowColor = "";
				$actualAcc_row = "";
				$q_row = '';
				$e_row = '';
				$t_row = '';
				$per_row = "";
				if ($SiData['supEdit']) {
					$arr = unserialize($SiData['supEdit']);
					// print_r($arr);
					for ($arr_index = 0; $arr_index < count($arr); $arr_index++) {
						$editedArr = $arr[$arr_index][1];
						for ($editedArr_index = 0; $editedArr_index < count($editedArr); $editedArr_index++) {
							if ($editedArr[$editedArr_index][0] == "Q") {
								$q_row = 'color:#ff00f5';
							} else if ($editedArr[$editedArr_index][0] == "E") {
								$e_row = 'color:#ff00f5';
							} else if ($editedArr[$editedArr_index][0] == "T") {
								$t_row = 'color:#ff00f5';
							} else if ($editedArr[$editedArr_index][0] == "actualAcc") {
								$actualAcc_row = 'color:#ff00f5';
							} else if ($editedArr[$editedArr_index][0] == "percent") {
								$per_row = 'color:#ff00f5';
							}
						}
					}
				}
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
				$col = "";
				if (!$this->hideCol) {
					$col = "
					<td style='width:10px;padding:10px;' class='noprint'>
					<i class='inverted circular blue edit icon' onclick='EditCoreFuncData(\"$SiData[cfd_id]\")'></i>
					<br>
					<br>
					<i style='$this->hide' class='inverted circular grey undo icon' onclick='RemoveCoreFuncData(\"$SiData[cfd_id]\")'></i>
					</td>
					";
				}
				$sideTag = "";
				if ($SiData['critics']) {
					$sideTag = "<a class='ui blue left ribbon label noprint' onclick='showcommentOfSignatories(" . $SiData['cfd_id'] . ")'>View Comments</a>
						<br>
						<br>";
				}
				$coreDisable = "";
				if ($SiData['disable']) {
					$coreDisable = "color:blue;font-weight:600";
				}
				// core functions table row
				$percentBadge = "";
				if (!$coreDisable) {
					$this->percent += $SiData['percent'];
					$openModPercent = "";
					if (!$this->hideCol && $this->hide == "") {
						$openModPercent = "onclick='changePercent($SiData[cfd_id])'";
					}
					$percentBadge = "<a class='ui black label' $openModPercent>
										<span style='$per_row'>$SiData[percent] %</span>
									</a>";
				}
				$view = "
				<tr style='width:25%;$coreDisable'>
				<td>
				<div class='segment'>
				$sideTag
				<p style='padding-left:$padding;'>
				$percentBadge
				" . $ar['cf_count'] . " " . $ar['cf_title'] . " 
				</p>
				</div>
				</td>
				<td style='width:25%'>" . nl2br($si['mi_succIn']) . "</td>
				<td style='$this->budgetView'></td>
				<td style='$this->accountableView'> " . $accountableNames . "</td>
				<td style='width:25%;$actualAcc_row'> " . nl2br($SiData['actualAcc']) . "</td>
				<td style='$q_row'>$SiData[Q]</td>
				<td style='$e_row'>$SiData[E]</td>
				<td style='$t_row'>$SiData[T]</td>
				<td>$a</td>
				<td>" . nl2br($SiData['remarks']) . "</td>
				<td style='width:50px' class ='noprint'><button class='ui basic blue button' onclick='fileUplod()' data-id='$SiData[cfd_id]|$this->emp_ID'>Documentations</button></td>
				$col
				</tr>";
				$cTotal++;
			} else {
				$view = "<tr>
				<td style='padding-left:$padding;width:25%'>
					" . $ar['cf_count'] . " " . $ar['cf_title'] . "
				</td>
				<td style='width:25%'>" . nl2br($si['mi_succIn']) . "</td>
				<td style='$this->budgetView'></td>
				<td style='$this->accountableView'>" . $accountableNames . "</td>
				<td colspan='8' style='padding:10px'>
				<button class='ui fluid basic blue button' onclick='accOpenModal($si[mi_id])'>Add Accomplishments</button>
				<br>
				<button class='ui fluid red button' onclick='reassign($si[mi_id])'>Not Applicable</button>
				</td>
				</tr>";
				$count++;
			}
		} else {
			$col = "";
			if (!$this->hideCol) {
				$col = "<td class='noprint' ></td>";
			}
			$view = "<tr>
			<td style='padding-left:$padding;width:25%'>" . $ar['cf_count'] . " " . $ar['cf_title'] . "</td>
			<td style='width:25%'></td>
			<td style='$this->budgetView'></td>
			<td style='$this->accountableView'></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td class='noprint'></td>
			$col
			</tr>";
		}
		$ar = [$view, $count, $a, $cTotal];
		return $ar;
	}
	private function Core_mfoRow($padding, $ar)
	{
		$cTotal = 0;
		$count = 0;
		$totalav = 0;
		$inSi = 0;
		$view = "";
		if (count($ar[1]) > 0) {
			while ($inSi < count($ar[1])) {
				if ($inSi == 0) {
					$row0 = $this->Core_siRow($padding, $ar[0], $ar[1][$inSi]);
					$view .= $row0[0];
					$count += $row0[1];
					$totalav += $row0[2];
					$cTotal += $row0[3];
				} else {
					$row1 = $this->Core_siRow($padding, ['cf_count' => '', 'cf_title' => ''], $ar[1][$inSi]);
					$view .= $row1[0];
					$count += $row1[1];
					$totalav += $row1[2];
					$cTotal += $row1[3];
				}
				$inSi++;
			}
		} else {
			$view .= $this->Core_siRow($padding, $ar[0], "")[0];
		}
		$a = [$view, $count, $totalav, $cTotal];
		return $a;
	}
	public function get_coreView()
	{
		return $this->coreView;
	}
	// methods for support function
	private function supportFunctionTr()
	{
		$this->supportPercent = 0;
		if ($this->get_status('formType') == '1' || $this->get_status('formType') == '5') {
			$sql = "SELECT * FROM `spms_supportfunctions` where `type`=1";
		} elseif ($this->get_status('formType') == '3') {
			$sql = "SELECT * FROM `spms_supportfunctions` where `type`=3";
		} else {
			$sql = "SELECT * FROM `spms_supportfunctions` where `type`=2";
		}
		$sql = mysqli::query($sql);
		$col = "";
		if (!$this->hideCol) {
			$col = "<td class='noprint' ></td>";
		}
		$viewTr = "<tr style='background:#f7f70026'><td colspan='8'><b>Support Function</b></td><td class='noprint'></td>
		<td style='$this->budgetView'></td>
		<td style='$this->accountableView'></td>
		$col</tr>";
		$emp_count = 0;
		$totalAv = 0;
		while ($tr = $sql->fetch_assoc()) {
			$sqlSelect = "SELECT * from spms_supportfunctiondata where parent_id='$tr[id_suppFunc]' and emp_id='$this->emp_ID' and period_id='$this->per_ID'";
			$sqlSelect = mysqli::query($sqlSelect);
			$sqlSelectCount = $sqlSelect->num_rows;
			if ($sqlSelectCount > 0) {
				$fdata = $sqlSelect->fetch_assoc();
				$av = 0;
				$per = $fdata['percent'] / 100;
				$q = 0;
				$e = 0;
				$t = 0;
				// $tableRowColor = '';
				$acc_row = "";
				$q_row = "";
				$e_row = "";
				$t_row = "";
				if ($fdata['supEdit'] != "") {
					$arr = unserialize($fdata['supEdit']);
					for ($arr_index = 0; $arr_index < count($arr); $arr_index++) {
						$edArr = $arr[$arr_index][1];
						for ($edArr_index = 0; $edArr_index < count($edArr); $edArr_index++) {
							if ($edArr[$edArr_index][0] == "Q") {
								$q_row = "color:#ff00f5";
							} else if ($edArr[$edArr_index][0] == "E") {
								$e_row = "color:#ff00f5";
							} else if ($edArr[$edArr_index][0] == "T") {
								$t_row = "color:#ff00f5";
							} else if ($edArr[$edArr_index][0] == "accomplishment") {
								$acc_row = "color:#ff00f5";
							}
						}
					}
				}
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
				$col = "";
				if (!$this->hideCol) {
					$col = "<td style='width:10px;padding:10px;' class='noprint'>
					<i class='inverted circular blue edit icon' onclick='suppFuncEditEmpData($fdata[sfd_id])'></i>
					<br>
					<br>
					<i style='$this->hide' class='inverted circular grey undo icon' onclick='suppFuncRemoveEmpData($fdata[sfd_id])'></i>
					</td>";
				}
				$this->supportPercent += $fdata['percent'];
				$viewTr .= "
				<tr>
				<td style='width:25%'>$tr[mfo] = $fdata[percent] %</td>
				<td style='width:25%'>$tr[suc_in]</td>
				<td style='$this->budgetView'></td>
				<td style='$this->accountableView'></td>
				<td style='$acc_row'>" . nl2br($fdata['accomplishment']) . "</td>
				<td style='$q_row'>$fdata[Q]</td>
				<td style='$e_row'>$fdata[E]</td>
				<td style='$t_row'>$fdata[T]</td>
				<td>$av</td>
				<td>" . nl2br($fdata['remark']) . "</td>
				<td class='noprint'></td>
				$col
				</tr>
				";
				$totalAv += $av;
			} else {
				$emp_count++;
				$viewTr .= "
				<tr>
				<td style='width:25%'>$tr[mfo] = $tr[percent] %</td>
				<td style='width:25%'>$tr[suc_in]</td>
				<td style='width:25%;padding:10px' colspan='10'><button class='ui basic primary fluid button' onclick='addSuppAccomplishement($tr[id_suppFunc])'> Add Accomplishments for your Support Function</button></td>
				</tr>
				";
			}
		}
		$this->supportView = $viewTr;
		$this->support_countEmpty = $emp_count;
		$this->support_totalAv = $totalAv;
	}
	public function get_supportView()
	{
		return $this->supportView;
	}
	// method for stratgic function
	private function strategicTr()
	{
		$this->strtPercent = "N/A";
		$sql = "SELECT * from spms_strategicfuncdata where period_id = '$this->per_ID' and emp_id = '$this->emp_ID'";
		$sql = mysqli::query($sql);
		$countStrat = $sql->num_rows;
		if (!$sql) {
			die($this->error);
		}
		$col = "";
		if (!$this->hideCol) {
			$col = "<td class='noprint' ></td>";
		}
		$tr = "<tr style='background:#f7f70026'><td colspan='8'><b>Strategic Function</b></td><td class='noprint'></td>
		<td style='$this->budgetView'></td>
		<td style='$this->accountableView'></td>
		$col</tr>";
		$totalCount = 0;
		$totalAv = 0;
		while ($row = $sql->fetch_assoc()) {
			// $av = $row['Q']+$row['T'];
			$av = isset($row['average']) && $row['average'] > 0 ? $row['average'] : 0;
			$col = "";
			if (!$this->hideCol) {
				$col = "<td class='noprint' style='width:20px;'>
				<i style='$this->hide' class='ui inverted green circular edit icon'	onclick='strategicOpenModal($row[strategicFunc_id])'></i>
				<br>
				<br>
				<i style='$this->hide' class='ui inverted red circular times icon' 	onclick='strategicDeleteFunc($row[strategicFunc_id])'></i>
				</td>";
			}
			$tr .= "
			<tr>
			<td style='width:25%'>$row[mfo]</td>
			<td style='width:25%'>$row[succ_in]</td>
			<td style='$this->budgetView'></td>
			<td style='$this->accountableView'></td>
			<td style='width:25%'>$row[acc]</td>
			<!--
				<td>$row[Q]</td>
			-->
			<td></td>
			<td></td>
			<!--
				<td>$row[T]</td> 
			-->
			<td></td>
			<td>$row[average]</td>
			<td>$row[remark]</td>
			<td class='noprint'></td>
			$col
			</tr>
			";
			$totalAv += $av;
			$totalCount++;
			if ($row['noStrat'] == "0") {
				$this->strtPercent = 20;
			}
		}
		if ($totalAv > 0) {
			$totalAv = $totalAv / $totalCount;
		} else {
			$totalAv = 0;
		}
		$tr .= "	<tr style='$this->strtBtn'>
		<td colspan='10' style='background:#ffff0047'>
		<button class='ui primary fluid button' onclick='finishperformanceReview(\"Done\",\"\",\"\")'>Next <i class='ui angle double right icon'></i></button>
		</td>
		</tr>";
		if ($totalAv > 0) {
			$totalAv = $totalAv * 0.20;
			# format only two decimal places
			$totalAv = number_format($totalAv, 2);
			# prevent rounding off value
			// $totalAv = intval(($totalAv * 100)) / 100;
		} else {
			$totalAv = 0;
		}
		// $totalAv = $totalAv*0.20;
		// $totalAv = $totalAv;
		$this->strategicView = $tr;
		$this->strategic_totalAv = $totalAv;
		$this->strategic_count = $countStrat;
	}
	public function get_strategicView()
	{
		return $this->strategicView;
	}
	public function form_strategicView()
	{
		$not_applicable_button = "<button hidden class='ui fluid button secondary' onclick='noStrategicFunc()'>Not Applicable</button>";
		$not_applicable_button = ""; //disabled 


		$view = "
		<br>
		<br>
		<div class='ui three column grid' style='background:white;width:1000px;padding-top:100px;margin:auto;border-radius: 50px 20px;'>
		<div class='column'>
		</div>
		<div class='column'>
		<div class='ui fluid'>
		<h1 >Strategic Function</h1>
			<form class='ui form' onsubmit='return saveStrategicFunc()'>
				<div class='field'>
					<label>MFO/PAP:</label>
					<textarea rows='1' id='mfo' required placeholder='...'></textarea>
				</div>
				<div class='field'>
					<label>Success Indicator:</label>
					<textarea rows='2' id='suc_in' placeholder='Enter success indicator here...'></textarea>
				</div>
				<div class='field'>
					<label>Actual Accomplishment:</label>	
					<textarea rows='2' id='acc' placeholder='Enter the actual accomplishment here...'></textarea>
					</div>
<!--		
				<div class='field'>
					<label>Quality</label>
					<select id='quality' class='ui fluid dropdown'>
						<option value='1'>1</option>
						<option value='2'>2</option>
						<option value='3'>3</option>
						<option value='4'>4</option>
						<option value='5'>5</option>
					</select>
				</div>
				<div class='field'>
					<label>Timeliness</label>
					<select id='time' class='ui fluid dropdown'>
						<option value='1'>1</option>
						<option value='2'>2</option>
						<option value='3'>3</option>
						<option value='4'>4</option>
						<option value='5'>5</option>
					</select>
				</div>
-->
				<div class='field'>
					<label>Final Rating:</label>
					<input name='my_field' pattern='^\d*(\.\d{0,3})?$' id='stratAverage' placeholder='1-5'>
				</div>
				<div class='field'>
					<label>Remark</label>
					<textarea rows='2' id='remark'></textarea>
				</div>
				<input type='submit' class='ui fluid button' value='Save' placeholder='Enter remarks here...'>
			</form>
				$not_applicable_button
			</div>
		</div>
		<div class='column'>
		</div>
		</div>
		";
		return $view;
	}
	public function hideNextBtn()
	{
		$this->strtBtn = 'display:none';
		$this->load();
	}
	// comment and reccomendation
	private function comment()
	{
		$commentsql = "SELECT * from spms_commentrec where period_id='$this->per_ID' and emp_id='$this->emp_ID'";
		$commentsql = mysqli::query($commentsql);
		$countRow = $commentsql->num_rows;
		$commentsql = $commentsql->fetch_assoc();
		$comment = "";
		if (isset($commentsql['comment'])) {
			$comment = $commentsql['comment'];
		}
		$view = "
		<form class='ui form' style='width:40%;margin:auto;padding:20px' onsubmit='return commentRecFunc()' >
		<div class='field'>
		<label>Comments and Reccomendation (<i style='color:red'>Note:</i> This box is accessible only by your Immediate Suppervisor)</label>
		<textarea id='comRec' disabled>$comment</textarea>
		</div>
		<button class='ui fluid primary button' type='submit'>Go to Final Stage</button>
		</form>
		";
		$this->commentData = $commentsql;
		$this->comment = $view;
		$this->commentCount = $countRow;
	}
	public function form_comment()
	{
		return $this->comment;
	}
	public function get_comment($i)
	{
		$dat = $this->commentData;
		return $dat[$i];
	}
	// strat
	private function head_of_agency()
	{
		$sql = "SELECT * from spms_performancereviewstatus where period_id='$this->per_ID' and employees_id='$this->emp_ID'";
		$result = mysqli::query($sql);
		$row = $result->fetch_assoc();

		$department_id = $_SESSION["emp_info"]["department_id"];

		# if vice mayor & sp head of agency = vice mayor
		if ($department_id == 16) {
			$lgu_head = "HENRY E. CARREON, JR.";
		} else {
			$lgu_head = "JOHN T. RAYMOND, JR.";
		}

		return isset($row["HeadAgency"]) ? "value='$row[HeadAgency]'" : "value='$lgu_head'";
		// return json_encode($this);
	}

	private	function empData($id, $mayor)
	{
		$emps = "";
		if ($id == '') {
			$id = 0;
		}
		$empSql = "SELECT * from `employees`";
		$empSql = mysqli::query($empSql);
		while ($getData = $empSql->fetch_assoc()) {
			$val = $getData["employees_id"];

			if ($mayor) {
				$val  = $getData['firstName'] . " " . $getData["middleName"] . " " . $getData["lastName"] . $getData["extName"] ? ", " . $getData["extName"] : "";
				$val = mb_convert_case($val, MB_CASE_UPPER);
			}

			if ($getData['employees_id'] == $id) {
				$emps .= "<option value='$val' selected>$getData[firstName] $getData[middleName] $getData[lastName] $getData[extName]</option>";
			} else {
				$emps .= "<option value='$val'>$getData[firstName] $getData[middleName] $getData[lastName] $getData[extName]</option>";
			}
		}
		return $emps;
	}
	private function signatories()
	{
		$fileStatus = $this->fileStatus;
		// echo json_encode($sql);
		$option  = ['', '', '', '', ''];
		$fieldDisabled = "";
		$agencyRadioBtn = "

		<div class='ui  form' style='width:210px;padding:20px;margin:auto;border-radius:10px 10px 10px 10px;box-shadow:1px 1px 10px black;'>
			<div class='grouped fields'>
			    <div class='field'>
			      <div class='ui slider checkbox'>
			        <input type='radio' name='agencies' value='LGU' checked='checked' onchange='agencies()'>
			        <label>LGU</label>
			      </div>
			    </div>
			    <div class='field'>
			      <div class='ui slider checkbox'>
			        <input type='radio' name='agencies' value='NGA' onchange='agencies()'>
			        <label>Detailed to NGA</label>
			      </div>
			    </div>
		    </div>
	    </div>
		";
		$agencyOp = $this->empData($fileStatus['HeadAgency'], 1);
		if ($fileStatus['formType'] != "") {
			$option[$fileStatus['formType']] = 'selected';
			$fieldDisabled = "disabled";
			$agencyRadioBtn = '';
			$agencyOp = "<option value='$fileStatus[HeadAgency]' selected>$fileStatus[HeadAgency]</option>";
		}


		$json = json_encode($this);

		$form_type = isset($this->fileStatus["formType"]) ? $this->fileStatus["formType"] : '';


		$view = "
		<script>

			$('.ui.dropdown').dropdown({
				forceSelection: false,
				fullTextSearch: true,
				clearable: true,
			  });
			
			  $('.ui.dropdown.headAgency').dropdown({
				forceSelection: false,
				fullTextSearch: true,
				clearable: true,
				allowAdditions: true
			  });

		</script>
		<div style='background:white;width:900px;padding-top:100px;margin:auto;border-radius: 50px 20px;'>
		<div class='ui icon message' style='width:50%;padding:20px;margin:auto'>
		<i class='open book icon'></i>
		<div class='content'>
		<div class='header'>
		Agreement
		</div>
		<p>By filling Up this form from Start to Finish means that I,
		commit to deliver and agree to be rated on the attainment of
		the following targets in accordance with the indicated measures</p>
		</div>
		</div>
		<br>
		<br>

		$agencyRadioBtn

	    <br>
	    <br>
		<form class='ui form' style='width:50%;padding:20px;margin:auto;' onsubmit='return signatoriesFunc()'>
		<div class='field'>
		<label>Form Type</label>
		<select id='formType' required onchange='reviewFormType()' $fieldDisabled>
		<option value='1' $option[1]>IPCR</option>
		<option value='2' $option[2]>SPCR</option>
		<option value='3' $option[3]>DPCR</option>
		<option value='4' $option[4]>DIVISION Performance Commitment Review</option>
		</select>
		</div>
		<div class='field' >
		<label>Immediate Supervisor</label>
		<select class='ui fluid search dropdown' id='immediateSup' >
		<option value=''>Select your Supervisor</option>
		" . $this->empData($fileStatus['ImmediateSup'], 0) . "
		</select>
		</div>
		<div class='field'>
		<label>Department Head</label>
		<select class='ui fluid search dropdown' id='departmentHead'>
		<option value=''>Select your Supervisor</option>
		" . $this->empData($fileStatus['DepartmentHead'], 0) . "
		</select>
		</div>

		<div class='field'>
		<label>Head of Agency</label>
		<input type='text' id='headAgency' placeholder='Head of Agency' " . $this->head_of_agency() . ">
		</div>
		<button type='submit' class='ui fluid primary button'>Next <i class='ui angle double right icon'></i></button>
		</form>
		<br>
		<br>
		<br>
		</div>
		";

		#if national agency
		if ($form_type == 5) {
			$view = "
		<div style='background:white;width:900px;padding-top:100px;margin:auto;border-radius: 50px 20px;'>
		<div class='ui icon message' style='width:50%;padding:20px;margin:auto'>
		<i class='open book icon'></i>
		<div class='content'>
		<div class='header'>
		Agreement
		</div>
		<p>By filling Up this form from Start to Finish means that I,
		commit to deliver and agree to be rated on the attainment of
		the following targets in accordance with the indicated measures</p>
		</div>
		</div>
		<br>
		<br>

		$agencyRadioBtn

	    <br>
	    <br>
		<form class='ui form' style='width:50%;padding:20px;margin:auto;' onsubmit='return signatoriesFunc()'>
		<div class='field'>
		<label>Form Type</label>
		<select id='formType' required onchange='reviewFormType()' $fieldDisabled>
		<option value='5' selected>IPCR</option>
		</select>
		</div>
		<div class='field' >
		<label>Immediate Supervisor</label>
		<input class='ui fluid input' type='text' id='immediateSup' value='" . $fileStatus['ImmediateSup'] . "'>
		</div>
		<div class='field'>
		<label>Department Head</label>
		<input class='ui fluid input' type='text' id='departmentHead' value='" . $fileStatus['DepartmentHead'] . "'>
		</div>

		<div class='field'>
		<label>Head of Agency</label>
		<input type='text' id='headAgency' placeholder='Head of Agency' " . $this->head_of_agency() . ">
		</div>
		<button type='submit' class='ui fluid primary button'>Next <i class='ui angle double right icon'></i></button>
		</form>
		<br>
		<br>
		<br>
		</div>
		";
		}

		$this->signa_form = $view;
	}
	public function form_signatories()
	{
		return $this->signa_form;
	}
	// misc
	public function tableHeader()
	{
		$empInfo = $this->EmpInfo;
		$col = "";
		if (!$this->hideCol) {
			$col = "<td class='noprint'></td>";
		}
		$formType = $this->fileStatus;

		$ipcr = "<tr style='background:#0080003d'>
		<td style='width:28%'>
		<p style='font-size:10px'>
		Reviewed By:
		</p>
		<p style='text-align:center'>
		<u>" . $this->get_fullname($this->get_status('ImmediateSup')) . "</u>
		<br>
		<span style='font-size:10px'>
		Immediate Superior
		</span>
		</p>
		</td>
		<td style='width:28%'>
		<p style='font-size:10px'>
		Noted By:
		</p>
		<p style='text-align:center'>
		<u>" . $this->get_fullname($this->get_status('DepartmentHead')) . "</u>
		<br>
		<span style='font-size:10px'>
		Department Head
		</span>
		</p>
		</td>
		<td style='width:28%'>
		<p style='font-size:10px'>
		Approved By:
		</p>
		<p style='text-align:center'>
		<u>" . $this->get_status('HeadAgency') . "</u>
		<br>
		<span style='font-size:10px'>
		Head of Agency
		</span>
		</p>
		</td>
		<td colspan='5'>
		<p style='font-size:10px'>
		Date:
		</p>
		<p style='text-align:center'>
		<u>" . $this->get_status('dateAccomplished') . "</u>
		<br>
		<span style='font-size:10px'>

		</span>
		</p>
		</td>
		</tr>";

		$ipcrNGA = "<tr style='background:#0080003d'>
		<td style='width:28%'>
		<p style='font-size:10px'>
		Reviewed By:
		</p>
		<p style='text-align:center'>
		<u>" . $this->get_status('ImmediateSup') . "</u>
		<br>
		<span style='font-size:10px'>
		Immediate Superior
		</span>
		</p>
		</td>
		<td style='width:28%'>
		<p style='font-size:10px'>
		Noted By:
		</p>
		<p style='text-align:center'>
		<u>" . $this->get_status('DepartmentHead') . "</u>
		<br>
		<span style='font-size:10px'>
		Department Head
		</span>
		</p>
		</td>
		<td style='width:28%'>
		<p style='font-size:10px'>
		Approved By:
		</p>
		<p style='text-align:center'>
		<u>" . $this->get_status('HeadAgency') . "</u>
		<br>
		<span style='font-size:10px'>
		Head of Agency
		</span>
		</p>
		</td>
		<td colspan='5'>
		<p style='font-size:10px'>
		Date:
		</p>
		<p style='text-align:center'>
		<u>" . $this->get_status('dateAccomplished') . "</u>
		<br>
		<span style='font-size:10px'>

		</span>
		</p>
		</td>
		</tr>";


		$spcr = "
		<tr style='background:#0080003d'>
		<td style='width:28%'>
		<p style='font-size:10px'>
		Reviewed By:
		</p>
		<p style='text-align:center'>
		<u>" . $this->get_fullname($this->get_status('DepartmentHead')) . "</u>
		<br>
		<span style='font-size:10px'>
		Immediate Superior/Dept. Head
		</span>
		</p>
		</td>
		<td style='width:28%'>
		<p style='font-size:10px'>
		Date:
		</p>
		<p style='text-align:center'>
		<br>
		<span style='font-size:10px'>
		</span>
		</p>
		</td>
		<td style='width:28%'>
		<p style='font-size:10px'>
		Approved By:
		</p>
		<p style='text-align:center'>
		<u>" . $this->get_status('HeadAgency') . "</u>
		<br>
		<span style='font-size:10px'>
		Head of Agency
		</span>
		</p>
		</td>
		<td colspan='5'>
		<p style='font-size:10px'>
		Date:
		</p>
		<p style='text-align:center'>
		<u>" . $this->get_status('dateAccomplished') . "</u>
		<br>
		<span style='font-size:10px'>
		</span>
		</p>
		</td>
		</tr>
		";
		$dpcr = "
		<tr style='background:#0080003d'>
		<td style='width:28%'>
		<p style='font-size:10px'>
		Prepared by:
		</p>
		<p style='text-align:center'>
		<u>" . $this->get_fullname($this->emp_ID) . "</u>
		<br>
		<span style='font-size:10px'>
		Department Head
		</span>
		</p>
		</td>
		<td style='width:28%'>
		<p style='font-size:10px'>
		Date:
		</p>
		<p style='text-align:center'>
		<br>
		<span style='font-size:10px'>
		</span>
		</p>
		</td>
		<td style='width:28%'>
		<p style='font-size:10px'>
		Approved By:
		</p>
		<p style='text-align:center'>
		<u>" . $this->get_status('HeadAgency') . "</u>
		<br>
		<span style='font-size:10px'>
		Head of Agency
		</span>
		</p>
		</td>
		<td colspan='5'>
		<p style='font-size:10px'>
		Date:
		</p>
		<p style='text-align:center'>
		<u>" . $this->get_status('dateAccomplished') . "</u>
		<br>
		<span style='font-size:10px'>
		</span>
		</p>
		</td>
		</tr>
		";
		$headerType = $ipcr;
		$headerTitle = "INDIVIDUAL PERFORMANCE COMMITMENT AND REVIEW (IPCR)";
		if ($formType['formType'] == 4) {
			$headerType = $spcr;
			$headerTitle = "Division PERFORMANCE COMMITMENT AND REVIEW";
		} elseif ($formType['formType'] == 2) {
			$headerType = $spcr;
			$headerTitle = "SECTION PERFORMANCE COMMITMENT AND REVIEW (SPCR)";
		} else if ($formType['formType'] == 3) {
			$headerType = $dpcr;
			$headerTitle = "DEPARTMENT PERFORMANCE COMMITMENT AND REVIEW (DPCR)";
		} else if ($formType['formType'] == 5) {
			$headerType = $ipcrNGA;
			$headerTitle = "INDIVIDUAL PERFORMANCE COMMITMENT AND REVIEW (IPCR)";
		}
		$period = $this->period;
		$view = "
		<table border='1px' style='border-collapse:collapse;width:98%;margin:auto;'>
		<tr>
		<td colspan='8' style='padding:10px'>
		<p style='text-align:center'><b>$headerTitle</b></p>
		<p>
		I, <b>$empInfo[firstName] $empInfo[middleName] $empInfo[lastName] $empInfo[extName]</b>, $empInfo[position] of the <b>$empInfo[department]</b>
		commit to deliver and agree to be rated on the attainment of the following targets in accordance with the indicated measures for the
		period $period[month_mfo] $period[year_mfo]
		</p>
		<p style='width:25%;text-align:center;float:right'>
		<b><u>$empInfo[firstName] $empInfo[middleName] $empInfo[lastName] $empInfo[extName]</u></b><br>
		Ratee
		</p>
		</td>
		</tr>
		$headerType
		</table>
		";
		return $view;
	}
	public function tableFooter()
	{
		$col = "";
		$commentTd = "";
		$adjectival = "POOR";
		$fileStatus = $this->fileStatus;
		$overallAv = 0;

		$overallAv += $this->strategic_totalAv;
		$overallAv += $this->core_totalAv;
		$overallAv += $this->support_totalAv;


		if ($overallAv <= 5 && $overallAv > 4) {
			$adjectival = "OUTSTANDING";
		} elseif ($overallAv <= 4 && $overallAv > 3) {
			$adjectival = "Very Satisfactory";
		} elseif ($overallAv <= 3 && $overallAv > 2) {
			$adjectival = "Satisfactory";
		} elseif ($overallAv <= 2 && $overallAv > 1) {
			$adjectival = "Unsatisfactory";
		}
		if (!$this->hideCol) {
			$col = "<td class='noprint'></td>";
			$commentTd = "<td class='noprint' style='padding:10px'><i style='$this->hide' class='ui blue inverted edit circular icon' onclick='showPr(\"coreFunction\",\"comment\")'></i></td>
			";
		}

		if ($fileStatus['formType'] == 5) {
			$ImmediateSup = $this->get_status('ImmediateSup');
			$DepartmentHead = $this->get_status('DepartmentHead');
		} else {
			$ImmediateSup = $this->get_fullname($this->get_status('ImmediateSup'));
			$DepartmentHead = $this->get_fullname($this->get_status('DepartmentHead'));
		}

		# Temporarily empties strategic rating, final numerical rating, 
		# and final adjectival rating for the period of Jan-June 2022 ONLY 
		# $this->period["year_mfo"] != "2022"?...
		$cut_year = "2222";
		$strategic_total = $this->period["year_mfo"] != $cut_year ? $this->strategic_totalAv : " ";
		$final_numerical_rating = $this->period["year_mfo"] != $cut_year ? $overallAv : "";
		$final_adjectival_rating = $this->period["year_mfo"] != $cut_year ? $adjectival : "";

		// <td>Formula:(Total of all average ratings / no. of entries)x20%</td>
		$view = "
		<table border='1px' style='border-collapse:collapse;width:98%;margin:auto'>
		<tbody>
		<tr style='font-size:12px;background:#f7f70026'>
		<td colspan='2'><p style='font-size:9px'>SUMMARYOF RATING</p></td>
		<td><p style='font-size:9px;text-align:center'>TOTAL</p></td>
		<td colspan='3'><p style='font-size:9px'>Final Numerical Rating</p></td>
		<td colspan='2'><p style='font-size:9px'>Final Adjectival Rating</p></td>
		<td class='noprint'></td>
		$col
		</tr>
		<tr>
		<td>Strategic Objectives</td>
		<td>Total Weight Allocation:" . $this->get_strtPercent() . "</td>
		<td><center><b>" . $strategic_total . "</b></center></td>
		<td colspan='3' rowspan='3'>
		<center><b> " . $final_numerical_rating . " </b></center>
		</td>
		<td colspan='2' rowspan='3'><center><b>" . $final_adjectival_rating . "</b></center></td>
		<td class='noprint'></td>
		$col
		</tr>
		<tr>
		<td>Core Functions</td>
		<td>Total Weight Allocation:" . $this->get_percent() . "%</td>
		<td><center><b>$this->core_totalAv</b></center></td>
		<td class='noprint'></td>
		$col
		</tr>
		<tr>
		<td>Support Functions</td>
		<td>Total Weight Allocation:" . $this->get_supportPercent() . "%</td>
		<td><center><b>" . $this->support_totalAv . "</b></center></td>
		<td class='noprint'></td>
		$col
		</tr>
		<tr>
		<td colspan='8' style='font-size:12px'>
		<b>Comments and Recommendation For Development Purpose:</b>
		" . $this->get_comment('comment') . "
		<br>
		<br>
		<br>
		</td>
		<td class='noprint'></td>
		$commentTd
		</tr>
		</tbody>
		</table>
		<table border='1px' style='border-collapse:collapse;width:98%;margin:auto;'>
		<thead style='background:#0080003d'>
		<tr style='font-size:10px'>
		<td >Discussed: Date:</td>
		<td>Assessed by: Date:</td>
		<td></td>
		<td>Reviewed: Date:</td>
		<td>Final Rating by:</td>
		<td>Date:</td>
		<td class='noprint'></td>
		$col
		</tr>
		<tr>
		<td style='text-align:center;width:16%; vertical-align:bottom;'>
		<span style='font-size:11px'><b>" . $this->get_emp("firstName") . " " . $this->get_emp("middleName") . " " . $this->get_emp("lastName") . " " . $this->get_emp("extName") . "</b></span>
		</td>
		<td style='text-align:center;width:16%'>
		<p style='font-size:9px; margin-bottom: 25px;'>	I certified that I discussed my assessment of the performance with the employee:</p>
		<p style='font-size:11px'><b>" . $ImmediateSup . "</b></p>
		</td>
		<td style='text-align:center;width:16%'>
			<p style='font-size:9px; margin-bottom: 25px;'>I certified that I discussed with the employee how they are rated:</p>
			<p style='font-size:11px;'><b>" . $DepartmentHead . "</b></p>
		</td>
		<td style='text-align:center;width:16%'>
		<p style='font-size:9px;'>
		<br>
		<br>
		<br>
		(all PMT member will sign)
		</p>
		</td>
		<td style='text-align:center;width:16%;vertical-align:bottom;'>
			<span style='font-size:11px'><b>" . $this->get_status('HeadAgency') . "</b></span>
		</td>
		<td style='text-align:center;width:9.2%'>
		</td>
		<td class='noprint'></td>
		$col
		</tr>
		<tr style='font-size:9px'>
		<td style='text-align:center;width:16%'>Ratee</td>
		<td style='text-align:center;width:16%'>Supervisor</td>
		<td style='text-align:center;width:16%'>Department Head</td>
		<td style='text-align:center;width:16%'></td>
		<td style='text-align:center;width:16%'>Head of Agency</td>
		<td style='text-align:center;width:9.2%'></td>
		<td class='noprint'></td>
		$col
		</tr>
		</thead>
		</table>
		";
		return $view;
	}
	public function get_approveBTN()
	{
		$col = "";
		$view = "";
		if (!$this->hideCol) {
			$accountId = $_SESSION['emp_id'];
			$fileStatus = $this->fileStatus;
			if ($accountId == $fileStatus['ImmediateSup']) {
				if ($fileStatus['ImmediateSup'] == $fileStatus['DepartmentHead']) {
					$view = "<button class='ui teal massive fluid button noprint' style='width:95%' onclick='commentRecModalShow(" . $this->period['mfoperiod_id'] . ",$this->emp_ID)'>Certify Results</button>";
					// }elseif($fileStatus['formType']==1){
					// $view = "<button class='ui teal massive fluid button noprint' style='width:95%' onclick='commentRecModalShow(".$this->period['mfoperiod_id'].",$this->emp_ID)'>Approve Results</button>";
				} else {
					$view = "<button class='ui teal massive fluid button noprint' style='width:95%' onclick='commentRecModalShow(" . $this->period['mfoperiod_id'] . ",$this->emp_ID)'>Approve Results</button>";
					// $view ="<button alert='alert(contact toto)'>Something is Wrong</button>";
					// $view = "<button class='ui teal massive fluid button noprint' style='width:95%' onclick='approval(".$this->get_status('performanceReviewStatus_id').",$this->emp_ID)'>Approve</button>";
				}
			} elseif ($accountId == $fileStatus['DepartmentHead']) {
				// if($fileStatus['formType']==2){
				$view = "<button class='ui teal massive fluid button noprint' style='width:95%' onclick='commentRecModalShow(" . $this->period['mfoperiod_id'] . ",$this->emp_ID)'>Certify Results</button>";
				// }else{
				// 	$view = "<button class='ui teal massive fluid button noprint' style='width:95%' onclick='approval(".$this->get_status('performanceReviewStatus_id').",$this->emp_ID)'>Approve</button>";
				// }
			} elseif ($accountId == $fileStatus['PMT']) {
				if ($fileStatus['formType'] == 3) {
					$view = "<button class='ui teal massive fluid button noprint' style='width:95%' onclick='commentRecModalShow(" . $this->period['mfoperiod_id'] . ",$this->emp_ID)'>Submit Results</button>";
				} else {
					$view = "<button class='ui teal massive fluid button noprint' style='width:95%' onclick='approval(" . $this->get_status('performanceReviewStatus_id') . ",$this->emp_ID)'>Approve</button>";
				}
			} elseif ($accountId == $fileStatus['employees_id']) {
				$total = 0;
				$strat = 0;
				if ($this->strtPercent == 20) {
					$strat = 20;
				}
				$total = $this->percent + $this->supportPercent + $strat;
				$btnStatus = "disabled";
				$btnColor = "red";
				$btnMsg = "Your Percentage Allocation is Lacking:(" . $total . "/100)";
				if ($total == 100) {
					$btnStatus = "onclick='submitPerformance()'";
					$btnColor = "teal";
					$btnMsg = "Submit";
				} elseif ($total > 100) {
					$btnMsg = "Your Percentage Allocation is too much:(" . $total . "/100)";
				}
				$view = "<center><button class='ui $btnColor massive fluid button noprint' style='width:95%' $btnStatus>$btnMsg</button></center>";
			}
		}
		return $view;
	}
	// Rating Scale
	function RatingScaleTable()
	{
		$view = "
		<table border='1px' style='border-collapse:collapse;width:98%;margin:auto'>
		<thead style='background:#00c4ff36;font-size:14px'>
		<tr>
		<th colspan='5' style='font-size:18px'>
		Rating Scale Matrix
		<br>" . $this->get_emp('department') . "
		<br>For " . $this->get_period('month_mfo') . " " . $this->get_period('year_mfo') . "
		</th>
		<tr>
		<tr>
		<th rowspan='2' style='padding:20px'>MFO / PAP</th>
		<th rowspan='2'>Success Indicator</th>
		<th colspan='4' style='width:40px'>Rating Matrix</th>
		</tr>
		<tr style='font-size:12px'>
		<th>Q</th>
		<th>E</th>
		<th>T</th>
		</tr>
		</thead>
		<tbody style='font-size:14px'>
		<tr style='background:#f7f70026'><td colspan='10'><b>Core Function</b></td></tr>
		" . $this->RatingScaleRow() . "
		</tbody>
		</table>
		";
		return $view;
	}
	function RatingScaleRow()
	{
		$arr = $this->coreAr();
		$view = "";
		$count0 = count($arr);
		$in0 = 0;
		while ($in0 < $count0) {
			$view .= $this->matrixTable(0, $arr[$in0]);
			$a1 = $arr[$in0][2];
			$in1 = 0;
			while ($in1 < count($a1)) {
				$view .= $this->matrixTable(20, $a1[$in1]);
				$a2 = $a1[$in1][2];
				$in2 = 0;
				while ($in2 < count($a2)) {
					$view .= $this->matrixTable(40, $a2[$in2]);
					$a3 = $a2[$in2][2];
					$in3 = 0;
					while ($in3 < count($a3)) {
						$view .= $this->matrixTable(60, $a3[$in3]);
						$in3++;
					}
					$in2++;
				}
				$in1++;
			}
			$in0++;
		}
		return $view;
	}
	function RatingMat($a)
	{
		$view = '';
		$a = unserialize($a);
		$count = 5;
		while ($count >= 1) {
			if ($a[$count] != "") {
				$view .= $count . " - " . $a[$count] . "<br>";
			}
			$count--;
		}
		return $view;
	}
	function matrixtr($padding, $ar, $si)
	{
		if ($si != "") {
			$view = "<tr>
			<td style='padding-left:$padding;width:25%'>" . $ar['cf_count'] . " " . $ar['cf_title'] . "</td>
			<td style='width:25%'>" . nl2br($si['mi_succIn']) . "</td>
			<td style='width:15%'>" . $this->RatingMat($si['mi_quality']) . "</td>
			<td style='width:15%'>" . $this->RatingMat($si['mi_eff']) . "</td>
			<td style='width:15%'>" . $this->RatingMat($si['mi_time']) . "</td>
			</tr>";
		} else {
			$view = "<tr>
			<td style='padding-left:$padding;width:25%'>" . $ar['cf_count'] . " " . $ar['cf_title'] . "</td>
			<td style='width:25%'></td>
			<td></td>
			<td></td>
			<td></td>
			</tr>";
		}
		return $view;
	}
	function matrixTable($padding, $ar)
	{
		$inSi = 0;
		$view = "";
		if (count($ar[1]) > 0) {
			while ($inSi < count($ar[1])) {
				if ($inSi == 0) {
					$view .= $this->matrixtr($padding, $ar[0], $ar[1][$inSi]);
				} else {
					$view .= $this->matrixtr($padding, ['cf_count' => '', 'cf_title' => ''], $ar[1][$inSi]);
				}
				$inSi++;
			}
		} else {
			$view .= $this->matrixtr($padding, $ar[0], "");
		}
		return $view;
	}
}
class year
{
	private $show;
	function __construct()
	{
		$dnow = date('Y') + 1;
		$dpast = date('Y') - 50;
		$view = "";
		while ($dnow >= $dpast) {
			$view .= "<option value='$dnow'>$dnow</option>";
			$dnow--;
		}
		$this->show = $view;
	}
	function get_year()
	{
		return $this->show;
	}
}
class step
{
	private	$signa;
	private	$core;
	private	$strat;
	private	$support;
	private	$comment;
	private	$final;
	function __construct()
	{
		$this->signa = "disabled";
		$this->core = "disabled";
		$this->strat = "disabled";
		$this->support = "disabled";
		$this->comment = "disabled";
		$this->final = "disabled";
	}
	public function _set($i)
	{
		if ($i == "signatories") {
			$this->signa = "active";
		} else if ($i == "core") {
			$this->signa = "completed";
			$this->core = "active";
		} else if ($i == "support") {
			$this->signa = "completed";
			$this->core = "completed";
			$this->support = "active";
		} else if ($i == "strategic") {
			$this->signa = "completed";
			$this->core = "completed";
			$this->support = "completed";
			$this->strat = "active";
		} else if ($i == "comment") {
			$this->signa = "completed";
			$this->core = "completed";
			$this->support = "completed";
			$this->strat = "completed";
			$this->comment = "active";
		} else if ($i == "final") {
			$this->signa = "completed";
			$this->core = "completed";
			$this->support = "completed";
			$this->strat = "completed";
			$this->comment = "completed";
			$this->final = "active";
		}
	}
	public function _get()
	{
		$view = "
		<div class='ui mini six steps noprint'>
		<div class='$this->signa step' onclick='showPr(\"coreFunction\",\"signatories\")'>
		<i class='list alternate icon'></i>
		<div class='content'>
		<div class='title'>Signatories	</div>
		<div class='description'>Fill in the Signatories assigned for you.</div>
		</div>
		</div>
		<div class='$this->core step' onclick='showPr(\"coreFunction\",\"core\")'>
		<i class='list alternate icon'></i>
		<div class='content'>
		<div class='title'>Core Function	</div>
		<div class='description'>Fill in your Accomplishments Reports</div>
		</div>
		</div>
		<div class='$this->support step' onclick='showPr(\"coreFunction\",\"support\")'>
		<i class='band aid icon'></i>
		<div class='content'>
		<div class='title'>Support Functions</div>
		<div class='description'>Input your Support Function Accomplishments</div>
		</div>
		</div>
		<div class='$this->strat step' onclick='showPr(\"coreFunction\",\"strategic\")'>
		<i class='id badge icon'></i>
		<div class='content'>
		<div class='title'>Strategic Function</div>
		<div class='description'>Add your Strategic Function</div>
		</div>
		</div>
		<div class='$this->comment step' onclick='showPr(\"coreFunction\",\"comment\")'>
		<i class='id badge icon'></i>
		<div class='content'>
		<div class='title'>Comment And Recommendation</div>
		<div class='description'>For improvement purposes</div>
		</div>
		</div>
		<div class='$this->final step' onclick='showPr(\"coreFunction\",\"final\")'>
		<i class='clipboard check icon'></i>
		<div class='content'>
		<div class='title'>Final Stage</div>
		<div class='description'>Your Performance and Commitment Review for this period is Ready for Submission</div>
		</div>
		</div>
		</div>
		";
		return $view;
	}
	public function set_disable($var)
	{
		if ($var) {
			$this->signa = "disabled";
			$this->core = "disabled";
			$this->strat = "disabled";
			$this->support = "disabled";
			$this->comment = "disabled";
			$this->final = "disabled";
		}
	}
}
class table
{
	private $trHead;
	private $trBody;
	private $trFoot;
	private $hideCol;
	private $budgetView;
	private $accountableView;
	private $dat;
	function __construct($h)
	{
		$this->hideCol = $h;
	}
	public function formType($type)
	{
		if ($type == 2) {
			$this->budgetView	 = 'display:none';
		} else if ($type == 1) {
			$this->budgetView = 'display:none';
			$this->accountableView = 'display:none';
		}
	}
	public function set_body($data)
	{
		$this->trBody .= $data;
		$this->load();
	}
	public function set_head($data)
	{
		$this->trHead = $data;
		$this->load();
	}
	public function set_foot($data)
	{
		$this->trFoot = $data;
		$this->load();
	}
	private function load()
	{
		$col = "";
		if (!$this->hideCol) {
			$col = "<th rowspan='2' class='noprint' ><i class='ui cog icon'></th>";
		}
		$table = "
		$this->trHead
		<table border='1px' style='border-collapse:collapse;width:98%;margin:auto;'>
		<tr style='background:#00c4ff36;font-size:14px'>
		<th rowspan='2' style='padding:20px'>MFO / PAP</th>
		<th rowspan='2'>Success Indicator</th>
		<th rowspan='2' style='$this->budgetView'>Alloted Budget<br>for 2021 (whole<br>year)</th>
		<th  rowspan='2' style='$this->accountableView'>Individual/s or <br> Division Accountable</th>
		<th rowspan='2'>Actual Accomplishments</th>
		<th colspan='4' style='width:40px'>Rating Matrix</th>
		<th rowspan='2'>Remarks</th>
		<th rowspan='2' class='noprint' ><i class='ui blue folder icon'></i></th>
		$col
		</tr>
		<tr style='font-size:12px;background:#00c4ff36;font-size:14px'>
		<th>Q</th>
		<th>E</th>
		<th>T</th>
		<th>A</th>
		</tr>
		<tbody style='font-size:14px'>
		$this->trBody
		</tbody>
		</table>
		$this->trFoot
		";
		$this->dat = $table;
	}
	public function _get()
	{
		return $this->dat;
	}
}
class employees extends mysqli
{
	function __construct()
	{
		$host = "localhost";
		$usernameDb = "";
		// $password = "teamhrmo2019";
		$password = "teamhrmo2019";
		$database = "ihris";
		parent::__construct($host, $usernameDb, $password, $database);
		parent::set_charset("utf8");
	}
	public function get_all()
	{
		$sql  = "SELECT * from employees";
		$sql = parent::query($sql);
		$view = "<option value=''>Search Employee name</option>";
		while ($data = $sql->fetch_assoc()) {
			$view .= "<option value='$data[employees_id]'>$data[firstName] $data[middleName] $data[lastName] $data[extName]</option>";
		}
		return $view;
	}
	public function get_department($dep)
	{
		$sql  = "SELECT * from employees where	department_id='$dep'";
		$sql = parent::query($sql);
		$view = "<option value=''>Search Employee name</option>";
		while ($data = $sql->fetch_assoc()) {
			$view .= "<option value='$data[employees_id]'>$data[firstName] $data[middleName] $data[lastName] $data[extName]</option>";
		}
		return $view;
	}
}
function Authorization_Error()
{
	$view = "
	<div style='margin:auto;width:400px;padding-top:50px'>
	<h1 class='ui icon header'>
	<i class='exclamation triangle icon'></i>
	<div class='content'>
	You are not Authorize
	<div class='sub header'>You enter a page you are not permitted to enter. Ask for admin permission to enter</div>
	</div>
	</h1>
	</div>";
	return $view;
}
