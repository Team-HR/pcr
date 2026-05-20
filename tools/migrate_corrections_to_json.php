<?php
/**
 * One-shot script: migrate PHP serialized corrections to JSON.
 * Converts corrections in spms_pcr_mfos and spms_pcr_indicators from
 * serialize() format to json_encode() format.
 *
 * Run ONLY after the code has been updated to use json_decode/json_encode.
 */

require_once __DIR__ . '/../_connect.db.php';

$db = new mysqli($host, $user, $password, $database);
$db->set_charset("utf8");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "<pre>";

// Migrate spms_pcr_mfos.corrections
$mfoSql = "SELECT cf_ID, corrections FROM spms_pcr_mfos WHERE corrections IS NOT NULL AND corrections != ''";
$mfoResult = $db->query($mfoSql);
$mfoCount = 0;
$mfoErrors = 0;

if ($mfoResult) {
    while ($row = $mfoResult->fetch_assoc()) {
        $cfId = $row['cf_ID'];
        $corrections = $row['corrections'];

        // Unserialize the PHP data
        $decoded = unserialize($corrections);
        if ($decoded === false && $corrections !== 'b:0;') {
            echo "ERROR: Failed to unserialize MFO cf_ID=$cfId\n";
            $mfoErrors++;
            continue;
        }

        // Re-encode as JSON
        $json = json_encode($decoded);
        $escaped = $db->real_escape_string($json);

        // Update
        $updateSql = "UPDATE spms_pcr_mfos SET corrections = '$escaped' WHERE cf_ID = '$cfId'";
        if ($db->query($updateSql)) {
            $mfoCount++;
        } else {
            echo "ERROR: Failed to update MFO cf_ID=$cfId: " . $db->error . "\n";
            $mfoErrors++;
        }
    }
    echo "MFO: Migrated $mfoCount rows, $mfoErrors errors\n";
} else {
    echo "ERROR: " . $db->error . "\n";
}

// Migrate spms_pcr_indicators.corrections
$siSql = "SELECT mi_id, corrections FROM spms_pcr_indicators WHERE corrections IS NOT NULL AND corrections != ''";
$siResult = $db->query($siSql);
$siCount = 0;
$siErrors = 0;

if ($siResult) {
    while ($row = $siResult->fetch_assoc()) {
        $miId = $row['mi_id'];
        $corrections = $row['corrections'];

        // Unserialize the PHP data
        $decoded = unserialize($corrections);
        if ($decoded === false && $corrections !== 'b:0;') {
            echo "ERROR: Failed to unserialize SI mi_id=$miId\n";
            $siErrors++;
            continue;
        }

        // Re-encode as JSON
        $json = json_encode($decoded);
        $escaped = $db->real_escape_string($json);

        // Update
        $updateSql = "UPDATE spms_pcr_indicators SET corrections = '$escaped' WHERE mi_id = '$miId'";
        if ($db->query($updateSql)) {
            $siCount++;
        } else {
            echo "ERROR: Failed to update SI mi_id=$miId: " . $db->error . "\n";
            $siErrors++;
        }
    }
    echo "SI: Migrated $siCount rows, $siErrors errors\n";
} else {
    echo "ERROR: " . $db->error . "\n";
}

echo "DONE: Total migrated " . ($mfoCount + $siCount) . " rows\n";
echo "</pre>";
$db->close();
