<div id="browseCont2">
  <?php
  if ($user->authorization) {
    for ($index = 0; $index <= count($user->authorization); $index++) {
      if ($index == count($user->authorization)) {
        echo  Authorization_Error();
      } else if ($user->authorization[$index] == "Matrix") {
  ?>
        <h1 class="ui center aligned icon header">
          <i class="find icon"></i>
          Browse PCRs
        </h1>
        <h2 class="ui center aligned icon header">CITY SOCIAL WELFARE & DEVELOPMENT OFFICE</h2>

        <?php
        $option = new employees();
        ?>

        <div class="ui segment" style="width: 50%; margin: auto; margin-bottom: 15px;">
          <table class="ui selectable table ">
            <thead>
              <tr>
                <th>Name</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $color = [
                "DPCR" => "blue",
                "SPCR" => "red",
                "IPCR" => "yellow"
              ];
              foreach ($option->get_all_department($_GET['periodId']) as $key => $pcr) {
              ?>
                <tr>
                  <td style="text-indent: <?= $pcr['level'] * 30 ?>px;">
                    <button class="ui basic mini button <?= $color[$pcr["formType_"]] ?>"><?= $pcr['formType_'] ?></button>
                    <?= $pcr['full_name'] ?>
                    <?php
                    if ($pcr['formType_'] != 'IPCR') {
                      echo "<i class='ui icon arrow down'></i>";
                    }
                    ?>
                  </td>
                  <td>
                    <button class="ui primary button" onclick="openPcr(<?= $pcr['period_id'] . ',' . $pcr['employees_id'] ?>)">Open</button>
                  </td>
                </tr>
              <?php
              }
              ?>
            </tbody>
          </table>
        </div>
  <?php
        break;
      }
    }
  } else {
    echo Authorization_Error();
  }
  ?>
</div>
<script type="text/javascript">
  function openPcr(period_id, employees_id) {
    window.location.href = `/index.php?browse-pcr&periodId=${period_id}&employeeId=${employees_id}`;
  }
</script>