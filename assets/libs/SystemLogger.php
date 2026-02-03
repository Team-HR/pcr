<?php
/**
 * 
 * How to call it from other files in assets/libs/
 * From any PHP file, include the helper then call:
 * 
 * php
 * require_once __DIR__ . '/../../libs/SystemLogger.php';
 * (Adjust the path if the caller is not in the same folder.)
 * 
 * $escapedLogQuery = $mysqli->real_escape_string($sqlQuery);
 * if (!logSpmsSystemQuery($mysqli, $escapedLogQuery)) {
 *   die($mysqli->error);
 * }
 * 
 * 
*/

if (!function_exists('ensureSpmsSystemLogsTable')) {
	function ensureSpmsSystemLogsTable($mysqli)
	{
		$createLogTableSql = "CREATE TABLE IF NOT EXISTS `spms_system_logs` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `employee_id` int(11) NOT NULL,
		  `query` longtext NOT NULL,
		  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

		$createLogTable = $mysqli->query($createLogTableSql);
		if (!$createLogTable) {
			return false;
		}

		return true;
	}
}

if (!function_exists('logSpmsSystemQuery')) {
	function logSpmsSystemQuery($mysqli, $escapedSql)
	{
		if (!ensureSpmsSystemLogsTable($mysqli)) {
			return false;
		}

		$employeeId = (int)($_SESSION['emp_info']['employees_id'] ?? 0);
		if ($employeeId <= 0) {
			return true;
		}

		$insertLogSql = "INSERT INTO `spms_system_logs` (`employee_id`, `query`) VALUES ('$employeeId', '$escapedSql')";
		$insertLog = $mysqli->query($insertLogSql);
		if (!$insertLog) {
			return false;
		}

		return true;
	}
}
