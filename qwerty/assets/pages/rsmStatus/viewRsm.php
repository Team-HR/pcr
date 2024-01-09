<?php
$rsmClass = new RsmClass();

$rsmClass->set_period($_GET['period']);
$rsmClass->set_department($_GET['department']);
?>
<br>
<table class="table-bordered border-primary" style="border-collapse:collapse;width:98%;margin:auto;background:white">
    <thead style="background:#00c4ff36;font-size:14px">
        <tr style="text-align:center">
            <th rowspan="2" style="padding:20px">MFO / PAP</th>
            <th rowspan="2">Success Indicator</th>
            <th colspan="3" style="width:40px">Rating Matrix</th>
            <th rowspan="2" style="width:40px">Incharge</th>
        </tr>
        <tr style="font-size:12px">
            <th>Q</th>
            <th>E</th>
            <th>T</th>
        </tr>
    </thead>
    <tbody>
        <?= $rsmClass->get_view() ?>
    </tbody>
</table>