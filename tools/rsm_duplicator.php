<?php

// Minimal test harness for assets/libs/RsmDuplicator.php
$connectFile = __DIR__ . '/../_connect.db.php';
if (!file_exists($connectFile)) {
    http_response_code(500);
    echo "Missing _connect.db.php. Create it based on _connect.db.php.example";
    exit;
}

require_once $connectFile;
require_once __DIR__ . '/../assets/libs/RsmDuplicator.php';

try {
    $dsn = "mysql:host={$host};dbname={$database};charset=utf8";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo "Failed to connect to DB via PDO: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    exit;
}

$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sourcePeriodId = isset($_POST['sourcePeriodId']) && $_POST['sourcePeriodId'] !== '' ? (int)$_POST['sourcePeriodId'] : null;
    $sourceDepId = isset($_POST['sourceDepId']) && $_POST['sourceDepId'] !== '' ? (int)$_POST['sourceDepId'] : null;
    $targetPeriodId = isset($_POST['targetPeriodId']) && $_POST['targetPeriodId'] !== '' ? (int)$_POST['targetPeriodId'] : null;

    $targetDepId = isset($_POST['targetDepId']) && $_POST['targetDepId'] !== '' ? (int)$_POST['targetDepId'] : null;
    $filterInCharge = isset($_POST['filterInCharge']) && $_POST['filterInCharge'] !== '' ? trim((string)$_POST['filterInCharge']) : null;
    $specificCfId = isset($_POST['specificCfId']) && $_POST['specificCfId'] !== '' ? (int)$_POST['specificCfId'] : null;

    if ($sourcePeriodId === null || $sourceDepId === null || $targetPeriodId === null) {
        $result = [
            'status' => 'error',
            'message' => 'sourcePeriodId, sourceDepId, and targetPeriodId are required.',
        ];
    } else {
        $result = duplicateSpmsData(
            $pdo,
            $sourcePeriodId,
            $sourceDepId,
            $targetPeriodId,
            $targetDepId,
            $filterInCharge,
            $specificCfId
        );
    }
}

function h($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RsmDuplicator Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .row { margin-bottom: 10px; }
        label { display: inline-block; width: 180px; }
        input { padding: 6px; width: 260px; }
        button { padding: 8px 14px; }
        pre { background: #f6f8fa; padding: 12px; overflow: auto; }
        .note { color: #555; margin-top: 8px; }
    </style>
</head>
<body>

<h2>RsmDuplicator.php Test Page</h2>

<form method="post">
    <div class="row">
        <label for="sourcePeriodId">Source Period ID *</label>
        <input id="sourcePeriodId" name="sourcePeriodId" type="number" required value="<?= h($_POST['sourcePeriodId'] ?? '') ?>">
    </div>

    <div class="row">
        <label for="sourceDepId">Source Dep ID *</label>
        <input id="sourceDepId" name="sourceDepId" type="number" required value="<?= h($_POST['sourceDepId'] ?? '') ?>">
    </div>

    <div class="row">
        <label for="targetPeriodId">Target Period ID *</label>
        <input id="targetPeriodId" name="targetPeriodId" type="number" required value="<?= h($_POST['targetPeriodId'] ?? '') ?>">
    </div>

    <div class="row">
        <label for="targetDepId">Target Dep ID</label>
        <input id="targetDepId" name="targetDepId" type="number" value="<?= h($_POST['targetDepId'] ?? '') ?>">
        <div class="note">Leave blank to keep original dep_id</div>
    </div>

    <div class="row">
        <label for="filterInCharge">Filter In-Charge (employee_id)</label>
        <input id="filterInCharge" name="filterInCharge" type="text" value="<?= h($_POST['filterInCharge'] ?? '') ?>">
        <div class="note">If set, only copies MIs where mi_incharge contains this ID</div>
    </div>

    <div class="row">
        <label for="specificCfId">Specific Root cf_ID</label>
        <input id="specificCfId" name="specificCfId" type="number" value="<?= h($_POST['specificCfId'] ?? '') ?>">
        <div class="note">If set, duplicates only this CF subtree (as new roots)</div>
    </div>

    <div class="row">
        <button type="submit">Run Duplicate</button>
    </div>
</form>

<?php if ($result !== null): ?>
    <h3>Result</h3>
    <pre><?php echo h(print_r($result, true)); ?></pre>
<?php endif; ?>

</body>
</html>
