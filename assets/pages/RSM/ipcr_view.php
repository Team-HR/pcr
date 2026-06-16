<?php
// Standalone clean view of an employee's Individual Rating Scale Matrix (with Q/E/T) for a period.
// Routed via ?config=RSMipcrView&emp=ID[&period=Month&year=YYYY]
require_once __DIR__ . '/../../libs/Db.php';
$db = new Db();
$mysqli = $db->getMysqli();

$emp = isset($_GET['emp']) ? $mysqli->real_escape_string($_GET['emp']) : '';

// Resolve period: prefer explicit period+year from URL, else fall back to session period.
$period_id = isset($_SESSION['period']) ? $_SESSION['period'] : '';
if (isset($_GET['period']) && isset($_GET['year'])) {
  $p = $mysqli->real_escape_string($_GET['period']);
  $y = $mysqli->real_escape_string($_GET['year']);
  $pres = $mysqli->query("SELECT mfoperiod_id FROM spms_periods WHERE month_mfo='$p' AND year_mfo='$y' LIMIT 1");
  if ($pres && $prow = $pres->fetch_assoc()) {
    $period_id = $prow['mfoperiod_id'];
  }
}

$employee = new Employee_data();
$employee->set_emp($emp);
$employee->set_period($period_id);

$emp_name = trim(
  $employee->get_emp('firstName') . ' ' .
    $employee->get_emp('lastName') . ' ' .
    $employee->get_emp('extName')
);
$period_label = $employee->get_period('month_mfo') . ' ' . $employee->get_period('year_mfo');
$department_id = $employee->get_emp('department_id');

// Get department name
$dept_name = '';
if ($department_id) {
  $dept_res = $mysqli->query("SELECT department FROM department WHERE department_id='" . $mysqli->real_escape_string($department_id) . "' LIMIT 1");
  if ($dept_res && $dept_row = $dept_res->fetch_assoc()) {
    $dept_name = $dept_row['department'];
  }
}

$irm = new IRM();
$irm->set_cardi($emp, $period_id, $department_id);
$irm_rows = $irm->get_view();
?>
<!DOCTYPE html>
<html>

<head>
  <title>Individual Rating Scale<?= $emp_name ? ' - ' . htmlspecialchars($emp_name) : '' ?></title>
  <link rel="shortcut icon" href="assets/ico/logo.ico" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="assets/libs/ui/dist/semantic.css">
  <script src="assets/libs/jquery/jquery-3.3.1.min.js"></script>
  <style media="screen">
    body {
      background-color: #f7f7f7;
      padding: 30px;
    }

    .ipcr-wrapper {
      width: 100%;
      max-width: 100%;
      margin: 0 auto;
      background: #fff;
      padding: 24px;
      border-radius: 6px;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.12);
    }

    table {
      background: #fff;
    }

    .ipcr-toolbar {
      text-align: right;
      margin-bottom: 16px;
    }
  </style>
  <style media="print">
    @page {
      size: landscape;
    }

    body {
      background: #fff;
      padding: 0;
    }

    .ipcr-wrapper {
      box-shadow: none;
      max-width: none;
      padding: 0;
    }

    .noprint {
      display: none !important;
    }

    table {
      font-size: 12px;
    }
  </style>
</head>

<body>
  <div class="ipcr-wrapper">
    <div class="ipcr-toolbar noprint">
      <button class="ui small primary button" onclick="window.print()"><i class="print icon"></i> Print</button>
    </div>
    <?php if (!$emp) : ?>
      <h3 style="text-align:center;color:#999">No employee specified.</h3>
    <?php else : ?>
      <table class="ui celled table">
        <thead>
          <tr>
            <th colspan="5" style="padding:20px;text-align:center">
              <?php if ($dept_name) : ?>
                <span style="text-transform:uppercase;font-weight:bold"><?= htmlspecialchars($dept_name) ?></span>
                <br>
              <?php endif; ?>
              <span style="font-size:1.5em;font-weight:bold">Individual Rating Scale</span>
              <br>
              <?= htmlspecialchars($period_label) ?>
              <br>
              <span style="text-transform:capitalize;color:#1976d2"><?= htmlspecialchars($emp_name) ?></span>
            </th>
          </tr>
          <tr>
            <th rowspan="2" style="padding:20px">MFO/ PAP</th>
            <th rowspan="2">Success Indicator</th>
            <th colspan="3" style="text-align:center">Rating Matrix</th>
          </tr>
          <tr style="font-size:12px">
            <th>Q</th>
            <th>E</th>
            <th>T</th>
          </tr>
        </thead>
        <tbody>
          <?= $irm_rows ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</body>

</html>
<?php
exit;
