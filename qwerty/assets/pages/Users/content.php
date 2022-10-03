<?php
$allPmt = "SELECT * from `employees` left join `spms_accounts` on `employees`.`employees_id`=`spms_accounts`.`employees_id` where `spms_accounts`.`type` like '%PMT%'";
$allPmt = $mysqli->query($allPmt);
$AllpmtRow = "";
while ($rowData = $allPmt->fetch_assoc()) {
  $AllpmtRow .="
  <tr>
  <td>$rowData[lastName]</td>
  <td>$rowData[firstName]</td>
  <td>$rowData[middleName]</td>
  <td>$rowData[type]</td>
  <td>
    <a name='button' href='#gotoUserForm' class='btn btn-primary btnPmtResult' data-family='showDetails' data-id='$rowData[employees_id]'>Details</a>
  </td>
  </tr>
  ";
}
?>
<div class="container">
  <div class="jumbotron">
    <h1 class="display-3">Manage Accounts</h1>
    <p class="lead">
      This account is being used by the the employees that are using IPCR.
      Admin account is very important any intruders can destroy this system.
      If acounts are leaked I the programmer wiil not take responsibility of any damage
    </p>
  </div>
  <div class="input-group">
    <div class="input-group-prepend">
      <span class="input-group-text">Search Employee</span>
    </div>
    <input type="text" class="form-control" id='searchEmp'>
  </div>
  <div id='results' style="position:relative">
  </div>
  <hr id="gotoUserForm">
  <form style="border:1px solid black;border-radius:10px;padding:20px;display:none" name="userForm" >
    <button type="button" class="btn btn-info form-control" name="resetPass">Reset Password</button>
  </form>
  <br>
  <br>
  <div class="table-responsive">
   <nav class="navbar navbar-light bg-light">
    <ul class="nav justify-content-end">
      <li class="nav-item">
        <form class="form-inline my-2 my-lg-0" name="selectDep">
          <select class="form-control mr-sm-2" type="search" name='search'>
                <option value="">PMT</option>
              <?php 
                $sql = "SELECT * from `department`";
                $sql = $mysqli->query($sql);
                while ($option = $sql->fetch_assoc()) {
                  echo "<option value='$option[department_id]'>$option[department]</option>";                  
                }
               ?>
          </select> 
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
      </li>
    </ul>
   </nav>
    <table class="table table-hover">
      <thead style="background:#80808047">
        <tr>
          <td>Last name</td>
          <td>First name</td>
          <td>MI</td>
          <td>Type</td>
          <td></td>
        </tr>
      </thead>
      <tbody id="allEmpShowIndepartment">
        <?=$AllpmtRow?>
      </tbody>
    </table>
  </div>
</div>
<script type="text/javascript">
(function(){
  "use strict";

  document.addEventListener('click',(e)=>{
    try{
      if(e.target.attributes['data-family'].value=="showDetails"){
        selectedEmployee(e);
      }
    }catch(e){
    }
  });
  document.getElementById('searchEmp').addEventListener('keyup',searchEmp);
  var results = document.getElementById("results");
  function searchEmp(){
    var ev = event;
    if(ev.target.value){
      results.innerHTML = "<h5 style='text-align:center;color:#6565658c'><br>fetching...</h5>";
      $.post("?config=Users",{
        searchEmp:ev.target.value,
      },function(data,textStatus,xhr){
        results.innerHTML = data;
        var searchResult = document.getElementsByClassName('searchResult');
        var count = 0;
        while (count<searchResult.length) {
          searchResult[count].addEventListener('click',selectedEmployee);
          count++;
        }
      });
    }else{
      results.innerHTML = "";
    }
  }
  document.forms.userForm.addEventListener('submit',updateUser);
  function updateUser(){
    event.preventDefault();
    var formElements = event.target.elements;
    var type = '';
    if(formElements.reviewer.checked){
      if(type==""){
        type +='Reviewer';
      }else{
        type +=',Reviewer';
      }
    }
    if(formElements.pmt.checked){
      if(type==""){
        type +='PMT';
      }else{
        type +=',PMT';
      }
    }
    if(formElements.matrix.checked){
      if(type==""){
        type +='Matrix';
      }else{
        type +=',Matrix';
      }
    }
    var username = formElements.username.value;
    var empId = formElements.employeeId.value;
    document.forms.userForm.innerHTML  = "<h5 style='text-align:center;color:#6565658c'><br>Connecting to server ....</h5>";
    $.post("?config=Users",{
      updateAccount:empId,
      username:username,
      type:type,
    },function(data,textStatus,xhr){
      if(data){
        refresher(empId);
      }else{
        console.log(data);
      }
    });
  }
  function selectedEmployee(e){
    document.getElementById("results").innerHTML = "";
    document.getElementById('searchEmp').value = "";
    var dataId = e.target.attributes['data-id'].value;
    document.forms.userForm.innerHTML  = "<h5 style='text-align:center;color:#6565658c'><br>Fetching data...</h5>";
    $.post('?config=Users',{
      showFormDetails:dataId,
    },function(data,textStatus,xhr){
      document.forms.userForm.style.display = "";
      document.forms.userForm.innerHTML = data;
      try{
        document.forms.userForm.resetPass.addEventListener('click',resetPass);
      }catch(e){
      }
    })
  }
  function refresher(dataId){
    document.getElementById("results").innerHTML = "";
    document.getElementById('searchEmp').value = "";
    document.forms.userForm.innerHTML  = "<h5 style='text-align:center;color:#6565658c'><br>Fetching data...</h5>";
    $.post('?config=Users',{
      showFormDetails:dataId,
    },function(data,textStatus,xhr){
      document.forms.userForm.style.display = "";
      document.forms.userForm.innerHTML = data;
      document.forms.userForm.resetPass.addEventListener('click',resetPass);
    })
  }
  function resetPass(){
    event.target.disabled = true;
    var dataId = event.target.attributes['data-id'].value;
    $.post('?config=Users',{
      resetPassword:dataId,
    },function(data,textStatus,xhr){
      alert(data);
    });
  }
    document.selectDep.addEventListener('submit',function(e){
      event.preventDefault();
        $.post('?config=Users',{
          searchSelectedDep:e.target.search.value,
        },function(data,textStatus,xhr){
          document.getElementById("allEmpShowIndepartment").innerHTML = data;
        });
    })


}());
</script>
