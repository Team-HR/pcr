<?php
/**
 * One-shot script: drop the now-redundant mi_incharge column from spms_pcr_indicators.
 * Run ONLY after verifying all reads/writes use pms_ipcr_si_assignments.
 */

require_once __DIR__ . '/../_connect.db.php';

$db = new mysqli($host, $user, $password, $database);
$db->set_charset("utf8");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "<pre>";

$sql = "ALTER TABLE spms_pcr_indicators DROP COLUMN mi_incharge";

if ($db->query($sql)) {
    echo "SUCCESS: Column mi_incharge dropped from spms_pcr_indicators.\n";
} else {
    echo "ERROR: " . $db->error . "\n";
}

echo "</pre>";
$db->close();
