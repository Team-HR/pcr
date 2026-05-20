<?php
/**
 * Script to diagnose foreign key constraint issues.
 * 
 * Checks column types, indexes, and remaining orphaned data.
 */

require_once __DIR__ . '/../_connect.db.php';

$db = new mysqli($host, $user, $password, $database);
$db->set_charset("utf8");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "<pre>";
echo "=== FOREIGN KEY DIAGNOSTICS ===\n\n";

// Check column types for foreign key relationships
$checks = [
    ['table' => 'spms_pcr_indicators', 'column' => 'cf_ID', 'ref_table' => 'spms_pcr_mfos', 'ref_column' => 'cf_ID'],
    ['table' => 'spms_pcr_si_assignments', 'column' => 'success_indicator_id', 'ref_table' => 'spms_pcr_indicators', 'ref_column' => 'mi_id'],
    ['table' => 'spms_pcr_si_assignments', 'column' => 'user_id', 'ref_table' => 'employees', 'ref_column' => 'employees_id'],
    ['table' => 'spms_pcr_si_qet_descriptors', 'column' => 'success_indicator_id', 'ref_table' => 'spms_pcr_indicators', 'ref_column' => 'mi_id'],
    ['table' => 'spms_pcr_indicator_accomplishments', 'column' => 'p_id', 'ref_table' => 'spms_pcr_indicators', 'ref_column' => 'mi_id'],
    ['table' => 'spms_pcr_mfos', 'column' => 'parent_id', 'ref_table' => 'spms_pcr_mfos', 'ref_column' => 'cf_ID'],
    ['table' => 'spms_pcr_status', 'column' => 'employees_id', 'ref_table' => 'employees', 'ref_column' => 'employees_id'],
];

foreach ($checks as $idx => $check) {
    echo ($idx + 1) . ". Checking {$check['table']}.{$check['column']} → {$check['ref_table']}.{$check['ref_column']}...\n";
    
    // Get column type for child column
    $sql = "SELECT DATA_TYPE, COLUMN_TYPE, IS_NULLABLE 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = '$database' 
            AND TABLE_NAME = '{$check['table']}' 
            AND COLUMN_NAME = '{$check['column']}'";
    $result = $db->query($sql);
    $childCol = $result->fetch_assoc();
    echo "   Child column: {$childCol['COLUMN_TYPE']} (nullable: {$childCol['IS_NULLABLE']})\n";
    
    // Get column type for referenced column
    $sql = "SELECT DATA_TYPE, COLUMN_TYPE, IS_NULLABLE 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = '$database' 
            AND TABLE_NAME = '{$check['ref_table']}' 
            AND COLUMN_NAME = '{$check['ref_column']}'";
    $result = $db->query($sql);
    $refCol = $result->fetch_assoc();
    echo "   Ref column:  {$refCol['COLUMN_TYPE']} (nullable: {$refCol['IS_NULLABLE']})\n";
    
    // Check if referenced column has index
    $sql = "SELECT INDEX_NAME 
            FROM INFORMATION_SCHEMA.STATISTICS 
            WHERE TABLE_SCHEMA = '$database' 
            AND TABLE_NAME = '{$check['ref_table']}' 
            AND COLUMN_NAME = '{$check['ref_column']}'";
    $result = $db->query($sql);
    if ($result->num_rows > 0) {
        echo "   Ref column index: EXISTS\n";
    } else {
        echo "   Ref column index: MISSING (required for FK)\n";
    }
    
    // Check for orphaned records
    $sql = "SELECT COUNT(*) as count 
            FROM {$check['table']} t 
            LEFT JOIN {$check['ref_table']} r ON t.{$check['column']} = r.{$check['ref_column']} 
            WHERE r.{$check['ref_column']} IS NULL";
    $result = $db->query($sql);
    $count = $result->fetch_assoc()['count'];
    echo "   Orphaned records: $count\n";
    
    echo "\n";
}

echo "=== DIAGNOSTICS COMPLETE ===\n";
echo "</pre>";
$db->close();
