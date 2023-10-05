<?php

?>
<div class="w3-row w3-padding-64">
  <div class="w3-quarter">
    <p></p>
  </div>
  <div class="w3-half  w3-card  w3-theme-l3">
    <div class="w3-container w3-theme-d4 w3-padding-32">
      <h1 class="w3-center">
        <i class="fa fa-user-plus w3-jumbo "></i>
        <br>
        Create Account
      </h1>
      <center>
        <a href='?page=userList' class="w3-hide-large">
          <button  class="w3-button w3-round-large w3-theme-l3" type="button" name="button" >
          <i class="fa fa-sign-in"></i>
          User List
        </button>
      </a>
      </center>
    </div>
    <form class="" onsubmit="return false" >
      <br>
      <div class="w3-container w3-padding">
        <div class="w3-row w3-section w3-center">
          <div class="w3-col" style="width:50px"><i class="w3-xxlarge fa fa-user"></i></div>
          <div class="w3-rest">
            <?php
            ?>
            <select class="w3-input w3-animate-left w3-theme-l3" id='employee'>
              <?=$query->selectOption("SELECT * from employees")?>
            </select>
          </div>
        </div>
        <div class="w3-row w3-section  w3-center">
          <div class="w3-col" style="width:50px"><i class="w3-xxlarge	fa fa-user-circle-o"></i></div>
          <div class="w3-rest">
            <input class="w3-input w3-animate-left w3-theme-l3" type="text" placeholder="Username" id='username'>
          </div>
        </div>
        <div class="w3-center ">
          <h1><i class="fa fa-unlock"></i></h1>
          <h3>Privileges</h3>
        </div>
        <div class="w3-row ">
          <div class="w3-half">
            <input class="w3-check CreateAccountAuth" type="checkbox" value="Matrix" >
            <label>Mother Matrix</label>
          </div>
          <div class="w3-half">
            <input class="w3-check CreateAccountAuth" type="checkbox" value="PMT" >
            <label>PMT</label>
          </div>
          <div class="w3-half">
            <input class="w3-check CreateAccountAuth" type="checkbox" value="Reviewer" >
            <label>Superisor / Department Head </label>
          </div>
        </div>
        <br>
        <br>
        <br>
      </div>
      <button class="w3-button w3-block w3-theme-d4 w3-hover-theme" id="formSubmitBtn" onclick="CreateForm()"><i id="text">+ Create</i> <i id="loading" class="fa fa-circle-o-notch fa-spin" style="font-size:36px;display:none"></i></button>
    </form>
  </div>
  <div class="w3-quarter">
    <p></p>
  </div>
</div>
<script type="text/javascript">
function _(el){
  return document.querySelector(el);
}
function CreateForm(el){
  el = this.document.activeElement;
  el.disabled = true;
  el.children.loading.style.display = 'block';
  el.children.text.style.display = 'none';
  xml = new XMLHttpRequest();
  fd = new FormData();
  employee = _('#employee').value;
  username = _('#username').value;
  type = document.getElementsByClassName('CreateAccountAuth');
  auth = [];
  for(i=0;i<type.length;i++){
    if(type[i].checked){
      auth.push(type[i].value);
    }
  }
  fd.append('employee',employee);
  fd.append('username',username);
  fd.append('createUser',auth);
  xml.onreadystatechange = function(e){
    if(this.readyState==4){
      data = this.responseText;
      el.disabled= false;
      el.children.loading.style.display = 'none';
      el.children.text.style.display = 'block';
      parentChilds = el.parentNode;
      if(data==1){
        alert('success');
      }else{
        alert('somthing is wrong');
      }
    }
  }
  // xml.open("POST","http://www.facebook.com",true);
  xml.open("POST","?config=createUser",true);
  xml.send(fd);
}
</script>
