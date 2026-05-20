<?php
/**
 * Script to fix column type mismatches and clean up remaining orphaned data.
 * 
 * This script:
 * 1. Alters column types to match for foreign key constraints
 * 2. Deletes remaining orphaned records
 */

require_once __DIR__ . '/../_connect.db.php';

$db = new mysqli($host, $user, $password, $database);
$db->set_charset("utf8");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "<pre>";
echo "=== FIXING SCHEMA AND CLEANUP ===\n\n";

$totalDeleted = 0;

// 1. Fix column type mismatches
echo "1. Fixing column type mismatches...\n";

// Change spms_pcr_si_assignments.success_indicator_id from bigint(20) unsigned to int(11)
$sql = "ALTER TABLE spms_pcr_si_assignments 
        MODIFY COLUMN success_indicator_id INT(11) NOT NULL";
$result = $db->query($sql);
if ($result) {
    echo "   Changed spms_pcr_si_assignments.success_indicator_id to INT(11)\n";
} else {
    echo "   ERROR: " . $db->error . "\n";
}

// Change spms_pcr_si_assignments.user_id from bigint(20) unsigned to int(10) unsigned
$sql = "ALTER TABLE spms_pcr_si_assignments 
        MODIFY COLUMN user_id INT(10) UNSIGNED NOT NULL";
$result = $db->query($sql);
if ($result) {
    echo "   Changed spms_pcr_si_assignments.user_id to INT(10) UNSIGNED\n";
} else {
    echo "   ERROR: " . $db->error . "\n";
}

// Change spms_pcr_si_qet_descriptors.success_indicator_id from bigint(20) unsigned to int(11)
$sql = "ALTER TABLE spms_pcr_si_qet_descriptors 
        MODIFY COLUMN success_indicator_id INT(11) NOT NULL";
$result = $db->query($sql);
if ($result) {
    echo "   Changed spms_pcr_si_qet_descriptors.success_indicator_id to INT(11)\n";
} else {
    echo "   ERROR: " . $db->error . "\n";
}

// Change spms_pcr_mfos.parent_id from varchar(225) to int(11) and make nullable
$sql = "ALTER TABLE spms_pcr_mfos 
        MODIFY COLUMN parent_id INT(11) NULL";
$result = $db->query($sql);
if ($result) {
    echo "   Changed spms_pcr_mfos.parent_id to INT(11) NULL\n";
} else {
    echo "   ERROR: " . $db->error . "\n";
}

// Convert empty strings to NULL for parent_id
$sql = "UPDATE spms_pcr_mfos 
        SET parent_id = NULL 
        WHERE parent_id = '' OR parent_id = '0'";
$result = $db->query($sql);
if ($result) {
    echo "   Converted empty parent_id values to NULL\n";
} else {
    echo "   ERROR: " . $db->error . "\n";
}

// Change spms_pcr_status.employees_id from int(11) to int(10) unsigned
$sql = "ALTER TABLE spms_pcr_status 
        MODIFY COLUMN employees_id INT(10) UNSIGNED NOT NULL";
$result = $db->query($sql);
if ($result) {
    echo "   Changed spms_pcr_status.employees_id to INT(10) UNSIGNED\n";
} else {
    echo "   ERROR: " . $db->error . "\n";
}

echo "\n";

// 2. Clean up remaining orphaned records
echo "2. Cleaning up remaining orphaned records...\n";

// Delete remaining orphaned indicators
$sql = "DELETE FROM spms_pcr_indicators 
        WHERE cf_ID NOT IN (SELECT cf_ID FROM spms_pcr_mfos)";
$result = $db->query($sql);
if ($result) {
    $deleted = $db->affected_rows;
    $totalDeleted += $deleted;
    echo "   Deleted $deleted orphaned indicators\n";
} else {
    echo "   ERROR: " . $db->error . "\n";
}

// Delete orphaned si_qet_descriptors
$sql = "DELETE FROM spms_pcr_si_qet_descriptors 
        WHERE success_indicator_id NOT IN (SELECT mi_id FROM spms_pcr_indicators)";
$result = $db->query($sql);
if ($result) {
    $deleted = $db->affected_rows;
    $totalDeleted += $deleted;
    echo "   Deleted $deleted orphaned si_qet_descriptors\n";
} else {
    echo "   ERROR: " . $db->error . "\n";
}

// Delete orphaned indicator_accomplishments
$sql = "DELETE FROM spms_pcr_indicator_accomplishments 
        WHERE p_id NOT IN (SELECT mi_id FROM spms_pcr_indicators)";
$result = $db->query($sql);
if ($result) {
    $deleted = $db->affected_rows;
    $totalDeleted += $deleted;
    echo "   Deleted $deleted orphaned indicator_accomplishments\n";
} else {
    echo "   ERROR: " . $db->error . "\n";
}

// Delete orphaned MFOs (parent_id issues)
$sql = "DELETE FROM spms_pcr_mfos 
        WHERE parent_id IS NOT NULL 
        AND parent_id NOT IN (SELECT cf_ID FROM spms_pcr_mfos)";
$result = $db->query($sql);
if ($result) {
    $deleted = $db->affected_rows;
    $totalDeleted += $deleted;
    echo "   Deleted $deleted orphaned MFOs\n";
} else {
    echo "   ERROR: " . $db->error . "\n";
}

echo "\n";
echo "=== FIX AND CLEANUP COMPLETE ===\n";
echo "Total records deleted: $totalDeleted\n";
echo "</pre>";
$db->close();
