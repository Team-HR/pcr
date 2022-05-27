<div class="w3-row w3-padding-64" style="position:relative">
  <div id="EditModal" class="w3-modal">
    <div class="w3-modal-content">
      <span class="w3-button w3-display-topright" id='modalClose' data-target='EditModal'>&times;</span>
      <div class="w3-container w3-padding">
        <form class="w3-container w3-margin" onsubmit="return false">
          <br>
          <label>Full Name</label>
          <input class="w3-input" type="hidden" id='modalEdit_user' readonly >
          <input class="w3-input" type="text" id='modalEdit_fullname' readonly >
          <br>
          <label>Department</label>
          <input class="w3-input" type="text" id='modalEdit_department' readonly >
          <br>
          <label>Username</label>
          <input class="w3-input" type="text" id='modalEdit_username' >
          <br>
          <center>
            <h1>Privileges</h1>
          </center>
          <div class="w3-row" id='privileges'>
            <div class="w3-half">
              <input class="w3-check CreateAccountAuth" type="checkbox" value="Matrix">
              <label>Mother Matrix</label>
            </div>
            <div class="w3-half">
              <input class="w3-check CreateAccountAuth" type="checkbox" value="PMT">
              <label>PMT</label>
            </div>
            <div class="w3-half">
              <input class="w3-check CreateAccountAuth" type="checkbox" value="Reviewer">
              <label>Supervisor / Department Head</label>
            </div>
          </div>
          <hr>
          <button class="w3-button w3-blue" type="Submit" type="button" name="button" id='saveChangesModal' style="width:100%">
            <i>Save Changes</i>
            <i class='fa fa-circle-o-notch fa-spin' style='display:none'></i>
          </button>
        </form>
      </div>
    </div>
  </div>
  <div class="w3-padding">
    <div class="w3-responsive">
      <table class="w3-table-all w3-hoverable">
        <thead>
          <tr class="w3-theme-d4">
            <th colspan="6">
              <h1 class="w3-center w3-opacity">
                <i class="fa fa-users w3-jumbo "></i>
                <br>
                Account List
              </h1>
              <center>
                <a href='?page=createUser' class="w3-hide-large">
                  <button  class="w3-button w3-round-large w3-theme-l3" type="button" name="button" >
                    <i class="fa fa-sign-in"></i>
                    Create User
                  </button>
                </a>
              </center>
            </th>
          </tr>
          <tr>
            <td colspan="6">
              <label for="">Find</label>
              <input type="search" class='w3-input w3-border w3-padding' id='filter' data-target="empRow">
            </td>
          </tr>
          <tr>
            <th><input type='checkbox' id='selectAllUser' data-target='selectedUser'></th>
            <th>Full Name</th>
            <th>Department</th>
            <th>Username</th>
            <th>Privileges</th>
            <th>Options</th>
          </tr>
        </thead>
        <tbody>
          <tr >
            <?=listOfUser()?>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php
