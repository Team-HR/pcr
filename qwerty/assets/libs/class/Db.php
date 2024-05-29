<?php

class Db extends mysqli
{
	protected $mysqli;

	function __construct()
	{

		$dirs = explode(DIRECTORY_SEPARATOR, __DIR__);
		$config_file = "_connect.db.php"; //database configuration that serves as identifier for where the project folder (ihris/ihris_dev) is
		$file_location = "";
		foreach ($dirs as $dir) {
			if (file_exists($file_location . $config_file)) {
				$config_file = $file_location . $config_file;
				break;
			}
			$file_location .= $dir . DIRECTORY_SEPARATOR;
		}

		require $config_file;
		
		$mysqli = new mysqli($host, $user, $password, $database);
		$mysqli->set_charset("utf8");

		// MYSQL ERROR REPORTING START
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		// MYSQL ERROR REPORTING END

		$this->mysqli = $mysqli;

	}

	public function getMysqli()
	{
		return $this->mysqli;
	}
}
