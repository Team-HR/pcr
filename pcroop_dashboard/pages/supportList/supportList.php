<!-- The Modal -->
<div id="EditModal" class="w3-modal">
  <div class="w3-modal-content">
    <div class="w3-container">
      <span onclick="closeModal()" data-target = 'EditModal'
      class="w3-button w3-display-topright">&times;</span>
      <h1>Edit Form</h1>
      <hr>
      <form class="w3-padding" method="post" name="supportFuntionForm">
        <br>
        <input type="hidden" name="SupportId" >
        <label for="">MFO / PAP</label>
        <textarea class="w3-input" name="mfoInput" value=""></textarea>
        <br>
        <label for="">Success Indicator</label>
        <textarea class="w3-input" name="succInput"></textarea>
        <br>
        <br>
        <label for="">Percent</label>
        <input class="w3-input" type="number" name="percentInput" value="">
        <br>
        <label for=""  style="display:none">For</label>
        <select class="w3-input" name=""  style="display:none">
          <option value="">Employee</option>
          <option value="1">Department Head</option>
        </select>
        <br>
        <ul class="w3-ul w3-hoverable" name='QualityUl'>
          <li class="ListHeaderForm">
            <h6>Quality</h6>
          </li>
          <li class="w3-row w3-section w3-animate-opacity" style="display:none">
            <div class="w3-col" style="width:50px"><i class="w3-xlarge">5=</i></div>
            <div class="w3-rest">
              <input class="w3-input w3-theme-l4 w3-border qualityUl" type="text">
            </div>
          </li>
          <li class="w3-row w3-section w3-animate-opacity" style="display:none">
            <div class="w3-col" style="width:50px"><i class="w3-xlarge">4=</i></div>
            <div class="w3-rest">
              <input class="w3-input w3-theme-l4 w3-border qualityUl" type="text">
            </div>
          </li>
          <li class="w3-row w3-section w3-animate-opacity" style="display:none">
            <div class="w3-col" style="width:50px"><i class="w3-xlarge">3=</i></div>
            <div class="w3-rest">
              <input class="w3-input w3-theme-l4 w3-border qualityUl" type="text">
            </div>
          </li>
          <li class="w3-row w3-section w3-animate-opacity" style="display:none">
            <div class="w3-col" style="width:50px"><i class="w3-xlarge">2=</i></div>
            <div class="w3-rest">
              <input class="w3-input w3-theme-l4 w3-border qualityUl" type="text">
            </div>
          </li>
          <li class="w3-row w3-section w3-animate-opacity" style="display:none">
            <div class="w3-col" style="width:50px"><i class="w3-xlarge">1=</i></div>
            <div class="w3-rest">
              <input class="w3-input w3-theme-l4 w3-border qualityUl" type="text">
            </div>
          </li>
        </ul>
        <ul class="w3-ul w3-hoverable" name='EfficiencyUl'>
          <li class="ListHeaderForm">
            <h6>Efficiency</h6>
          </li>
          <li class="w3-row w3-section w3-animate-opacity" style="display:none">
            <div class="w3-col" style="width:50px"><i class="w3-xlarge">5=</i></div>
            <div class="w3-rest">
              <input class="w3-input w3-theme-l4 w3-border efficiencyUl" type="text">
            </div>
          </li>
          <li class="w3-row w3-section w3-animate-opacity" style="display:none">
            <div class="w3-col" style="width:50px"><i class="w3-xlarge">4=</i></div>
            <div class="w3-rest">
              <input class="w3-input w3-theme-l4 w3-border efficiencyUl" type="text">
            </div>
          </li>
          <li class="w3-row w3-section w3-animate-opacity" style="display:none">
            <div class="w3-col" style="width:50px"><i class="w3-xlarge">3=</i></div>
            <div class="w3-rest">
              <input class="w3-input w3-theme-l4 w3-border efficiencyUl" type="text">
            </div>
          </li>
          <li class="w3-row w3-section w3-animate-opacity" style="display:none">
            <div class="w3-col" style="width:50px"><i class="w3-xlarge">2=</i></div>
            <div class="w3-rest">
              <input class="w3-input w3-theme-l4 w3-border efficiencyUl" type="text">
            </div>
          </li>
          <li class="w3-row w3-section w3-animate-opacity" style="display:none">
            <div class="w3-col" style="width:50px"><i class="w3-xlarge">1=</i></div>
            <div class="w3-rest">
              <input class="w3-input w3-theme-l4 w3-border efficiencyUl" type="text">
            </div>
          </li>
        </ul>
        <ul class="w3-ul w3-hoverable" name='TimelinessUl'>
          <li class="ListHeaderForm">
            <h6>Timeliness</h6>
          </li>
          <li class="w3-row w3-section w3-animate-opacity" style="display:none">
            <div class="w3-col" style="width:50px"><i class="w3-xlarge">5=</i></div>
            <div class="w3-rest">
              <input class="w3-input w3-theme-l4 w3-border timelinessUl" type="text">
            </div>
          </li>
          <li class="w3-row w3-section w3-animate-opacity" style="display:none">
            <div class="w3-col" style="width:50px"><i class="w3-xlarge">4=</i></div>
            <div class="w3-rest">
              <input class="w3-input w3-theme-l4 w3-border timelinessUl" type="text">
            </div>
          </li>
          <li class="w3-row w3-section w3-animate-opacity" style="display:none">
            <div class="w3-col" style="width:50px"><i class="w3-xlarge">3=</i></div>
            <div class="w3-rest">
              <input class="w3-input w3-theme-l4 w3-border timelinessUl" type="text">
            </div>
          </li>
          <li class="w3-row w3-section w3-animate-opacity" style="display:none">
            <div class="w3-col" style="width:50px"><i class="w3-xlarge">2=</i></div>
            <div class="w3-rest">
              <input class="w3-input w3-theme-l4 w3-border timelinessUl" type="text">
            </div>
          </li>
          <li class="w3-row w3-section w3-animate-opacity" style="display:none">
            <div class="w3-col" style="width:50px"><i class="w3-xlarge">1=</i></div>
            <div class="w3-rest">
              <input class="w3-input w3-theme-l4 w3-border timelinessUl" type="text">
            </div>
          </li>
        </ul>
        <br>
        <center>
          <button class="w3-button w3-blue" style="width:90%" type="submit">Save Changes</button>
        </center>
        <br>
        <br>
        <br>
      </form>
    </div>
  </div>
  <br>
  <br>
  <br>
