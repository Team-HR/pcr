<?php
/**
 * One-shot script: drop the now-redundant serialized QET columns from spms_pcr_indicators.
 * Run ONLY after:
 *   1. tools/migrate_si_qet_descriptors.php has been executed successfully.
 *   2. The application has been verified to read exclusively from pms_si_qet_descriptors.
 */

require_once __DIR__ . '/../_connect.db.php';

$db = new mysqli($host, $user, $password, $database);
$db->set_charset("utf8");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "<pre>";

$sql = "ALTER TABLE spms_pcr_indicators
        DROP COLUMN mi_quality,
        DROP COLUMN mi_eff,
        DROP COLUMN mi_time";

if ($db->query($sql)) {
    echo "SUCCESS: Columns mi_quality, mi_eff, mi_time dropped from spms_pcr_indicators.\n";
} else {
    echo "ERROR: " . $db->error . "\n";
}

echo "</pre>";
$db->close();
