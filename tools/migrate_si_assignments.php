<?php
/**
 * Migration script: Creates pms_ipcr_si_assignments table and
 * migrates existing mi_incharge data from spms_pcr_indicators.
 *
 * Run once. Safe to re-run (uses INSERT IGNORE / duplicate checks).
 *
 * assigned_by for historical data: employee ID 9
 */

require_once __DIR__ . '/../_connect.db.php';

$db = new mysqli($host, $user, $password, $database);
$db->set_charset("utf8");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "<pre>";

// ---------------------------------------------------------------
// 1. Create pms_ipcr_si_assignments table
// ---------------------------------------------------------------
$create_sql = "CREATE TABLE IF NOT EXISTS pms_ipcr_si_assignments (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  success_indicator_id bigint(20) unsigned NOT NULL,
  user_id bigint(20) unsigned NOT NULL,
  period_id bigint(20) unsigned NOT NULL,
  assigned_by bigint(20) unsigned NOT NULL,
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_si_id (success_indicator_id),
  KEY idx_user_id (user_id),
  KEY idx_period_id (period_id),
  KEY idx_assigned_by (assigned_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (!$db->query($create_sql)) {
    die("Error creating table: " . $db->error . "\n");
}
echo "Table pms_ipcr_si_assignments: OK\n";

// ---------------------------------------------------------------
// 2. Migrate existing mi_incharge data
// ---------------------------------------------------------------
$sql = "SELECT mi_id, mi_incharge, cf_ID
        FROM spms_pcr_indicators
        WHERE mi_incharge != '' AND mi_incharge IS NOT NULL";
$result = $db->query($sql);

if (!$result) {
    die("Error querying indicators: " . $db->error . "\n");
}

$migrated  = 0;
$skipped   = 0;
$errors    = 0;

while ($row = $result->fetch_assoc()) {
    $mi_id      = (int) $row['mi_id'];
    $cf_ID      = $row['cf_ID'];
    $mi_incharge = $row['mi_incharge'];

    $period_id = get_period_id($db, $cf_ID);

    if (!$period_id) {
        echo "SKIP: mi_id=$mi_id — could not resolve period_id from cf_ID=$cf_ID\n";
        $skipped++;
        continue;
    }

    $emp_ids = explode(',', $mi_incharge);
    foreach ($emp_ids as $emp_id) {
        $emp_id = (int) trim($emp_id);
        if (!$emp_id) {
            continue;
        }

        // Idempotent: skip if already migrated
        $check = "SELECT id FROM pms_ipcr_si_assignments
                  WHERE success_indicator_id = '$mi_id'
                    AND user_id = '$emp_id'
                    AND period_id = '$period_id'
                  LIMIT 1";
        $check_res = $db->query($check);
        if ($check_res && $check_res->num_rows > 0) {
            $skipped++;
            continue;
        }

        $insert = "INSERT INTO pms_ipcr_si_assignments
                    (success_indicator_id, user_id, period_id, assigned_by, created_at, updated_at)
                   VALUES
                    ('$mi_id', '$emp_id', '$period_id', 9, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())";
        if ($db->query($insert)) {
            $migrated++;
        } else {
            echo "ERROR inserting mi_id=$mi_id emp_id=$emp_id: " . $db->error . "\n";
            $errors++;
        }
    }
}

echo "\n--- Migration Complete ---\n";
echo "Inserted : $migrated\n";
echo "Skipped  : $skipped\n";
echo "Errors   : $errors\n";
echo "</pre>";

$db->close();

// ---------------------------------------------------------------
// Helper: traverse MFO tree upward to find top-level mfo_periodId
// ---------------------------------------------------------------
function get_period_id($db, $cf_ID)
{
    if (!$cf_ID && $cf_ID !== 0) {
        return null;
    }
    $cf_ID = $db->real_escape_string($cf_ID);
    $sql   = "SELECT mfo_periodId, parent_id FROM spms_pcr_mfos WHERE cf_ID = '$cf_ID' LIMIT 1";
    $res   = $db->query($sql);
    if (!$res) {
        return null;
    }
    $row = $res->fetch_assoc();
    if (!$row) {
        return null;
    }
    // If this MFO is a top-level node (no parent), return its period
    if ($row['parent_id'] === '' || $row['parent_id'] === null) {
        return $row['mfo_periodId'];
    }
    // Recurse up the tree
    return get_period_id($db, $row['parent_id']);
}
