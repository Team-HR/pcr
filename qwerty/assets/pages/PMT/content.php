<?php
$sql = "SELECT * from `spms_accounts` left join `employees` on `spms_accounts`.`employees_id`=`employees`.`employees_id` where `type` like '%PMT%'";
$sql = $mysqli->query($sql);
$col = "";
$index = 0;
while ($a = $sql->fetch_assoc()) {
  $assignSql = "SELECT * from `spms_departmentassignedtopmt` 
                left join `department` on `spms_departmentassignedtopmt`.`department_id`=`department`.`department_id`
                where `employees_id`='$a[employees_id]'";
  $assignSql = $mysqli->query($assignSql);
  echo $mysqli->error;
  $assignedDep = "";
  while ($dep = $assignSql->fetch_assoc()) {
    $assignedDep .= "
    <li class='list-group-item d-flex justify-content-between align-items-center'>
    $dep[department]
    <button type='button' class='close' aria-label='Close' data-id='$dep[departmentAssignedToPMT_id]'><span aria-hidden='true'>×</span></button>
    </li>
    ";
  }

  $col .= "<div class='col-md-4 col-sm-6 pmtCard'>
    <div class='card'>
      <div class='accordion' id='accordionExample{$index}'>
        <div class='accordion-item'>
          <h2 class='accordion-header' id='headingOne{$index}'>
            <button class='accordion-button' type='button' data-bs-toggle='collapse' data-bs-target='#collapseOne{$index}'
                    aria-expanded='true' aria-controls='collapseOne{$index}'>
              <h5>$a[lastName] $a[firstName] $a[middleName]</h5>
            </button>
          </h2>
          <div id='collapseOne{$index}' class='accordion-collapse collapse' aria-labelledby='headingOne{$index}'
              data-bs-parent='#accordionExample{$index}'>
            <div class='accordion-body'>
              <div class='form-group'>
                <div class='input-group mb-3'>
                  <div class='input-group-prepend'>
                    <span class='input-group-text'>Search</span>
                  </div>
                  <input type='text' class='form-control searchDep'>
                </div>
                <div class='result' style='position:relative;' data-id='$a[employees_id]'></div>
              </div>
              <ul class='list-group' data-id='$a[employees_id]'>
                $assignedDep
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>";

  $index += 1;
}
?>
<div class="jumbotron" style="background-image:url('assets/image/pmt.png');background-size:100% 100%;background-repeat: no-repeat;">
  <div style="background:#000000c7;width:95%;margin:auto;color:#58ec7a;padding:30px">
    <h5 class="display-5" style="color: white">Assign a department to PMT.</h5>
    <p class="lead"></p>
    <hr class="m-y-md">
    <p>If user is not found. Go to <a href="?Users">"Manage User"</a>search the name of the employee and check PMT checkbox then click update</p>
  </div>
</div>
<div class="row" style="padding:20px">
  <?= $col ?>
</div>
<div class="container" id='unassignDep'>
  <h6 style="text-align:center">Loading..</h6>
</div>
<script type="text/javascript">
  (function() {
    "use strict";
    var searchDep = document.getElementsByClassName('searchDep');
    var count = 0;
    while (count < searchDep.length) {
      searchDep[count].addEventListener('keyup', showDepAvialable);
      count++;
    }

    function showDepAvialable() {
      var path = event.composedPath()
      //event.path[2].children[1];
      // Check if path[2] exists first
      if (path[2] && path[2].children[1]) {
        const displayTo = path[2].children[1];
        var el = event.target.value;
        if (el.length >= 3) {
          $.post('?config=PMT', {
            searchDep: el,
          }, function(data, textStatus, xhr) {
            displayTo.innerHTML = data;
            var searchResult = document.getElementsByClassName('searchResult');
            var count = 0;
            while (count < searchResult.length) {
              searchResult[count].addEventListener('click', selectedSearchResult);
              count++;
            }
          });
        } else {
          displayTo.innerHTML = "";
        }
      }
    }

    function selectedSearchResult() {

      var path = event.composedPath()

      if (path[2] && path[2].attributes['data-id'].value) {

        var empId = path[2].attributes['data-id'].value;
        console.log('PMT ID:', empId);

        var depToAssign = event.target.attributes['data-id'].value;
        console.log('depToAssign:', depToAssign);

        var targetFile = path[4];
        path[2].innerHTML = "";

        $.post('?config=PMT', {
          assignDepartmentToPMT: empId,
          depToAssign: depToAssign,
        }, function(data, textStatus, xhr) {
          refreshCard(empId, targetFile);
        });

      }

    }

    function refreshCard(empId, targetFile) {
      var targetFile = targetFile.children[1];
      $.post('?config=PMT', {
        refreshCard: empId,
      }, function(data, textStatus, xhr) {
        targetFile.innerHTML = data;
        var closeBbtnList = targetFile.getElementsByClassName('close');
        var count = 0;
        while (count < closeBtnList.length) {
          closeBtnList[count].addEventListener('click', removeList);
          count++;
        }
        unassignDep();
      });
    }
    var closeBtnList = document.getElementsByClassName('close');
    var count = 0;
    while (count < closeBtnList.length) {
      closeBtnList[count].addEventListener('click', removeList);
      count++;
    }

    function removeList() {
      var elements = event.composedPath();
      if (elements[3] && elements[3]?.attributes['data-id']?.value) {
        var empId = elements[3].attributes['data-id'].value;
        var dataId = elements[1].attributes['data-id'].value;
        $.post('?config=PMT', {
          deleteAssignDep: dataId,
        }, function(data, textStatus, xhr) {
          refreshCard(empId, elements[4]);
        });
      }
    }

    document.addEventListener('keyup', keyEvents);

    function keyEvents() {
      var key = event.keyCode;
      var count = 0;
      if (key == 27) {
        var result = document.getElementsByClassName('result');
        while (count < result.length) {
          result[count].innerHTML = "";
          count++;
        }
      }
    }

    function unassignDep() {
      var container = document.getElementById('unassignDep');
      $.post('?config=PMT', {
        unassignDep: true,
      }, function(data, textStatus, xhr) {
        if (data) {
          container.style.display = '';
          container.innerHTML = data;
        } else {
          container.style.display = 'none';
        }
      });
    }

    var pmtHiddenToggle = document.getElementsByClassName('pmtHiddenToggle');
    var count = 0;
    while (count < pmtHiddenToggle.length) {
      pmtHiddenToggle[count].addEventListener('click', pmtHiddenTogglefunc);
      count++;
    }

    function pmtHiddenTogglefunc() {
      var targetCont = event.target.attributes['data-content'].value;
      // targetCont = event.path; //[3].getElementsByClassName(targetCont);
      console.log(targetCont);

      return false
      if (targetCont[0].style.display) {
        targetCont[0].style.display = "";
      } else {
        targetCont[0].style.display = "none";
      }
    }
    var checkpageReady = setInterval(function() {
      if (document.readyState == 'complete') {
        unassignDep();
        clearInterval(checkpageReady);
      }
    }, 1000);
  }())
</script>