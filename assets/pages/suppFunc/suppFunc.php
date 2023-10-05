<?php
require_once "config.php";
function bd($ar){
  $ar = unserialize($ar);
  $c = 1;
  $rate = "";
  while($c<=5){
    if($ar[$c]!=null){
      $rate .="<b>".$c."</b> - ".$ar[$c]."<br>";
    }
    $c++;
  }
  return $rate;
}
$sql = "SELECT * from spms_supportfunctions";
$sql = $mysqli->query($sql);
$InputedSupp = "";
if($sql->num_rows>0){
  while ($trow = $sql->fetch_assoc()) {
    if($trow['owner']==1){
      $onwer = "Department Head";
    }else{
      $onwer = "All Employees";
    }
    $InputedSupp .="
    <tr style='font-size:14px'>
    <td class='collapsing'>
    $trow[mfo]
    </td>
    <td class='collapsing'>
    $trow[suc_in]
    </td>
    <td class='collapsing' style='width:300px'>
    ".bd($trow['E'])."
    </td>
    <td class='collapsing' style='width:300px'>
    ".bd($trow['T'])."
    </td>
    <td class='collapsing'>
    $trow[percent]%
    </td>
    <td class='collapsing'>
    $onwer
    </td>
    <td class='collapsing'>
    <i class='ui green circular inverted edit icon' onclick='supportDataFucnEdit($trow[id_suppFunc])'></i>
    <br>
    <br>
    <i class='ui red circular inverted trash icon' onclick='supportDataFucnDelete($trow[id_suppFunc])'></i>
    </td>
    </tr>
    ";
  }
}else{
  $InputedSupp = "
  <tr>
  <td class='collapsing'>
  <i class='exclamation triangle icon'></i>Empty
  </td>
  </tr>
  ";
}
?>
<div class="ui raised very padded text container segment">
  <div class="ui mini icon info message">
    <i class="info icon"></i>
    <div class="content">
      <p>This form is Exclusive only for Me (programmer) this form is for future use only If the system of the city is changed and this data needs to be modified</p>
    </div>
  </div>
  <script type="text/javascript">
  $(document).ready(function() {
    $('.ui.checkbox').checkbox();
    $('.ui.dropdown').dropdown();
  });

  function supportDataFucnEdit(id){
    $("#allModal").modal('show');
  }
  function supportDataFucnDelete(id){
    con = confirm("Are you sure?");
    if(con){
      $.post('?config=supportFunction', {
        removeSupport:id
      }, function(data, textStatus, xhr) {
        if(data==1){
          location.reload(true);
        }else{
          alert();
        }
      });
    }
  }
  function cbutton(i,type){
    check = document.getElementById(i.id).checked;
    if(check == true){
      $("#in_"+type).slideDown(100);
    }else{
      $("#in_"+type).slideUp(100);
    }
  }
  function submitSupportFunction(){
    percent = $("#percent").val();
    owner = $("#owner").val();
    suppMfo = $("#suppMfo").val();
    succIndi = $("#succIndi").val();
    q = ['','','','','',''];
    e = ['','','','','',''];
    t = ['','','','','',''];
    c = 1
    while(c<=3){
      ca = 1;
      if(c==1){
        while(ca<=5){
          q[ca] = $("#input"+ca+"Quality").val();
          ca++;
        }
      }if(c==2){
        while(ca<=5){
          e[ca] = $("#input"+ca+"Efficiency").val();
          ca++;
        }
      }if(c==3){
        while(ca<=5){
          t[ca] = $("#input"+ca+"Timeliness").val();
          ca++;
        }
      }
      c++;
    }
    $.post('?config=supportFunction', {
      StoreSF:true,
      percent:percent,
      owner:owner,
      suppMfo:suppMfo,
      succIndi:succIndi,
      q:q,
      e:e,
      t:t
    }, function(data, textStatus, xhr) {
      if(data==1){
        window.location.href ="?supportFuntion";
      }else{
        alert(data);
      }
    });
  }
  </script>
  <h2 class="ui header">Support Function Form</h2>
  <form class="ui form noSubmit" onsubmit="submitSupportFunction()">
    <div class="field">
      <label>MFO/PAP</label>
      <textarea id='suppMfo'></textarea>
    </div>
    <div class="field">
      <label>Success Indicator</label>
      <textarea id='succIndi'></textarea>
    </div>
    <div class="ui fluid labeled input">
      <div class="ui basic label">Percent</div>
      <input type="number" placeholder="" id='percent'>
      <div class="ui label">%</div>
    </div>
    <br>
    <div class="ui fluid labeled input">
      <div class="ui basic label">For</div>
      <div class="ui fluid selection dropdown">
        <input type="hidden" id='owner'>
        <i class="dropdown icon"></i>
        <div class="default text"></div>
        <div class="menu">
          <div class="item" data-value="" >All Employee</div>
          <div class="item" data-value="1">Administrator Only</div>
        </div>
      </div>
    </div>
    <?=rating('Quality')?>
    <?=rating("Efficiency")?>
    <?=rating("Timeliness")?>
    <input type="submit" class="ui primary basic fluid button" name="" value="Save">
  </form>
</div>
<br>
<br>
<h2 class="ui center aligned icon header">
  <i class="circular cogs icon"></i>
  Support Functions
</h2>
<table border="1px" style="width:95%;margin:auto;border-collapse:collapse">
  <thead>
    <tr>
      <th>MFO / PAP</th>
      <th>Succes Indicator</th>
      <th>Effic iency</th>
      <th>Timeliness</th>
      <th>Percent</th>
      <th>Incharge</th>
      <th>Option</th>
    </tr>
  </thead>
  <tbody style="font-size:14px">
    <?=$InputedSupp?>
  </tbody>
</table>
