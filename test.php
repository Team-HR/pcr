<?php

require_once "assets/libs/PmsAppMigrator.php";

date_default_timezone_set("Asia/Manila");
$host = "db";
$usernameDb = "admin";
$password = "teamhrmo2019";
$database = "ihris";
$mysqli = new mysqli($host, $usernameDb, $password, $database);
$mysqli->set_charset("utf8");
#####################################################################################

$migrator = new PmsAppMigrator($mysqli);
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

$migrator->prepare_pms_rsm_assignments_table();
$migrator->prepare_pms_rsm_success_indicators_table();
$migrator->migrate_pms_rsm_assignments_table_and_pms_rsm_success_indicators_table();


