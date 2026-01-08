<div id="browseCont">
  <?php
  if ($user->authorization) {
    for ($index = 0; $index <= count($user->authorization); $index++) {
      if ($index == count($user->authorization)) {
        echo  Authorization_Error();
      } else if ($user->authorization[$index] == "Matrix") {
  ?>
        <script type="text/javascript">
          $(document).ready(function() {
            $(".dropdown").dropdown({
              fullTextSearch: true
            });
          });
        </script>
        <h1 class="ui center aligned icon header">
          <i class="find icon"></i>
          Browse PCRs
        </h1>
        <?php
        $option = new employees();
        ?>
        <div class="ui inverted segment" style="width:50%;margin:auto;">
          <form class="ui form noSubmit " style="text-align:center" onsubmit="Get_info_browse()">
            <!-- <h1>CITY SOCIAL WELFARE & DEVELOPMENT OFFICE</h1> -->
            <h3>Select Personnel:</h3>
            <div class="field">
              <select class="ui fluid search dropdown" id="browse_emp">
                <?= $option->get_all() ?>
              </select>

              <!-- 
              <table class="ui table fluid">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Department</th>
                    <th>PCR</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>{name here}</td>
                    <td>{department}</td>
                    <td>
                      <button class="ui mini primary button">Open</button>
                    </td>
                  </tr>
                </tbody>
              </table> -->



            </div>
            <h3>In the period of</h3>
            <div class="field">
              <select class="ui fluid search dropdown" id="browse_period">
                <option value="January - June">January - June</option>
                <option value="July - December">July - December</option>
              </select>
            </div>
            <h3>In the year of</h3>
            <div class="field">
              <select class="ui fluid search dropdown" id="browse_year">
                <?= $year->get_year() ?>
              </select>
            </div>
            <br>
            <button class="blue ui fluid button" type="submit" name="button">Find <i class="ui search icon"></i></button>
          </form>
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
  function Get_info_browse() {
    formEl = event.srcElement.elements;
    empName = formEl.browse_emp.value;
    period = formEl.browse_period.value;
    year = formEl.browse_year.value;
    formEl.button.disabled = true;
    xml = new XMLHttpRequest;
    fd = new FormData();
    fd.append('pcrBrowseView', true);
    fd.append('emp', empName);
    fd.append('period', period);
    fd.append('year', year);
    xml.onreadystatechange = function() {
      if (xml.readyState === 4 && this.status === 200) {
        document.getElementById('browseCont').innerHTML = this.responseText;
      }
    }
    xml.open("POST", "?config=BrowseConfig", true);
    xml.send(fd);
  }
</script>