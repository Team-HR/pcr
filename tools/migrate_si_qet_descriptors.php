<?php
/**
 * Migration script: Creates pms_si_qet_descriptors table and
 * migrates existing mi_quality, mi_eff, mi_time serialized data
 * from spms_pcr_indicators into individual rows.
 *
 * Run once. Safe to re-run (idempotent).
 */

require_once __DIR__ . '/../_connect.db.php';

$db = new mysqli($host, $user, $password, $database);
$db->set_charset("utf8");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "<pre>";

// ---------------------------------------------------------------
// 1. Create pms_si_qet_descriptors table
// ---------------------------------------------------------------
$create_sql = "CREATE TABLE IF NOT EXISTS pms_si_qet_descriptors (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  success_indicator_id bigint(20) unsigned NOT NULL,
  measure_type enum('quality','efficiency','timeliness') NOT NULL,
  score tinyint(4) NOT NULL,
  descriptor text NOT NULL,
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_si_id (success_indicator_id),
  KEY idx_type (measure_type),
  UNIQUE KEY uniq_si_type_score (success_indicator_id, measure_type, score)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (!$db->query($create_sql)) {
    die("Error creating table: " . $db->error . "\n");
}
echo "Table pms_si_qet_descriptors: OK\n";

// ---------------------------------------------------------------
// 2. Migrate existing serialized data
// ---------------------------------------------------------------
$sql    = "SELECT mi_id, mi_quality, mi_eff, mi_time FROM spms_pcr_indicators";
$result = $db->query($sql);

if (!$result) {
    die("Error querying indicators: " . $db->error . "\n");
}

$migrated = 0;
$skipped  = 0;
$errors   = 0;

$type_map = [
    'quality'     => 'mi_quality',
    'efficiency'  => 'mi_eff',
    'timeliness'  => 'mi_time',
];

while ($row = $result->fetch_assoc()) {
    $mi_id = (int) $row['mi_id'];

    foreach ($type_map as $measure_type => $col) {
        $raw = $row[$col];
        if (!$raw) {
            continue;
        }
        $arr = @unserialize($raw);
        if (!is_array($arr)) {
            continue;
        }

        for ($score = 1; $score <= 5; $score++) {
            $descriptor = isset($arr[$score]) ? trim($arr[$score]) : '';
            if ($descriptor === '') {
                continue;
            }

            $esc_desc = $db->real_escape_string($descriptor);
            $insert   = "INSERT IGNORE INTO pms_si_qet_descriptors
                         (success_indicator_id, measure_type, score, descriptor, created_at, updated_at)
                         VALUES
                         ('$mi_id', '$measure_type', '$score', '$esc_desc', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())";

            if ($db->query($insert)) {
                if ($db->affected_rows > 0) {
                    $migrated++;
                } else {
                    $skipped++;
                }
            } else {
                echo "ERROR mi_id=$mi_id type=$measure_type score=$score: " . $db->error . "\n";
                $errors++;
            }
        }
    }
}

echo "\n--- Migration Complete ---\n";
echo "Inserted : $migrated\n";
echo "Skipped  : $skipped\n";
echo "Errors   : $errors\n";
echo "</pre>";

$db->close();
