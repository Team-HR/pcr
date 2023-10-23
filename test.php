<?php

require_once "assets/libs/PmsAppMigrator.php";

date_default_timezone_set("Asia/Manila");
$host = "db";
$usernameDb = "admin";
$password = "teamhrmo2019";
$database = "ihris";
$host_new = "192.168.50.51";
$database_new = "pcr_app";
$mysqli = new mysqli($host, $usernameDb, $password, $database);
$mysqli->set_charset("utf8");
$mysqli_new = new mysqli($host_new, "root", "password", $database_new);
$mysqli->set_charset("utf8");
#####################################################################################

$migrator = new PmsAppMigrator($mysqli, $mysqli_new);

##### create necessary tables if not existing in current DB
# migrate departments
$migrator->prepare_sys_departments_table();
$migrator->migrate_to_sys_departments_table();
# migrate employees
$migrator->prepare_sys_employees_table();
$migrator->migrate_to_sys_employees_table();
# assign employees to sys_employee_assigned_departments
$migrator->prepare_sys_employee_assigned_departments();
$migrator->migrate_to_prepare_sys_employee_assigned_departments_table();
# prepare and migrate rsm and success indicators
$migrator->prepare_pms_rsms_table();
$migrator->prepare_pms_rsm_assignments_table();
$migrator->prepare_pms_rsm_success_indicators_table();
$migrator->migrate_pms_rsm_assignments_table_and_pms_rsm_success_indicators_table();
# prepare and migrate support functions
$migrator->prepare_pms_pcr_support_functions_table();
$migrator->migrate_pms_pcr_support_functions_table();
# prepare and migrate sys_positions
$migrator->prepare_sys_positions();
$migrator->migrate_sys_positions_table();
