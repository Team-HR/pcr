<?php

class PmsAppMigrator
{

	private $mysqli;
	private $mysqli_new;

	public function __construct($mysqli, $mysqli_new)
	{
		$this->mysqli = $mysqli;
		$this->mysqli_new = $mysqli_new;
	}

	private function get_top_parent_of_rsm_success_indicator(&$parents, $cf_ID)
	{
		$sql = "SELECT * FROM `spms_corefunctions` WHERE `cf_ID` = '$cf_ID'";
		$res = $this->mysqli->query($sql);
		if ($row = $res->fetch_assoc()) {
			$parents[] = $row;
			if ($row['parent_id'] != '') {
				$this->get_top_parent_of_rsm_success_indicator($parents, $row['parent_id']);
			}
		}
	}


	public function prepare_sys_positions()
	{
		$sql = "
        	DROP TABLE IF EXISTS `sys_positions`;
        ";
		$this->mysqli_new->query($sql);

		$sql = "
        CREATE TABLE `sys_positions` (
			`id` bigint(20) UNSIGNED NOT NULL,
			`position` varchar(255) NOT NULL,
			`functional` varchar(255) DEFAULT NULL,
			`level` int(11) DEFAULT NULL,
			`category` varchar(255) DEFAULT NULL,
			`sg` int(11) DEFAULT NULL,
			`created_at` timestamp NULL DEFAULT NULL,
			`updated_at` timestamp NULL DEFAULT NULL
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
		$this->mysqli_new->query($sql);

		$sql = "
		ALTER TABLE `sys_positions`
			ADD PRIMARY KEY (`id`);
        ";
		$this->mysqli_new->query($sql);

		$sql = "
        ALTER TABLE `sys_positions`
  			MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
        ";
		$this->mysqli_new->query($sql);
	}

	public function migrate_sys_positions_table()
	{
		$sql = "SELECT * FROM `positiontitles`";
		$res = $this->mysqli->query($sql);
		$data = [];

		// 1	id Primary	bigint(20)		UNSIGNED	No	None		AUTO_INCREMENT	Change Change	Drop Drop	
		// 2	position	varchar(255)	utf8mb4_unicode_ci		No	None			Change Change	Drop Drop	
		// 3	functional	varchar(255)	utf8mb4_unicode_ci		Yes	NULL			Change Change	Drop Drop	
		// 4	level	int(11)			Yes	NULL			Change Change	Drop Drop	
		// 5	category	varchar(255)	utf8mb4_unicode_ci		Yes	NULL			Change Change	Drop Drop	
		// 6	sg	int(11)			Yes	NULL			Change Change	Drop Drop	
		// 7	created_at	timestamp			Yes	NULL			Change Change	Drop Drop	
		// 8	updated_at	timestamp			Yes	NULL			Change Change	Drop Drop	

		while ($row = $res->fetch_assoc()) {
			$data[] = [
				"id" => $row["position_id"],
				"position" => $row['position'],
				"functional" => $row['functional'],
				"level" => $row['level'],
				"category" => $row['category'],
				"sg" => $row['salaryGrade'],
			];
		}


		foreach ($data as $position) {
			$sql = "INSERT INTO `sys_positions` (`id`, `position`, `functional`, `level`, `category`, `sg`, `created_at`, `updated_at`) VALUES ('$position[id]', '$position[position]', '$position[functional]', '$position[level]', '$position[category]', '$position[sg]', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())";
			$this->mysqli_new->query($sql);
		}

		$json = json_encode($data, JSON_PRETTY_PRINT);
		echo "<pre>$json</pre>";
	}

	public function prepare_pms_pcr_support_functions_table()
	{
		$sql = "
        	DROP TABLE IF EXISTS `pms_pcr_support_functions`;
        ";
		$this->mysqli_new->query($sql);

		$sql = "
        CREATE TABLE `pms_pcr_support_functions` (
			`id` bigint(20) UNSIGNED NOT NULL,
			`order_num` smallint(6) NOT NULL DEFAULT 0,
			`pms_period_id` bigint(20) UNSIGNED NOT NULL,
			`support_function` varchar(255) NOT NULL,
			`success_indicator` varchar(255) NOT NULL,
			`quality` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '[]' CHECK (json_valid(`quality`)),
			`efficiency` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '[]' CHECK (json_valid(`efficiency`)),
			`timeliness` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '[]' CHECK (json_valid(`timeliness`)),
			`percent` int(11) NOT NULL,
			`form_type` varchar(255) NOT NULL,
			`created_at` timestamp NULL DEFAULT NULL,
			`updated_at` timestamp NULL DEFAULT NULL
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
		$this->mysqli_new->query($sql);

		$sql = "
		ALTER TABLE `pms_pcr_support_functions`
			ADD PRIMARY KEY (`id`);
        ";
		$this->mysqli_new->query($sql);

		$sql = "
        ALTER TABLE `pms_pcr_support_functions`
  			MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
        ";
		$this->mysqli_new->query($sql);
	}

	public function migrate_pms_pcr_support_functions_table()
	{
		$sql = "SELECT * FROM `spms_supportfunctions`";
		$res = $this->mysqli->query($sql);
		$data = [];
		$form_typ_cmp_for_order = "";
		$order_num = 0;
		while ($row = $res->fetch_assoc()) {
			$Q = unserialize($row['Q']);
			$E = unserialize($row['E']);
			$T = unserialize($row['T']);

			$Q = $this->convertSerial($Q);
			$Q = json_encode($Q);
			$row["Q"] = $Q;

			$E = $this->convertSerial($E);
			$E = json_encode($E);
			$row["E"] = $E;

			$T = $this->convertSerial($T);
			$T = json_encode($T);
			$row["T"] = $T;

			$form_type = "";
			if ($row['type'] == '1') {
				$form_type = "ipcr";
			} elseif ($row['type'] == '2') {
				$form_type = "spcr";
			} elseif ($row['type'] == '3') {
				$form_type = "dpcr";
			} elseif ($row['type'] == '4') {
				$form_type = "dspcr";
			}


			if ($form_type == $form_typ_cmp_for_order) {
				$order_num += 1;
			} else {
				$order_num = 0;
			}

			$form_typ_cmp_for_order = $form_type;

			$data[] = [
				"id" => $row['id_suppFunc'],
				"order_num" => $order_num,
				"pms_period_id" => 11,
				"support_function" => $row['mfo'],
				"success_indicator" => $row['suc_in'],
				"quality" => $row['Q'],
				"efficiency" => $row['E'],
				"timeliness" => $row['T'],
				"percent" => $row['percent'],
				"form_type" => $form_type,
				// "created_at" => $row[''],
				// "updated_at" => $row[''],
			];
		}


		// INSERT INTO `pms_pcr_support_functions` (`id`, `order_num`, `pms_period_id`, `support_function`, `success_indicator`, `quality`, `efficiency`, `timeliness`, `percent`, `form_type`, `created_at`, `updated_at`) VALUES (NULL, '0', '', '', '', '[]', '[]', '[]', '', '', NULL, NULL)


		foreach ($data as $support_function) {
			$sql = "INSERT INTO `pms_pcr_support_functions` (`id`, `order_num`, `pms_period_id`, `support_function`, `success_indicator`, `quality`, `efficiency`, `timeliness`, `percent`, `form_type`, `created_at`, `updated_at`) VALUES ('$support_function[id]', '$support_function[order_num]', '$support_function[pms_period_id]', '$support_function[support_function]', '$support_function[success_indicator]', '$support_function[quality]', '$support_function[efficiency]', '$support_function[timeliness]', '$support_function[percent]', '$support_function[form_type]', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())";
			$this->mysqli_new->query($sql);
		}
		// $json = json_encode($data, JSON_PRETTY_PRINT);
		// echo "<pre>" . $json . "</pre>";
	}


	public function migrate_pms_rsm_assignments_table_and_pms_rsm_success_indicators_table()
	{
		$sql = "SELECT * FROM `spms_matrixindicators`";
		// ; --Where mi_id = 10755
		$res = $this->mysqli->query($sql);
		$data = [];
		while ($row = $res->fetch_assoc()) {
			$mi_quality = unserialize($row['mi_quality']);
			$mi_eff = unserialize($row['mi_eff']);
			$mi_time = unserialize($row['mi_time']);
			$mi_quality = $this->convertSerial($mi_quality);
			$mi_quality = json_encode($mi_quality);
			$mi_eff = $this->convertSerial($mi_eff);
			$mi_eff = json_encode($mi_eff);
			$mi_time = $this->convertSerial($mi_time);
			$mi_time = json_encode($mi_time);
			$id = $row['mi_id'];
			$pms_rsm_id = $row['cf_ID'];
			$in_charges = explode(",", $row['mi_incharge']);
			$parents = [];
			$this->get_top_parent_of_rsm_success_indicator($parents, $pms_rsm_id);
			$period_id = $parents ? end($parents)['mfo_periodId'] : 0; //$top_most_parent ? $top_most_parent['mfo_periodId'] : 0;

			if (count($parents) > 0) {
				if (is_array($in_charges)) {
					foreach ($in_charges as $employee_id) {
						$sql = "INSERT INTO `pms_rsm_assignments`(`id`, `period_id`, `pms_rsm_success_indicator_id`, `sys_employee_id`,`created_at`, `updated_at`) VALUES (NULL,'$period_id','$id','$employee_id', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP());";
						$this->mysqli_new->query($sql);
					}
				}
				$success_indicator = $this->mysqli->real_escape_string($row['mi_succIn']);
				$quality = $this->mysqli->real_escape_string($mi_quality);
				$efficiency = $this->mysqli->real_escape_string($mi_eff);
				$timeliness = $this->mysqli->real_escape_string($mi_time);
				$sql = "INSERT INTO `pms_rsm_success_indicators`(`id`, `pms_rsm_id`, `index`, `success_indicator`, `quality`, `efficiency`, `timeliness`, `in_charges`, `corrections`, `created_at`, `updated_at`) VALUES ('$id','$pms_rsm_id','0','$success_indicator','$quality','$efficiency','$timeliness','[]','[]', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())";
				$this->mysqli_new->query($sql);

				$data[] = [
					"id" => $id,
					"pms_rsm_id" => $row['cf_ID'],
					"index" => 0,
					"success_indicator" => $success_indicator,
					"quality" => $mi_quality,
					"efficiency" => $mi_eff,
					"timeliness" => $mi_time,
					"in_charges" => $in_charges,
					// "parents" => $parents,
					"period_id" => $period_id
				];
			}
		}

		$json = json_encode($data, JSON_PRETTY_PRINT);
		echo "<pre>$json</pre>";
	}

	# create pms_rsm_assignments_table if not existing in current DB
	public function prepare_sys_employee_assigned_departments()
	{
		$sql = "
        DROP TABLE IF EXISTS `sys_employee_assigned_departments`;
        ";
		$this->mysqli_new->query($sql);

		$sql = "
        CREATE TABLE `sys_employee_assigned_departments` (
            `id` bigint(20) UNSIGNED NOT NULL,
            `sys_department_id` bigint(20) UNSIGNED NOT NULL,
            `sys_employee_id` bigint(20) UNSIGNED NOT NULL,
            `is_current` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
		$this->mysqli_new->query($sql);

		$sql = "
        ALTER TABLE `sys_employee_assigned_departments`
            ADD PRIMARY KEY (`id`);
        ";
		$this->mysqli_new->query($sql);

		$sql = "
        ALTER TABLE `sys_employee_assigned_departments`
            MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
        ";
		$this->mysqli_new->query($sql);
	}

	public function migrate_to_prepare_sys_employee_assigned_departments_table()
	{
		$sql = "SELECT * FROM `employees`";
		$res = $this->mysqli->query($sql);
		// $debug = "";
		while ($row = $res->fetch_assoc()) {
			$sys_employee_id = $row['employees_id'];
			$sys_department_id = $this->mysqli->real_escape_string($row['department_id']);
			$sql = "INSERT INTO `sys_employee_assigned_departments`(`id`, `sys_department_id`, `sys_employee_id`, `is_current`, `created_at`, `updated_at`) VALUES (NULL,'$sys_department_id','$sys_employee_id','1', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())";
			$this->mysqli_new->query($sql);
			// $debug .= json_encode($this->mysqli->error) . "<br/>";
		}
		// echo $debug;
	}

	public function prepare_sys_employees_table()
	{
		$sql = "
        DROP TABLE IF EXISTS `sys_employees`;
        ";
		$this->mysqli_new->query($sql);

		$sql = "
        CREATE TABLE `sys_employees` (
            `id` bigint(20) UNSIGNED NOT NULL,
            `last_name` varchar(255) NOT NULL,
            `first_name` varchar(255) NOT NULL,
            `middle_name` varchar(255) DEFAULT NULL,
            `ext` varchar(255) DEFAULT NULL,
            `gender` varchar(255) DEFAULT NULL,
            `is_employee` tinyint(1) NOT NULL DEFAULT 1,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `remarks` varchar(255) DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
		$this->mysqli_new->query($sql);

		$sql = "
        ALTER TABLE `sys_employees`
            ADD PRIMARY KEY (`id`);
        ";
		$this->mysqli_new->query($sql);

		$sql = "
        ALTER TABLE `sys_employees`
            MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
        ";
		$this->mysqli_new->query($sql);
	}

	public function migrate_to_sys_employees_table()
	{
		$sql = "SELECT * FROM `employees`";
		$res = $this->mysqli->query($sql);
		// $debug = "";
		while ($row = $res->fetch_assoc()) {
			$id = $row['employees_id'];
			$last_name = $this->mysqli->real_escape_string($row['lastName']);
			$first_name = $this->mysqli->real_escape_string($row['firstName']);
			$middle_name = $this->mysqli->real_escape_string($row['middleName']);
			$ext = $this->mysqli->real_escape_string($row['extName']);
			$gender = $row['gender'];
			$is_employee = 1;
			$is_active = $row['status'] == 'ACTIVE' ? 1 : 0;

			$sql = "INSERT INTO `sys_employees`(`id`, `last_name`, `first_name`, `middle_name`, `ext`, `gender`, `is_employee`, `is_active`, `remarks`, `created_at`, `updated_at`) VALUES ('$id','$last_name','$first_name','$middle_name','$ext','$gender','$is_employee','$is_active', NULL , CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())";
			$this->mysqli_new->query($sql);
			// $debug .= json_encode($this->mysqli->error) . "<br/>";
		}
		// echo $debug;
	}

	public function prepare_sys_departments_table()
	{
		$sql = "
        DROP TABLE IF EXISTS `sys_departments`;
        ";
		$this->mysqli_new->query($sql);

		$sql = "
        CREATE TABLE `sys_departments` (
            `id` bigint(20) UNSIGNED NOT NULL,
            `name` varchar(255) NOT NULL,
            `full_name` varchar(255) DEFAULT NULL,
            `is_section` tinyint(1) NOT NULL DEFAULT 0,
            `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
		$this->mysqli_new->query($sql);

		$sql = "
        ALTER TABLE `sys_departments`
            ADD PRIMARY KEY (`id`);
        ";
		$this->mysqli_new->query($sql);

		$sql = "
        ALTER TABLE `sys_departments`
            MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
        ";
		$this->mysqli_new->query($sql);
	}

	public function migrate_to_sys_departments_table()
	{
		$sql = "SELECT * FROM `department`";
		$res = $this->mysqli->query($sql);
		while ($row = $res->fetch_assoc()) {
			$id = $row['department_id'];
			$name = $row['alias'];
			$full_name = $row['department'];
			$sql = "INSERT INTO `sys_departments` (`id`, `name`, `full_name`, `is_section`, `parent_id`, `created_at`, `updated_at`) VALUES ('$id', '$name', '$full_name', '0', NULL, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())";
			$this->mysqli_new->query($sql);
		}
	}

	public function prepare_pms_rsms_table()
	{
		$sql = "
        DROP TABLE IF EXISTS `pms_rsms`;
        ";
		$this->mysqli_new->query($sql);

		$sql = "
		CREATE TABLE `pms_rsms` (
			`id` bigint(20) UNSIGNED NOT NULL,
			`period_id` bigint(20) UNSIGNED NOT NULL,
			`parent_id` bigint(20) UNSIGNED DEFAULT NULL,
			`sys_department_id` bigint(20) UNSIGNED NOT NULL,
			`code` varchar(255) DEFAULT NULL,
			`title` varchar(255) DEFAULT NULL,
			`created_at` timestamp NULL DEFAULT NULL,
			`updated_at` timestamp NULL DEFAULT NULL
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

		$this->mysqli_new->query($sql);

		$sql = "
		ALTER TABLE `pms_rsms`
			ADD PRIMARY KEY (`id`);
        ";
		$this->mysqli_new->query($sql);

		$sql = "
		ALTER TABLE `pms_rsms`
				MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
        ";
		$this->mysqli_new->query($sql);
	}

	public function migrate_pms_rsms_table()
	{
		$sql = "SELECT * FROM `spms_corefunctions`";
		$res = $this->mysqli->query($sql);
		$data = [];
		while ($row = $res->fetch_assoc()) {
			$data[] = [
				"id" => $row['cf_ID'],
				"period_id" => $row['mfo_periodId'],
				"parent_id" => $row['parent_id'] ? $row['parent_id'] : NULL,
				"sys_department_id" =>  $row['dep_id'],
				"code" => $row['cf_count'] ? $row['cf_count'] : NULL,
				"title" => $row['cf_title'],
			];
		}

		$sqls = [];
		foreach ($data as $mfo) {
			$parent_id = $parent_id_ = $mfo['parent_id'];

			if (!$parent_id) {
				$parent_id = "NULL";
			}



			$code = $this->mysqli_new->real_escape_string($mfo["code"]);
			$title = $this->mysqli_new->real_escape_string($mfo["title"]);

			$sql = "INSERT INTO `pms_rsms` (`id`, `period_id`, `parent_id`, `sys_department_id`, `code`, `title`, `created_at`, `updated_at`) VALUES ('$mfo[id]', '$mfo[period_id]', $parent_id, '$mfo[sys_department_id]', '$code', '$title', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())";

			// if (!$parent_id_) {
			$sqls[] = $sql;
			$res = $this->mysqli_new->query($sql);
			// }
		}

		$json = json_encode($sqls, JSON_PRETTY_PRINT);
		echo "<pre>$json</pre>";
	}

	public function prepare_pms_rsm_assignments_table()
	{
		$sql = "
        DROP TABLE IF EXISTS `pms_rsm_assignments`;
        ";
		$this->mysqli_new->query($sql);

		$sql = "
        CREATE TABLE IF NOT EXISTS `pms_rsm_assignments` (
          `id` bigint(20) UNSIGNED NOT NULL,
          `period_id` bigint(20) UNSIGNED NOT NULL,
          `pms_rsm_success_indicator_id` bigint(20) UNSIGNED NOT NULL,
          `sys_employee_id` bigint(20) UNSIGNED NOT NULL,
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
		$this->mysqli_new->query($sql);

		$sql = "
        ALTER TABLE `pms_rsm_assignments`
          ADD PRIMARY KEY (`id`);
        ";
		$this->mysqli_new->query($sql);

		$sql = "
          ALTER TABLE `pms_rsm_assignments`
          MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
        ";
		$this->mysqli_new->query($sql);
	}

	# prepare_pms_rsm_success_indicators_table
	public function prepare_pms_rsm_success_indicators_table()
	{
		$sql = "
        DROP TABLE IF EXISTS `pms_rsm_success_indicators`;
        ";
		$this->mysqli_new->query($sql);

		$sql = "
        CREATE TABLE `pms_rsm_success_indicators` (
            `id` bigint(20) UNSIGNED NOT NULL,
            `pms_rsm_id` bigint(20) UNSIGNED NOT NULL,
            `index` int(11) NOT NULL DEFAULT 0,
            `success_indicator` varchar(255) NOT NULL,
            `quality` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`quality`)),
            `efficiency` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`efficiency`)),
            `timeliness` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`timeliness`)),
            `in_charges` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`in_charges`)),
            `corrections` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`corrections`)),
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
		$this->mysqli_new->query($sql);

		$sql = "
        ALTER TABLE `pms_rsm_success_indicators`
            ADD PRIMARY KEY (`id`);
        ";
		$this->mysqli_new->query($sql);

		$sql = "
        ALTER TABLE `pms_rsm_success_indicators`
        MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
        ";
		$this->mysqli_new->query($sql);
	}

	private function convertSerial($metrics)
	{
		if (is_array($metrics)) {
			$null_count = 0;
			array_shift($metrics);
			foreach ($metrics as $key => $metric) {
				if ($metric == "") {
					$metrics[$key] = null;
					$null_count += 1;
				}
			}

			$metrics = array_reverse($metrics);

			if ($null_count  == 5) {
				$metrics = [];
			}
		} else {
			$metrics = [];
		}
		return $metrics;
	}
}
