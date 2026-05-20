<?php
/**
 * Script to clean up remaining orphaned records blocking FK constraints.
 * 
 * Targets:
 * 1. spms_pcr_indicators with invalid cf_ID
 * 2. spms_pcr_mfos with invalid parent_id
 */

require_once __DIR__ . '/../_connect.db.php';

$db = new mysqli($host, $user, $password, $database);
$db->set_charset("utf8");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "<pre>";
echo "=== CLEANUP REMAINING ORPHANS ===\n\n";

$totalDeleted = 0;

// 1. Delete orphaned spms_pcr_indicators
echo "1. Deleting orphaned indicators...\n";
$sql = "SELECT COUNT(*) as count 
        FROM spms_pcr_indicators i 
        LEFT JOIN spms_pcr_mfos m ON i.cf_ID = m.cf_ID 
        WHERE m.cf_ID IS NULL";
$result = $db->query($sql);
$count = $result->fetch_assoc()['count'];
echo "   Found $count orphaned indicators\n";

if ($count > 0) {
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
}
echo "\n";

// 2. Delete orphaned spms_pcr_mfos (invalid parent_id)
echo "2. Deleting orphaned MFOs (invalid parent_id)...\n";
$sql = "SELECT COUNT(*) as count 
        FROM spms_pcr_mfos m 
        LEFT JOIN spms_pcr_mfos p ON m.parent_id = p.cf_ID 
        WHERE m.parent_id IS NOT NULL AND p.cf_ID IS NULL";
$result = $db->query($sql);
$count = $result->fetch_assoc()['count'];
echo "   Found $count orphaned MFOs\n";

if ($count > 0) {
    $sql = "DELETE FROM spms_pcr_mfos 
            WHERE parent_id IS NOT NULL 
            AND parent_id NOT IN (SELECT cf_ID FROM spms_pcr_mfos)";
    $result = $db->query($sql);
    if ($result) {
        $deleted = $db->affected_rows;
        $totalDeleted += $deleted;
        echo "   DELETED: $deleted records\n";
    } else {
        echo "   ERROR: " . $db->error . "\n";
    }
}
echo "\n";

echo "=== CLEANUP COMPLETE ===\n";
echo "Total records deleted: $totalDeleted\n";
echo "</pre>";
$db->close();
