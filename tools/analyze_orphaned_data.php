<?php
/**
 * Script to analyze orphaned data and identify missing foreign key constraints.
 * 
 * This script checks for:
 * 1. Orphaned records in key tables
 * 2. Missing foreign key constraints
 * 3. Data integrity issues
 */

require_once __DIR__ . '/../_connect.db.php';

$db = new mysqli($host, $user, $password, $database);
$db->set_charset("utf8");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "<pre>";
echo "=== ORPHANED DATA ANALYSIS ===\n\n";

// 1. Check spms_pcr_indicators with invalid cf_ID (parent MFO)
echo "1. Checking spms_pcr_indicators with invalid cf_ID...\n";
$sql = "SELECT COUNT(*) as count 
        FROM spms_pcr_indicators i 
        LEFT JOIN spms_pcr_mfos m ON i.cf_ID = m.cf_ID 
        WHERE m.cf_ID IS NULL";
$result = $db->query($sql);
if (!$result) {
    echo "   ERROR: " . $db->error . "\n\n";
} else {
    $row = $result->fetch_assoc();
    echo "   Orphaned indicators: " . $row['count'] . "\n";
    if ($row['count'] > 0) {
        $sql = "SELECT i.mi_id, i.cf_ID FROM spms_pcr_indicators i LEFT JOIN spms_pcr_mfos m ON i.cf_ID = m.cf_ID WHERE m.cf_ID IS NULL LIMIT 10";
        $result = $db->query($sql);
        if ($result) {
            echo "   Sample orphaned records:\n";
            while ($row = $result->fetch_assoc()) {
                echo "     - mi_id: {$row['mi_id']}, cf_ID: {$row['cf_ID']}\n";
            }
        }
    }
}
echo "\n";

// 2. Check spms_pcr_si_assignments with invalid success_indicator_id
echo "2. Checking spms_pcr_si_assignments with invalid success_indicator_id...\n";
$sql = "SELECT COUNT(*) as count 
        FROM spms_pcr_si_assignments a 
        LEFT JOIN spms_pcr_indicators i ON a.success_indicator_id = i.mi_id 
        WHERE i.mi_id IS NULL";
$result = $db->query($sql);
if (!$result) {
    echo "   ERROR: " . $db->error . "\n\n";
} else {
    $row = $result->fetch_assoc();
    echo "   Orphaned assignments: " . $row['count'] . "\n";
    if ($row['count'] > 0) {
        $sql = "SELECT a.id, a.success_indicator_id FROM spms_pcr_si_assignments a LEFT JOIN spms_pcr_indicators i ON a.success_indicator_id = i.mi_id WHERE i.mi_id IS NULL LIMIT 10";
        $result = $db->query($sql);
        if ($result) {
            echo "   Sample orphaned records:\n";
            while ($row = $result->fetch_assoc()) {
                echo "     - id: {$row['id']}, success_indicator_id: {$row['success_indicator_id']}\n";
            }
        }
    }
}
echo "\n";

// 3. Check spms_pcr_si_assignments with invalid user_id
echo "3. Checking spms_pcr_si_assignments with invalid user_id...\n";
$sql = "SELECT COUNT(*) as count 
        FROM spms_pcr_si_assignments a 
        LEFT JOIN employees e ON a.user_id = e.employees_id 
        WHERE e.employees_id IS NULL";
$result = $db->query($sql);
if (!$result) {
    echo "   ERROR: " . $db->error . "\n\n";
} else {
    $row = $result->fetch_assoc();
    echo "   Orphaned assignments (invalid user): " . $row['count'] . "\n";
    if ($row['count'] > 0) {
        $sql = "SELECT a.id, a.user_id FROM spms_pcr_si_assignments a LEFT JOIN employees e ON a.user_id = e.employees_id WHERE e.employees_id IS NULL LIMIT 10";
        $result = $db->query($sql);
        if ($result) {
            echo "   Sample orphaned records:\n";
            while ($row = $result->fetch_assoc()) {
                echo "     - id: {$row['id']}, user_id: {$row['user_id']}\n";
            }
        }
    }
}
echo "\n";

// 4. Check spms_pcr_si_qet_descriptors with invalid success_indicator_id
echo "4. Checking spms_pcr_si_qet_descriptors with invalid success_indicator_id...\n";
$sql = "SELECT COUNT(*) as count 
        FROM spms_pcr_si_qet_descriptors d 
        LEFT JOIN spms_pcr_indicators i ON d.success_indicator_id = i.mi_id 
        WHERE i.mi_id IS NULL";