// php function ni siya
function listOfUser(){
  global $mysqli;
  $sql = "SELECT * FROM `accounts` left join employees on accounts.employees_id = employees.employees_id left join department on employees.department_id = department.department_id";
  $sql = $mysqli->query($sql);
  $row = "";
  while ($data = $sql->fetch_assoc()) {
    $row .="
    <tr class='empRow' data-target='row$data[acc_id]'>
    <td><input type='checkbox' class='selectedUser' value='$data[acc_id]'></td>
    <td>$data[firstName] $data[middleName] $data[lastName] $data[extName]</td>
    <td>$data[department]</td>
    <td>$data[username]</td>
    <td>".$data['type']."</td>
    <td>
    <button class='w3-button w3-green w3-hover-theme options w3-tooltip' style='width:80px' data-id='$data[acc_id]' data-target='edit'>
    <i class='fa fa-edit mainIcon'></i>
    <i class='fa fa-circle-o-notch fa-spin' style='display:none'></i>
    <span class='w3-text w3-animate-right' style='font-size:10px'>Edit</span>
    </button>
    <button class='w3-button w3-red w3-hover-khaki   options w3-tooltip' style='width:80px;' data-id='$data[acc_id]' data-target='delete'>
    <i class='fa fa-trash mainIcon'></i>
    <i class='fa fa-circle-o-notch fa-spin' style='display:none'></i>
    <span class='w3-text w3-animate-right' style='font-size:10px'>Delete</span>
    </button>
    <button class='w3-button w3-orange w3-hover-yellow options w3-tooltip' style='width:80px' data-id='$data[acc_id]' data-target='reset'>
    <i class='fa fa-key mainIcon'></i>
    <i class='fa fa-circle-o-notch fa-spin' style='display:none'></i>
    <span class='w3-text w3-animate-right' style='font-size:10px'>Reset</span>
    </button>
    </td>
    </tr>
    ";
  }
  return $row;
}
?>
<!-- javascript functions  -->
<script type="text/javascript">
function _(el){
  return document.getElementById(el);
}
// listeners and others
(function()
{
  option_element = document.getElementsByClassName("options");
  for(option_index = 0;option_index<option_element.length;option_index++)
  {
    option_element[option_index].addEventListener("click",loadingOnClick);
    option_element[option_index].addEventListener("click",btnType);
  }
  _('selectAllUser').addEventListener("click", checkall);
  _('filter').addEventListener("keyup", filter);
  _('modalClose').addEventListener("click", modalClose);
  _('saveChangesModal').addEventListener("click", loadingOnClick);
  _('saveChangesModal').addEventListener("click", editAccount);
  document.onkeyup  = function(e)
  {
    if(e.keyCode==27)
    {
      empty();
    }
  }
})();
function modalClose(){
  target = this.attributes['data-target'].value;
  _(target).style.display = "none";
  empty();
}
function empty(){
  el = this;
  _("EditModal").style.display = "none";
  _("modalEdit_user").value ="";
  _("modalEdit_fullname").value ="";
  _("modalEdit_department").value ="";
  _("modalEdit_username").value ="";
  checkBoxes = _("privileges").getElementsByTagName("input");
  for(boxIndex = 0 ; boxIndex < checkBoxes.length ; boxIndex++){
    checkBoxes[boxIndex].checked = false;
  }
  tr = document.getElementsByClassName('empRow');
  activeBtn = window.localStorage.getItem("rowTarget");
  for(index = 0;index<tr.length;index++){
    if(tr[index].attributes["data-target"].value==activeBtn){
      optionBtn = tr[index].children[5].children;
      optionBtn[0].disabled=false;
      optionBtn[0].children[0].style.display="";
      optionBtn[0].children[1].style.display="none";
      break;
    }
  }
  saveChangesBtn = _("saveChangesModal");
  if(saveChangesBtn.disabled){
    saveChangesBtn.children[0].style.display="";
    saveChangesBtn.children[1].style.display="none";
    saveChangesBtn.disabled = false;
  }
  window.localStorage.removeItem("rowTarget");
}
function checkall(e){
  target = this.attributes["data-target"].value;
  target = document.getElementsByClassName(target);
  for(index =0;index<target.length;index++){
    display = target[index].parentElement.parentElement.style.display;
    if(!display){
      if(this.checked){
        target[index].checked = true;
      }else{
        target[index].checked = false;
      }
    }
  }
}
function btnType(){
  btn = this.attributes;
  target = btn['data-target'].value.toUpperCase();
  if(target==='DELETE'){
    con = confirm("Are you sure? This cant be Undone");
    if(con){
      del(this);
    }else{
      this.disabled = false;
      this.children[0].style.display = '';
      this.children[1].style.display = 'none';
    }
  }else if(target==="EDIT"){
    selectedItem = this.parentElement.parentElement;
    window.localStorage.setItem("rowTarget",selectedItem.attributes['data-target'].value);
    element =  this.parentElement.parentElement.children;
    _("modalEdit_user").value = this.attributes['data-id'].value;
    _("modalEdit_fullname").value = element[1].innerHTML;
    _("modalEdit_department").value = element[2].innerHTML;
    _("modalEdit_username").value = element[3].innerHTML;
    privArr = element[4].innerHTML.split(',')
    checkBoxes = _("privileges").getElementsByTagName("input");
    for(index = 0;index<privArr.length;index++){
      for(boxIndex = 0 ; boxIndex < checkBoxes.length ; boxIndex++){
        if(privArr[index].toUpperCase() == checkBoxes[boxIndex].value.toUpperCase()){
          checkBoxes[boxIndex].checked = true;
        }
      }
    }
    _("EditModal").style.display = "block";
  }else if(target=='RESET'){
    resetPass(this);
  }
}
// reset password
function resetPass(dataId){
  xml = new XMLHttpRequest();
  fd = new FormData();
  data = dataId.attributes["data-id"].value;
  fd.append("ResetPassword",data);
  xml.onreadystatechange = function(){
    if(this.readyState==4){
      if(this.responseText==1){
        dataId.disabled = false;
        dataId.children[0].style.display = "";
        dataId.children[1].style.display = "none";
        alert("Done");
      }else{
        alert("Somthing went wrong check Host connection and refresh browser("+this.responseText+")");
      }
    }
  }
  xml.open("POST","?config=userList",true);
  xml.send(fd);
}
// delete
function del(el){
  tr = el.parentElement.parentElement;
  id = btn['data-id'].value;
  xml = new XMLHttpRequest();
  fd = new FormData();
  fd.append("DeleteCreatedUser",id);
  xml.onreadystatechange = function(e){
    if(this.readyState==4){
      response = this.responseText;
      if(response==1){
        tr.remove();
      }else{
        alert("Something went wrong when doing the proccess please check the internet connection and reload the browser else contact HR for this problem("+response+")");
      }
    }
  }
  xml.open("POST","?config=userList",true);
  xml.send(fd);
}
function editAccount(){
  xml = new XMLHttpRequest();
  fd = new FormData();
  typeArr = [];
  fd.append("editAccount",_("modalEdit_user").value);
  fd.append("username",_("modalEdit_username").value);
  type = document.getElementsByClassName("CreateAccountAuth");
  for (var i = 0; i < type.length; i++) {
    if(type[i].checked){
      typeArr.push(type[i].value);
    }
  }
  fd.append("privileges",typeArr);
  xml.onreadystatechange = function(){
    if(this.readyState==4){
      if(this.responseText==1){
        selectedTr = document.getElementsByClassName('empRow');
        for(idx =0 ; idx<selectedTr.length;idx++){
          if(selectedTr[idx].attributes['data-target'].value ==window.localStorage.getItem("rowTarget")){
            selectedTr = selectedTr[idx].children;
            selectedTr[3].innerHTML = _("modalEdit_username").value;
            selectedTr[4].innerHTML =  typeArr;
            break;
          }
        }
        empty();
      }else{
        alert("something went wrong(msg:"+this.responseText+")");
      }
    }
  }
  xml.open("POST","?config=userList",true);
  xml.send(fd);
}
</script>
