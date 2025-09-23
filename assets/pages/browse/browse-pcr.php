<div id="browsePcr"></div>

<script type="text/javascript">
  $(document).ready(function() {
    $(".dropdown").dropdown({
      fullTextSearch: true
    });

    openPcr(<?= $_GET["periodId"] ?>, <?= $_GET["employeeId"] ?>);

  });

  function openPcr(period_id, employees_id) {
    xml = new XMLHttpRequest;
    fd = new FormData();
    fd.append('pcrBrowseView2', true);
    fd.append('period_id', period_id);
    fd.append('emp', employees_id);
    xml.onreadystatechange = function() {
      if (xml.readyState === 4 && this.status === 200) {
        document.getElementById('browsePcr').innerHTML = this.responseText;
      }
    }
    xml.open("POST", "?config=BrowseConfig", true);
    xml.send(fd);
  }
</script>