$result = $db->query($sql);
if (!$result) {
    echo "   ERROR: " . $db->error . "\n\n";
} else {
    $row = $result->fetch_assoc();
    echo "   Orphaned descriptors: " . $row['count'] . "\n";
    if ($row['count'] > 0) {
        $sql = "SELECT d.id, d.success_indicator_id FROM spms_pcr_si_qet_descriptors d LEFT JOIN spms_pcr_indicators i ON d.success_indicator_id = i.mi_id WHERE i.mi_id IS NULL LIMIT 10";
        $result = $db->query($sql);
        if ($result) {
            echo "   Sample orphaned records:\n";
            while ($row = $result->fetch_assoc()) {
                echo "     - id: {$row['id']}, success_indicator_id: {$row['success_indicator_id']}\n";
            }
        }
    }
}
echo "\n";

// 5. Check spms_pcr_indicator_accomplishments with invalid p_id
echo "5. Checking spms_pcr_indicator_accomplishments with invalid p_id...\n";
$sql = "SELECT COUNT(*) as count 
        FROM spms_pcr_indicator_accomplishments a 
        LEFT JOIN spms_pcr_indicators i ON a.p_id = i.mi_id 
        WHERE i.mi_id IS NULL";
$result = $db->query($sql);
if (!$result) {
    echo "   ERROR: " . $db->error . "\n\n";
} else {
    $row = $result->fetch_assoc();
    echo "   Orphaned accomplishments: " . $row['count'] . "\n";
    if ($row['count'] > 0) {
        $sql = "SELECT a.cfd_id, a.p_id FROM spms_pcr_indicator_accomplishments a LEFT JOIN spms_pcr_indicators i ON a.p_id = i.mi_id WHERE i.mi_id IS NULL LIMIT 10";
        $result = $db->query($sql);
        if ($result) {
            echo "   Sample orphaned records:\n";
            while ($row = $result->fetch_assoc()) {
                echo "     - cfd_id: {$row['cfd_id']}, p_id: {$row['p_id']}\n";
            }
        }
    }
}
echo "\n";

// 6. Check spms_pcr_mfos with invalid parent_id (self-referencing)
echo "6. Checking spms_pcr_mfos with invalid parent_id...\n";
$sql = "SELECT COUNT(*) as count 
        FROM spms_pcr_mfos m 
        LEFT JOIN spms_pcr_mfos p ON m.parent_id = p.cf_ID 
        WHERE m.parent_id != '' AND p.cf_ID IS NULL";
$result = $db->query($sql);
if (!$result) {
    echo "   ERROR: " . $db->error . "\n\n";
} else {
    $row = $result->fetch_assoc();
    echo "   Orphaned MFOs (invalid parent): " . $row['count'] . "\n";
    if ($row['count'] > 0) {
        $sql = "SELECT m.cf_ID, m.parent_id FROM spms_pcr_mfos m LEFT JOIN spms_pcr_mfos p ON m.parent_id = p.cf_ID WHERE m.parent_id != '' AND p.cf_ID IS NULL LIMIT 10";
        $result = $db->query($sql);
        if ($result) {
            echo "   Sample orphaned records:\n";
            while ($row = $result->fetch_assoc()) {
                echo "     - cf_ID: {$row['cf_ID']}, parent_id: {$row['parent_id']}\n";
            }
        }
    }
}
echo "\n";

// 7. Check spms_pcr_status with invalid employees_id
echo "7. Checking spms_pcr_status with invalid employees_id...\n";
$sql = "SELECT COUNT(*) as count 
        FROM spms_pcr_status s 
        LEFT JOIN employees e ON s.employees_id = e.employees_id 
        WHERE e.employees_id IS NULL";
$result = $db->query($sql);
if (!$result) {
    echo "   ERROR: " . $db->error . "\n\n";
} else {
    $row = $result->fetch_assoc();
    echo "   Orphaned status records: " . $row['count'] . "\n";
    if ($row['count'] > 0) {
        $sql = "SELECT s.id, s.employees_id FROM spms_pcr_status s LEFT JOIN employees e ON s.employees_id = e.employees_id WHERE e.employees_id IS NULL LIMIT 10";
        $result = $db->query($sql);
        if ($result) {
            echo "   Sample orphaned records:\n";
            while ($row = $result->fetch_assoc()) {
                echo "     - id: {$row['id']}, employees_id: {$row['employees_id']}\n";
            }
        }
    }
}
echo "\n";

// Check existing foreign key constraints
echo "=== EXISTING FOREIGN KEY CONSTRAINTS ===\n";
$sql = "SELECT 
        TABLE_NAME, 
        COLUMN_NAME, 
        REFERENCED_TABLE_NAME, 
        REFERENCED_COLUMN_NAME 
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = '$database' 
        AND REFERENCED_TABLE_NAME IS NOT NULL";
$result = $db->query($sql);
if (!$result) {
    echo "   ERROR: " . $db->error . "\n\n";
} else {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "   {$row['TABLE_NAME']}.{$row['COLUMN_NAME']} → {$row['REFERENCED_TABLE_NAME']}.{$row['REFERENCED_COLUMN_NAME']}\n";
        }
    } else {
        echo "   No foreign key constraints found.\n";
    }
}
echo "\n";

echo "=== ANALYSIS COMPLETE ===\n";
echo "</pre>";
$db->close();
