<?php
/**
 * Script to add foreign key constraints to PCR tables.
 * 
 * This script adds foreign key constraints to prevent orphaned data.
 * Run AFTER cleanup_orphaned_data.php has been executed.
 */

require_once __DIR__ . '/../_connect.db.php';

$db = new mysqli($host, $user, $password, $database);
$db->set_charset("utf8");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "<pre>";
echo "=== ADDING FOREIGN KEY CONSTRAINTS ===\n\n";

$constraints = [
    // spms_pcr_indicators.cf_ID → spms_pcr_mfos.cf_ID
    [
        'table' => 'spms_pcr_indicators',
        'column' => 'cf_ID',
        'ref_table' => 'spms_pcr_mfos',
        'ref_column' => 'cf_ID',
        'on_delete' => 'CASCADE'
    ],
    // spms_pcr_si_assignments.success_indicator_id → spms_pcr_indicators.mi_id
    [
        'table' => 'spms_pcr_si_assignments',
        'column' => 'success_indicator_id',
        'ref_table' => 'spms_pcr_indicators',
        'ref_column' => 'mi_id',
        'on_delete' => 'CASCADE'
    ],
    // spms_pcr_si_assignments.user_id → employees.employees_id
    [
        'table' => 'spms_pcr_si_assignments',
        'column' => 'user_id',
        'ref_table' => 'employees',
        'ref_column' => 'employees_id',
        'on_delete' => 'CASCADE'
    ],
    // spms_pcr_si_qet_descriptors.success_indicator_id → spms_pcr_indicators.mi_id
    [
        'table' => 'spms_pcr_si_qet_descriptors',
        'column' => 'success_indicator_id',
        'ref_table' => 'spms_pcr_indicators',
        'ref_column' => 'mi_id',
        'on_delete' => 'CASCADE'
    ],
    // spms_pcr_indicator_accomplishments.p_id → spms_pcr_indicators.mi_id
    [
        'table' => 'spms_pcr_indicator_accomplishments',
        'column' => 'p_id',
        'ref_table' => 'spms_pcr_indicators',
        'ref_column' => 'mi_id',
        'on_delete' => 'CASCADE'
    ],
    // spms_pcr_mfos.parent_id → spms_pcr_mfos.cf_ID (self-referencing)
    [
        'table' => 'spms_pcr_mfos',
        'column' => 'parent_id',
        'ref_table' => 'spms_pcr_mfos',
        'ref_column' => 'cf_ID',
        'on_delete' => 'SET NULL'
    ],
    // spms_pcr_status.employees_id → employees.employees_id
    [
        'table' => 'spms_pcr_status',
        'column' => 'employees_id',
        'ref_table' => 'employees',
        'ref_column' => 'employees_id',
        'on_delete' => 'CASCADE'
    ],
];

$successCount = 0;
$errorCount = 0;

foreach ($constraints as $idx => $constraint) {
    $constraintName = "fk_{$constraint['table']}_{$constraint['column']}";
    
    echo ($idx + 1) . ". Adding constraint: {$constraint['table']}.{$constraint['column']} → {$constraint['ref_table']}.{$constraint['ref_column']}...\n";
    
    // Check if constraint already exists
    $checkSql = "SELECT COUNT(*) as count 
                 FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                 WHERE TABLE_SCHEMA = '$database' 
                 AND TABLE_NAME = '{$constraint['table']}' 
                 AND CONSTRAINT_NAME = '$constraintName'";
    $checkResult = $db->query($checkSql);
    $exists = $checkResult->fetch_assoc()['count'] > 0;
    
    if ($exists) {
        echo "   SKIPPED: Constraint already exists\n\n";
        $successCount++;
        continue;
    }
    
    $sql = "ALTER TABLE {$constraint['table']}
            ADD CONSTRAINT {$constraintName}
            FOREIGN KEY ({$constraint['column']})
            REFERENCES {$constraint['ref_table']}({$constraint['ref_column']})
            ON DELETE {$constraint['on_delete']}";
    
    $result = $db->query($sql);
    
    if ($result) {
        echo "   SUCCESS\n";
        $successCount++;
    } else {
        echo "   ERROR: " . $db->error . "\n";
        $errorCount++;
    }
    echo "\n";
}

echo "=== CONSTRAINT ADDITION COMPLETE ===\n";
echo "Success: $successCount\n";
echo "Errors: $errorCount\n";
echo "</pre>";
$db->close();
