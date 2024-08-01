<?php
class NameFormatter
{
	private $firstName;
	private $lastName;
	private $middleName;
	private $extName;
	private $exts = array('JR', 'SR');
	private $mysqli;

	function __construct($mysqli)

	{
		$this->mysqli = $mysqli;
		// $this->firstName = $firstName ? $firstName : '';
		// $this->lastName = $lastName ? $lastName : '';
		// $this->middleName = $middleName ? $middleName : '';
		// $this->extName = $extName ? $extName : '';
	}



	public function set_employee_id($employee_id)
	{
		$sql = "SELECT * FROM `employees` WHERE `employees_id` = '$employee_id'";
		$res = $this->mysqli->query($sql);

		if ($row = $res->fetch_assoc()) {
			$this->firstName = $row["firstName"] ? $row["firstName"] : '';
			$this->lastName = $row["lastName"] ? $row["lastName"] : '';
			$this->middleName = $row["middleName"] ? $row["middleName"] : '';
			$this->extName = $row["extName"] ? $row["extName"] : '';
		} else {
			$this->firstName = '';
			$this->lastName = '';
			$this->middleName = '';
			$this->extName = '';
		}
	}


	public function getFullName()
	{

		$firstName = $this->firstName;
		$lastName = $this->lastName;

		if ($this->middleName == ".") {
			$middleName = "";
		} else {
			$middleName	= $this->middleName;
			// $middleName = $this->middleName[0].".";
			if (strlen($middleName) > 0) {
				$middleName = " " . $this->middleName[0] . ".";
			} else $middleName = " " . $this->middleName . ".";
		}

		$extName	=	"";
		if ($this->extName) {
			$extName = strtoupper($this->extName);
			$exts = $this->exts;

			if (in_array(substr($extName, 0, 2), $exts)) {
				$extName = " " . mb_convert_case($extName, MB_CASE_UPPER, "UTF-8");
			} else {
				$extName = " " . $extName;
			}
		}

		$fullname =  mb_convert_case("$lastName, $firstName $middleName", MB_CASE_TITLE, "UTF-8") . $extName;

		return $fullname;
	}

	public function getFullNameStandardUpper()
	{

		$firstName = $this->firstName;
		$lastName = $this->lastName;

		if ($this->middleName == ".") {
			$middleName = "";
		} else {
			$middleName	= $this->middleName;
			if (strlen($middleName) > 0) {
				$middleName = " " . $this->middleName[0] . ". ";
			} else $middleName = " " . $this->middleName . ". ";
		}

		$extName	=	"";
		if ($this->extName) {
			$extName = strtoupper($this->extName);
			$exts = $this->exts;

			if (in_array(substr($extName, 0, 2), $exts)) {
				$extName = " " . mb_convert_case($extName, MB_CASE_UPPER, "UTF-8");
			} else {
				$extName = " " . $extName;
			}
		}

		$fullname =  mb_convert_case("$lastName, $firstName$middleName", MB_CASE_UPPER, "UTF-8") . $extName;

		return $fullname;
	}

	public function getFullNameStandardTitle()
	{

		$firstName = $this->firstName;
		$lastName = $this->lastName;

		if ($this->middleName == ".") {
			$middleName = "";
		} else {
			$middleName	= $this->middleName;
			$middleName = " " . $this->middleName[0] . ". ";
		}

		$extName	=	strtoupper($this->extName);
		$exts = $this->exts;

		if (in_array(substr($extName, 0, 2), $exts)) {
			$extName = " " . mb_convert_case($extName, MB_CASE_TITLE, "UTF-8");
		} else {
			$extName = " " . $extName;
		}

		$fullname =  mb_convert_case("$firstName $middleName $lastName", MB_CASE_TITLE, "UTF-8") . $extName;

		return $fullname;
	}
}
