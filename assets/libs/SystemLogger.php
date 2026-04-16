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
		$createLogTableSql = "CREATE TABLE IF NOT EXISTS spms_system_logs (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  guest text DEFAULT NULL,
		  employee_id int(11) NOT NULL,
		  query longtext NOT NULL,
		  updated_at timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
		  PRIMARY KEY (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

		$createLogTable = $mysqli->query($createLogTableSql);
		if (!$createLogTable) {
			return false;
		}

		return true;
	}
}

if (!function_exists('logSpmsSystemQuery')) {
	function getSpmsClientIpAddress(): string
	{
		$server = $_SERVER ?? [];
		$candidates = [
			$server['HTTP_CF_CONNECTING_IP'] ?? null,
			$server['HTTP_X_REAL_IP'] ?? null,
			$server['HTTP_CLIENT_IP'] ?? null,
			$server['HTTP_X_FORWARDED_FOR'] ?? null,
			$server['REMOTE_ADDR'] ?? null,
		];

		foreach ($candidates as $candidate) {
			if (!is_string($candidate) || trim($candidate) === '') {
				continue;
			}

			$parts = preg_split('/\s*,\s*/', $candidate);
			if (!is_array($parts)) {
				continue;
			}

			foreach ($parts as $ip) {
				$ip = trim((string)$ip);
				if ($ip === '') {
					continue;
				}
				if (filter_var($ip, FILTER_VALIDATE_IP)) {
					return $ip;
				}
			}
		}

		return 'Unknown';
	}

	function getSpmsBestEffortHostname(string $ip): ?string
	{
		if ($ip === 'Unknown' || !filter_var($ip, FILTER_VALIDATE_IP)) {
			return null;
		}

		$hostname = @gethostbyaddr($ip);
		if (!is_string($hostname) || $hostname === '' || $hostname === $ip) {
			return null;
		}

		return $hostname;
	}

	function logSpmsSystemQuery($mysqli, $escapedSql)
	{
		if (!ensureSpmsSystemLogsTable($mysqli)) {
			return false;
		}

		$employeeId = (int)($_SESSION['emp_info']['employees_id'] ?? 0);
		if ($employeeId <= 0) {
			return true;
		}

		$guestIp = getSpmsClientIpAddress();
		$guestHost = getSpmsBestEffortHostname($guestIp);

		$guestMeta = [
			'ip' => $guestIp,
			'hostname' => $guestHost,
			'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
			'accept_language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null,
			'referer' => $_SERVER['HTTP_REFERER'] ?? null,
			'client_hints' => [
				'ua' => $_SERVER['HTTP_SEC_CH_UA'] ?? null,
				'platform' => $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] ?? null,
				'mobile' => $_SERVER['HTTP_SEC_CH_UA_MOBILE'] ?? null,
			],
		];

		if (func_num_args() >= 3) {
			$extra = func_get_arg(2);
			if (is_array($extra) && !empty($extra)) {
				$guestMeta['extra'] = $extra;
			}
		}

		$guestMetaJson = json_encode($guestMeta, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		if (!is_string($guestMetaJson) || $guestMetaJson === '') {
			$guestMetaJson = (string)$guestIp;
		}

		$guestMetaEscaped = $mysqli->real_escape_string($guestMetaJson);

		$insertLogSql = "INSERT INTO spms_system_logs (guest, employee_id, query) VALUES ('$guestMetaEscaped', '$employeeId', '$escapedSql')";
		$insertLog = $mysqli->query($insertLogSql);
		if (!$insertLog) {
			return false;
		}

		return true;
	}
}
