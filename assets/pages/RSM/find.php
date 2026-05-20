<?php
    $type = $_POST['type'];
    $dataId = $mysqli->real_escape_string($_POST['dataId']);
    $search = $mysqli->real_escape_string($_POST['search']);
    $type_map = ['Quality' => 'quality', 'Efficiency' => 'efficiency', 'Timeliness' => 'timeliness'];
    $measure_type = $type_map[$type] ?? 'quality';

    // Find distinct success_indicator_ids whose descriptors match the search term
    $sql = "SELECT DISTINCT success_indicator_id
            FROM pms_si_qet_descriptors
            WHERE measure_type = '$measure_type' AND descriptor LIKE '%$search%'
            ORDER BY success_indicator_id DESC
            LIMIT 100";
    $res = $mysqli->query($sql);

    $seen = [];
    $groups = [];
    while ($row = $res->fetch_assoc()) {
        if (count($groups) >= 3) break;
        $si_id = $row['success_indicator_id'];
        if (in_array($si_id, $seen)) continue;
        $seen[] = $si_id;

        $dres = $mysqli->query("SELECT score, descriptor FROM pms_si_qet_descriptors
                                WHERE success_indicator_id = '$si_id' AND measure_type = '$measure_type'
                                ORDER BY score ASC");
        $scores = [];
        while ($drow = $dres->fetch_assoc()) {
            $scores[(int)$drow['score']] = $drow['descriptor'];
        }
        $groups[] = $scores;
    }

    if (count($groups) > 0) {
        $rslt = "";
        foreach ($groups as $dat) {
            $dats = "";
            for ($s = 5; $s >= 1; $s--) {
                if (isset($dat[$s]) && $dat[$s] != "") {
                    $dats .= "<b>$s</b> - " . htmlspecialchars($dat[$s]) . "<br>";
                }
            }
            $v1 = htmlspecialchars($dat[1] ?? '');
            $v2 = htmlspecialchars($dat[2] ?? '');
            $v3 = htmlspecialchars($dat[3] ?? '');
            $v4 = htmlspecialchars($dat[4] ?? '');
            $v5 = htmlspecialchars($dat[5] ?? '');
            $rslt .= "
                <div class='card'>
                    <div class='content'>
                        <div class='description'>
                            $dats
                        </div>
                    </div>
                    <div class='extra content'>
                    <div class='ui two buttons'>
                        <div class='ui basic green button' onclick='storeToISIinputs(\"||$v1||$v2||$v3||$v4||$v5\",\"{$type}{$_POST['dataId']}\")'>Use</div>
                    </div>
                    </div>
                </div>
            ";
        }
        echo "
            <div class='ui cards'>
                $rslt
            </div>
            <br>
        ";
    } else {
        echo "<h3 style='text-align:center;color:#22242675;border:1px solid #22242626;padding:10px;border-radius:10px 10px 10px 10px'>No suggestions Found</h3>";
    }
?>