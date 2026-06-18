<?php
/**
 * Script to insert missing strategic accomplishments for exempted periods (22, 23, 24, 25).
 * 
 * This script checks spms_pcr_status for employees in periods 22-25 and inserts
 * a spms_pcr_strategic_accomplishments record with noStrat=1 if one doesn't exist.
 * 
 * Text fields (mfo, succ_in, acc, remark) are set to 'N/A'
 * average is set to 0
 * Idempotent - safe to run multiple times
 */

require_once __DIR__ . '/../_connect.db.php';

$db = new mysqli($host, $user, $password, $database);
$db->set_charset("utf8");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "<pre>";
echo "=== INSERT MISSING STRATEGIC ACCOMPLISHMENTS ===\n\n";

$exempted_periods = [22, 23, 24, 25];
$totalInserted = 0;
$totalChecked = 0;

foreach ($exempted_periods as $period_id) {
    echo "Processing Period ID: $period_id\n";
    echo str_repeat("-", 40) . "\n";
    
    // Get all employees in spms_pcr_status for this period
    $sql = "SELECT DISTINCT employees_id FROM spms_pcr_status WHERE period_id = '$period_id'";
    $result = $db->query($sql);
    
    if (!$result) {
        echo "   ERROR fetching status records: " . $db->error . "\n\n";
        continue;
    }
    
    $periodInserted = 0;
    $periodChecked = 0;
    
    while ($row = $result->fetch_assoc()) {
        $emp_id = $row['employees_id'];
        $periodChecked++;
        
        // Check if strategic accomplishment already exists for this emp/period
        $checkSql = "SELECT COUNT(*) as count FROM spms_pcr_strategic_accomplishments 
                     WHERE emp_id = '$emp_id' AND period_id = '$period_id'";
        $checkResult = $db->query($checkSql);
        $exists = $checkResult->fetch_assoc()['count'] > 0;
        
        if ($exists) {
            continue; // Skip if already exists
        }
        
        // Insert new record with noStrat=1
        $insertSql = "INSERT INTO spms_pcr_strategic_accomplishments 
                      (strategicFunc_id, period_id, emp_id, mfo, succ_in, acc, average, remark, noStrat)
                      VALUES (NULL, '$period_id', '$emp_id', 'N/A', 'N/A', 'N/A', 0, 'N/A', 1)";
        
        $insertResult = $db->query($insertSql);
        
        if ($insertResult) {
            $periodInserted++;
        } else {
            echo "   ERROR inserting for emp_id=$emp_id: " . $db->error . "\n";
        }
    }
    
    echo "   Checked: $periodChecked employees\n";
    echo "   Inserted: $periodInserted records\n\n";
    
    $totalChecked += $periodChecked;
    $totalInserted += $periodInserted;
}

echo "=== SUMMARY ===\n";
echo "Total employees checked: $totalChecked\n";
echo "Total records inserted: $totalInserted\n";
echo "=== COMPLETE ===\n";
echo "</pre>";

$db->close();