</div>
<div class="container">
  <br>
  <br>
  <br>
  <table class="w3-table-all w3-hoverable">
    <thead >
      <tr class="w3-theme-d4">
        <td colspan="7" style="text-align:center">
          <h1>
            <i class="fa fa-list-ul fa-3x"></i>
          </h1>
          <h1>Support Function List</h1>
          <a href="?page=supportFunction">
            <button type="button" class="w3-button w3-round-large w3-theme-l3 w3-hide-large" name="button"> <i class="fa fa-sign-in"></i> Support Function Forms</button>
          </a>
        </td>
      </tr>
      <tr class="w3-theme-d2">
        <th>MFO / PAP</th>
        <th>percent</th>
        <th>Success Indicator</th>
        <th style="width:5%">Quality</th>
        <th style="width:20%">Efficiency</th>
        <th style="width:20%">Timeliness</th>
        <th>Option</th>
      </tr>
    </thead>
    <tbody>
      <?=getSupportFunctions()?>
    </tbody>
  </table>
</div>
<?php
function getSupportFunctions(){
  Global $mysqli;
  $sql = "SELECT * FROM `supportfunctions`";
  $sql = $mysqli->query($sql);
  if(!$sql){
    die($mysqli->error);
  }else{
    $view = "";
    while($row = $sql->fetch_assoc()){
      $view .= "<tr id='tableRow$row[id_suppFunc]'>";
      $view .= "<td>$row[mfo]</td>";
      $view .= "<td>$row[percent] %</td>";
      $view .= "<td>$row[suc_in]</td>";
      $view .= "<td>".unser($row['Q'])."</td>";
      $view .= "<td>".unser($row['E'])."</td>";
      $view .= "<td>".unser($row['T'])."</td>";
      $view .= "<td>";
      $view .= "<button class='w3-button w3-light-green w3-hover-green optionBtn' data-target='edit' data-id='$row[id_suppFunc]' data-row = 'tableRow$row[id_suppFunc]' >";
      $view .= "<i class='fa fa-pencil'></i>";
      $view .= "<i class='fa fa-spinner fa-pulse' style='display:none'></i>";
      $view .= "</button>";
      $view .= "<button class='w3-button w3-red w3-hover-pale-red optionBtn' data-target='delete' data-id='$row[id_suppFunc]' data-row = 'tableRow$row[id_suppFunc]'>";
      $view .= "<i class='fa fa-trash'></i>";
      $view .= "<i class='fa fa-spinner fa-pulse' style='display:none'></i>";
      $view .= "</button>";
      $view .= "</td>";
      $view .= "</tr>";
    }
  }
  return $view;
}
?>
<script type="text/javascript">
function _(el){
  return document.getElementById(el);
}
function clssName(el){
  return document.getElementsByClassName(el);
}
(function(){
  optionBtn = clssName('optionBtn');
  for(btnIndex = 0 ; btnIndex < optionBtn.length ; btnIndex++){
    optionBtn[btnIndex].addEventListener('click',loadingOnClick);
    optionBtn[btnIndex].addEventListener('click',actions);
  }

  ul = clssName("ListHeaderForm");
  for(ulIndex = 0 ; ulIndex<ul.length ; ulIndex++){
    ul[ulIndex].addEventListener('click',function(){
      ulChidren = this.parentElement.children;
      for(ulChidrenIndex = 1 ; ulChidrenIndex < ulChidren.length ; ulChidrenIndex++){
        if(ulChidren[ulChidrenIndex].style.display=="none"){
          ulChidren[ulChidrenIndex].style.display = "";
        }else{
          ulChidren[ulChidrenIndex].style.display = "none";
        }
      }
    });
  }
  document.supportFuntionForm.addEventListener("submit",sendEditSupport);
})();
function sendEditSupport(){
  formEvent = event;
  formEvent.preventDefault();
  xml = new XMLHttpRequest();
  fd = new FormData();
  form = this.children;
  fd.append("editSupport", form.SupportId.value);
  fd.append("mfoInput", form.mfoInput.value);
  fd.append("succInput", form.succInput.value);
  fd.append("percentInput", form.percentInput.value);
  q= [];
  e = [];
  t = [];
  for(i = 5 ; i >= 0 ; i--){
    if(i==5){
      q.push("");
      e.push("");
      t.push("");
    }else{
      q.push(form.QualityUl.getElementsByTagName("input")[i].value);
      e.push(form.EfficiencyUl.getElementsByTagName("input")[i].value);
      t.push(form.TimelinessUl.getElementsByTagName("input")[i].value);
    }
  }
  fd.append("Quality",q);
  fd.append("Efficiency",e);
  fd.append("Timeliness",t);
  xml.onreadystatechange = function(){
    if(this.readyState == 4){
      if(this.responseText==1){
        tableRow = _(window.localStorage.getItem("supportListTableRow")).children;
        tableRow[0].innerHTML = form.mfoInput.value;
        tableRow[1].innerHTML = form.percentInput.value+" %";
        tableRow[2].innerHTML = form.succInput.value;
        tableRow[3].innerHTML = rate(q);
        tableRow[4].innerHTML = rate(e);
        tableRow[5].innerHTML = rate(t);
        closeModal();
      }else{
        error = this.responseText;
        alert("Something went wrong!");
      }
    }
  }
  xml.open("POST","?config=supportList",true);
  xml.send(fd);
}
function rate(ar){
  view = "";
  for(index = 0 ; index < ar.length ; index++){
    if(ar[index]){
      view += (index)+" = "+ar[index]+"<br>";
    }
  }
  return view;
}
function actions(){
  window.localStorage.setItem("supportListTableRow",this.attributes['data-row'].value);
  target = this.attributes['data-target'].value.toUpperCase();
  if(target=='DELETE'){
    del(this);
  }else if(target=='EDIT'){
    // document.forms.supportFuntionForm.children.QualityUl.getElementsByTagName('input');
    _("EditModal").style.display="block";
    row = _(this.attributes['data-row'].value);
    row  = row.children;
    modalForm = document.forms.supportFuntionForm.children;
    modalForm.SupportId.value = this.attributes["data-id"].value;
    modalForm.mfoInput.value = row[0].innerHTML;
    modalForm.succInput.value = row[2].innerText;
    modalForm.percentInput.value = row[1].innerText.split(" ")[0];
    modalForm.QualityUl;
    console.log();
    if(row[3].innerHTML!=""){
      eff = row[3].innerText.split('\n');
        index = 0;
        arr = ['','','','','',''];
        for(index = 0 ;index < eff.length ; index++){
          spt = eff[index].split(" = ");
          arr[spt[0]] = spt[1];
        }
        for(i = 4 ;i >= 0 ; i-- ){
          modalForm.QualityUl.getElementsByTagName("input")[i].value = arr[5-i];
      }
    }
    if(row[4].innerHTML!=""){
      eff = row[4].innerText.split('\n');
      index = 0;
      arr = ['','','','','',''];
      for(index = 0 ;index <eff.length ; index++){
        spt = eff[index].split(" = ");
        arr[spt[0]] = spt[1];
      }
      for(i = 4 ;i >= 0 ; i-- ){
        modalForm.EfficiencyUl.getElementsByTagName("input")[i].value = arr[5-i];
      }
    }
    if(row[5].innerHTML!=""){
      eff = row[5].innerText.split('\n');
      index = 0;
      arr = ['','','','','',''];
      for(index = 0 ;index < eff.length ; index++){
        spt = eff[index].split(" = ");
        arr[spt[0]] = spt[1];
      }
      for(i = 4 ;i >= 0 ; i-- ){
        modalForm.TimelinessUl.getElementsByTagName("input")[i].value = arr[5-i];
      }
    }
  }
}
function del(el){
  xml = new XMLHttpRequest();
  fd = new FormData();
  // console.log(el.attributes['data-id'].value);
  fd.append("removeSupportData",el.attributes['data-id'].value);
  con = confirm("Are you sure?This cant be undone");
  if(con){
    xml.onreadystatechange = function(){
      if(this.readyState == 4){
        if(this.responseText==1){
          el.parentElement.parentElement.remove();
        }else{
          alert(this.responseText);
        }
      }
    }
    xml.open('POST','?config=supportList',true);
    xml.send(fd);
  }else{
    el.disabled = false;
    el.children[0].style.display = '';
    el.children[1].style.display = 'none';
  }
}
function closeModal(){
  row = _(window.localStorage.getItem('supportListTableRow'));
  row.lastElementChild.children[0].disabled = false;
  row.lastElementChild.children[0].children[0].style.display = "";
  row.lastElementChild.children[0].children[1].style.display = "none";
  formEl = document.supportFuntionForm.elements;
  for(ind = 0 ; ind<formEl.length ; ind++){
    formEl[ind].value = "";
  }
  // modalId = e.srcElement.attributes['data-target'].value;
  _("EditModal").style.display = "none";
}
</script>
