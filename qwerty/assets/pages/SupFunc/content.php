<?php
function indicationInputs($type){
  $view = "";
  $count = 5;
  while($count>=1){
    $view .= "
      <div class='form-group'>
        <div class='input-group'>
          <div class='input-group-prepend'>
            <span class='input-group-text'>$count =</span>
          </div>
          <textarea class='form-control' aria-label='With textarea' name='$type$count'></textarea>
        </div>
      </div>
    ";
    $count--;
  }
  return $view;
}
?>
<div class="modal fade bd-example-modal-lg" id='Supmodal' tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form style="padding:20px;border:1px solid #00000038;" id='updateSupForm'>
      </form>
    </div>
  </div>
</div>
<div class="container">
  <div style="text-align:center;padding-top:10%;padding-bottom:30%;background-image: url('assets/image/support.png');background-size: 100%;background-repeat: no-repeat;">
    <div style="background:#00000024;width: 450px;margin: auto;padding: 20px">
    <h1>Support Functions</h1>
    <a href="#gotoForm"><button type="button" class="btn btn-primary btn-lg">Form</button></a>
    <a href="#gotoTable"><button type="button" class="btn btn-primary btn-lg">Table</button></a>
    </div>
  </div>
  <br>
  <br id="gotoForm">
  <br>
  <br>
  <br>
  <div class="">
    <form style="padding:20px;border:1px solid #00000038;" name='supportForm'>
      <h2 style="color:#00000038">Support Function Form</h2>
      <hr>
      <input type="hidden" name="supEdit" value="">
      <div class="form-group">
        <label>MFO</label>
        <input type="text" class="form-control" name="mfo" placeholder="MFO" required>
      </div>
      <div class="form-group">
        <label>Success Indicators</label>
        <input type="text" class="form-control" name="successIndicator" placeholder="Success Indicators" required>
      </div>
      <div class="form-group">
        <label>Percentage</label>
        <input type="number" class="form-control" name="percent" placeholder="Percentage" required>
      </div>
      <div class="form-group">
        <label>For:</label>
        <select class="form-control" name="pcrType" required>
          <option></option>
          <option value="1">IPCR</option>
          <option value="2">SPMS / DIVISION</option>
          <option value="3">DPCR</option>
        </select>
      </div>
      <div class="row">
        <div class="col-sm" style="padding:5px">
          <div style="border:1px solid #00000038;padding:5px;border-radius:10px">
            <h4 style="padding:10px">Quality</h4>
            <?=indicationInputs('quality')?>
          </div>
        </div>
        <div class="col-sm" style="padding:5px">
          <div style="border:1px solid #00000038;padding:5px;border-radius:10px">
            <h4 style="padding:10px">Efficiency</h4>
            <?=indicationInputs('efficiency')?>
          </div>
        </div>
        <div class="col-sm" style="padding:5px">
          <div style="border:1px solid #00000038;padding:5px;border-radius:10px">
            <h4 style="padding:10px">Timeliness</h4>
            <?=indicationInputs('timeliness')?>
          </div>
        </div>
      </div>
      <br>
      <button class="btn btn-primary btn-lg form-control" name="submitBtn" type="submit"> Save </button>
    </form>
    <br>
  </div>
  <br id="gotoTable">
  <br>
  <br>
  <br>
  <div style="border:1px solid #00000038;" id="supFunctiongetData">
    <center>
      <h1>Fecthing Data....</h1>
    </center>
  </div>
  </div>
</div>
<script type="text/javascript">
(function(){
  'use strict';
  var c = setInterval(() => {
    if(document.readyState=="complete"){
      getSupportFunction();
      clearInterval(c);
    }
  }, 100);

  document.addEventListener('click', (e)=>{
    var trgt = e.target;
    try {
      if(trgt.attributes['data-umbra-action'].value=="delete"){
        deleteSupFunc(e);
      }else if(trgt.attributes['data-umbra-action'].value=="update"){
        updateSupFunc(e)
      }
    } catch (error) {
      
    }
  });

  document.forms.supportForm.addEventListener('submit',supportSave);
  document.getElementById('updateSupForm').addEventListener('submit',supportSave);

  function getSupportFunction(){
    var xml = new XMLHttpRequest();
    var fd = new FormData();
        fd.append('getSupportFunction',true);
        xml.onload = function(){
          document.getElementById("supFunctiongetData").innerHTML=xml.responseText;
        }
        xml.open('POST',"?config=getSupportFunction");
        xml.send(fd);
  }
  function supportSave(){
    event.preventDefault();
    var el = event.target.elements;
    el.submitBtn.disabled = true;
    el.submitBtn.innerHTML = 'Saving Data ....';
    var xml = new XMLHttpRequest();
    var fd = new FormData();
    var quality = [];
    quality[1] = el.quality1.value;
    quality[2] = el.quality2.value;
    quality[3] = el.quality3.value;
    quality[4] = el.quality4.value;
    quality[5] = el.quality5.value;
    var efficiency = [];
    efficiency[1] = el.efficiency1.value;
    efficiency[2] = el.efficiency2.value;
    efficiency[3] = el.efficiency3.value;
    efficiency[4] = el.efficiency4.value;
    efficiency[5] = el.efficiency5.value;
    var timeliness = [];
    timeliness[1] = el.timeliness1.value;
    timeliness[2] = el.timeliness2.value;
    timeliness[3] = el.timeliness3.value;
    timeliness[4] = el.timeliness4.value;
    timeliness[5] = el.timeliness5.value;
    $.post('?config=SupFunc',{
      saveSup:true,
      supEdit:el.supEdit.value,
      mfo: el.mfo.value,
      successIndicator: el.successIndicator.value,
      pcrType: el.pcrType.value,
      percent: el.percent.value,
      quality: quality,
      efficiency: efficiency,
      timeliness: timeliness,
    },function(data, textStatus, xhr){
      if(data==1){
        el.submitBtn.disabled = false;
        el.submitBtn.innerHTML = 'Save';
        var count = 0;
        while(count<el.length){
          if(el[count]==el.submitBtn){
            console.log("btn");
          }else{
            console.log("not btn");
            el[count].value = "";
          }
          count++;
        }
        getSupportFunction();
        $('#Supmodal').modal('hide')
      }else{
        alert('something went wrong');
      }
    })
  }
  function deleteSupFunc(ev){
    var dataId = ev.target.attributes['data-id'].value;
    var conf = confirm("Are you sure to delete this data?");
    if (conf) {
      ev.target.disabled = true;
      $.post('?config=SupFunc',{
        deleteData:dataId,
      },function(data,textStatus,xhr){
        if(data==1){
          ev.path[2].parentElement.removeChild(ev.path[2])
        }else{
          ev.target.disabled = false;
        }
      });
    }
  }
  var btnDelete = document.getElementsByClassName('update');
  var count = 0;
  while (count < btnDelete.length) {
    btnDelete[count].addEventListener('click',updateSupFunc);
    count++;
  };
  function updateSupFunc(ev){
    var modalCont = document.getElementById('updateSupForm');
    modalCont.innerHTML = "<h4 style='padding:50px;text-align:center'>please wait...</h4>";
    var dataId = ev.target.attributes['data-id'].value;
    $.post('?modal=SupFunc',{
      updateData:dataId,
    },function(data,textStatus,xhr){
      modalCont.innerHTML = data;
    });
  }

}());
</script>
