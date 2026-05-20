<?php
/**
 * Script to delete orphaned records from PCR tables.
 * 
 * This script deletes orphaned records in cascade order (child tables first, then parents).
 * No backup is created - deletion is permanent.
 */

require_once __DIR__ . '/../_connect.db.php';

$db = new mysqli($host, $user, $password, $database);
$db->set_charset("utf8");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "<pre>";
echo "=== CLEANUP ORPHANED DATA ===\n\n";

$totalDeleted = 0;

// 1. Delete orphaned spms_pcr_indicator_accomplishments
echo "1. Deleting orphaned indicator_accomplishments...\n";
$sql = "DELETE FROM spms_pcr_indicator_accomplishments 
        WHERE p_id NOT IN (SELECT mi_id FROM spms_pcr_indicators)";
$result = $db->query($sql);
if ($result) {
    $deleted = $db->affected_rows;
    $totalDeleted += $deleted;
    echo "   DELETED: $deleted records\n";
} else {
    echo "   ERROR: " . $db->error . "\n";
}
echo "\n";

// 2. Delete orphaned spms_pcr_si_assignments (invalid user_id)
echo "2. Deleting orphaned si_assignments (invalid user_id)...\n";
$sql = "DELETE FROM spms_pcr_si_assignments 
        WHERE user_id NOT IN (SELECT employees_id FROM employees)";
$result = $db->query($sql);
if ($result) {
    $deleted = $db->affected_rows;
    $totalDeleted += $deleted;
    echo "   DELETED: $deleted records\n";
} else {
    echo "   ERROR: " . $db->error . "\n";
}
echo "\n";

// 3. Delete orphaned spms_pcr_si_qet_descriptors (safety check)
echo "3. Deleting orphaned si_qet_descriptors (safety check)...\n";
$sql = "DELETE FROM spms_pcr_si_qet_descriptors 
        WHERE success_indicator_id NOT IN (SELECT mi_id FROM spms_pcr_indicators)";
$result = $db->query($sql);
if ($result) {
    $deleted = $db->affected_rows;
    $totalDeleted += $deleted;
    echo "   DELETED: $deleted records\n";
} else {
    echo "   ERROR: " . $db->error . "\n";
}
echo "\n";

// 4. Delete orphaned spms_pcr_indicators
echo "4. Deleting orphaned indicators...\n";
$sql = "DELETE FROM spms_pcr_indicators 
        WHERE cf_ID NOT IN (SELECT cf_ID FROM spms_pcr_mfos)";
$result = $db->query($sql);
if ($result) {
    $deleted = $db->affected_rows;
    $totalDeleted += $deleted;
    echo "   DELETED: $deleted records\n";
} else {
    echo "   ERROR: " . $db->error . "\n";
}
echo "\n";

// 5. Delete orphaned spms_pcr_mfos (invalid parent_id - self-referencing)
echo "5. Deleting orphaned MFOs (invalid parent_id)...\n";
$sql = "DELETE FROM spms_pcr_mfos 
        WHERE parent_id != '' 
        AND parent_id NOT IN (SELECT cf_ID FROM spms_pcr_mfos)";
$result = $db->query($sql);
if ($result) {
    $deleted = $db->affected_rows;
    $totalDeleted += $deleted;
    echo "   DELETED: $deleted records\n";
} else {
    echo "   ERROR: " . $db->error . "\n";
}
echo "\n";

// 6. Delete orphaned spms_pcr_status
echo "6. Deleting orphaned status records...\n";
$sql = "DELETE FROM spms_pcr_status 
        WHERE employees_id NOT IN (SELECT employees_id FROM employees)";
$result = $db->query($sql);
if ($result) {
    $deleted = $db->affected_rows;
    $totalDeleted += $deleted;
    echo "   DELETED: $deleted records\n";
} else {
    echo "   ERROR: " . $db->error . "\n";
}
echo "\n";

echo "=== CLEANUP COMPLETE ===\n";
echo "Total records deleted: $totalDeleted\n";
echo "</pre>";
$db->close();
