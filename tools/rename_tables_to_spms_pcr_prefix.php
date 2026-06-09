<?php
/**
 * One-shot script: rename PCR-related tables to use spms_pcr_ prefix.
 * 
 * Renames:
 * - pms_ipcr_si_assignments → spms_pcr_si_assignments
 * - pms_si_qet_descriptors → spms_pcr_si_qet_descriptors
 *
 * Run AFTER code has been updated to use the new table names.
 */

require_once __DIR__ . '/../_connect.db.php';

$db = new mysqli($host, $user, $password, $database);
$db->set_charset("utf8");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "<pre>";

// Rename pms_ipcr_si_assignments → spms_pcr_si_assignments
$sql1 = "RENAME TABLE pms_ipcr_si_assignments TO spms_pcr_si_assignments";
if ($db->query($sql1)) {
    echo "SUCCESS: Renamed pms_ipcr_si_assignments → spms_pcr_si_assignments\n";
} else {
    echo "ERROR: Failed to rename pms_ipcr_si_assignments: " . $db->error . "\n";
}

// Rename pms_si_qet_descriptors → spms_pcr_si_qet_descriptors
$sql2 = "RENAME TABLE pms_si_qet_descriptors TO spms_pcr_si_qet_descriptors";
if ($db->query($sql2)) {
    echo "SUCCESS: Renamed pms_si_qet_descriptors → spms_pcr_si_qet_descriptors\n";
} else {
    echo "ERROR: Failed to rename pms_si_qet_descriptors: " . $db->error . "\n";
}

echo "DONE\n";
echo "</pre>";
$db->close();
