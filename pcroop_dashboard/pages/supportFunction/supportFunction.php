<div class="w3-container">
  <div class="w3-row">
    <div class="w3-quarter">
      <p></p>
    </div>
    <div class="w3-half">
      <br>
      <br>
      <div class="w3-center w3-green w3-padding">
        <h1><i class="fa fa-cogs fa-3x"></i></h1>
        <h1>Support Function</h1>
        <a href="?page=supportList">
          <button type="button" class="w3-button w3-round-large w3-theme-l3 w3-hide-large" name="button"> <i class="fa fa-sign-in"></i> Support List</button>
        </a>
      </div>
      <form class="w3-form  w3-theme-l4 w3-border" method="post" style="padding:5%" id="supportForm">
        <label for="">MFO / PAP</label>
        <textarea class="w3-input w3-theme-l4 w3-border" name="mfo" placeholder="Type here"></textarea>
        <br>
        <label for="">Success Indicator</label>
        <textarea class="w3-input w3-theme-l4 w3-border" name="successIndicator" placeholder="Type here"></textarea>
        <br>
        <label for="">Percent</label>
        <input class="w3-input w3-theme-l4 w3-border" type="text" name="percent" value="" placeholder="Type here">
        <br>
        <label for="" style="display:none">For</label>
        <select class="w3-input w3-theme-l4 w3-border" name="For"  style="display:none">
          <option value="">Employee</option>
          <option value="1">Department Head</option>
        </select>
        <br>
        <ul class="w3-ul w3-hoverable" >
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
        <ul class="w3-ul w3-hoverable" >
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
        <ul class="w3-ul w3-hoverable" >
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
          <button class="w3-button w3-light-blue w3-hover-blue"  style="width:90%" type="submit">Save</button>
        </center>
      </form>
    </div>
    <div class="w3-quarter">
      <p></p>
    </div>
  </div>
</div>
<script type="text/javascript">
function classNames(el){
  return document.getElementsByClassName(el);
}
checker = true;

(function(){
  ListHeaderForm = classNames("ListHeaderForm");
  for (listIndex = 0; listIndex < ListHeaderForm.length; listIndex++) {
    ListHeaderForm[listIndex].addEventListener('click',ListForm);
  }
  document.getElementById("supportForm").addEventListener("submit",supportForm);

})();
function ListForm(){
  list = this.parentElement.children;
  this.disabled = true;
  listIndex=0;
  interval =setInterval(function (){
    listIndex++;
    if(listIndex===list.length){
      clearInterval(interval);
    }else if(list[listIndex].style.display=="none"){
      list[listIndex].style.display="";
    }else{
      list[listIndex].style.display="none";
    }
  }, 20);
  // for (i = 0; i < list.length; i++) {
  //   if(list[listIndex].style.display=="none"){
  //     list[listIndex].style.display="";
  //   }else{
  //     list[listIndex].style.display="none";
  //   }
  // }
}
function supportForm(e){
  e.preventDefault();
  formEl = this.children;
  form = this;
  mfo = formEl.mfo.value;
  successIndicator = formEl.successIndicator.value;
  percent = formEl.percent.value;
  paraKay = formEl.For.value;
  arrQuality = [];
  arrEfficiency = [];
  arrTimeliness = [];
  for (var i = 5; i >=0; i--){
    if(i==5){
      arrQuality.push("");
      arrEfficiency.push("");
      arrTimeliness.push("");
    }else{
      arrQuality.push(this.getElementsByClassName("qualityUl")[i].value);
      arrEfficiency.push(this.getElementsByClassName("efficiencyUl")[i].value);
      arrTimeliness.push(this.getElementsByClassName("timelinessUl")[i].value);
    }
  }
  xml = new XMLHttpRequest();
  fd = new FormData();
  fd.append("supportFuntionAdd",true);
  fd.append("mfo",mfo);
  fd.append("successIndicator",successIndicator);
  fd.append("percent",percent);
  fd.append("paraKay",paraKay);
  fd.append("arrQuality",arrQuality);
  fd.append("arrEfficiency",arrEfficiency);
  fd.append("arrTimeliness",arrTimeliness);
  xml.onreadystatechange = function(){
    if(this.readyState==4){
      if(this.responseText==1){
        alert("Success");
        // alertMsg(form,'success','Successfully Inputted');
        inputs = form.elements;
        for (var inputIn = 0; inputIn < inputs.length; inputIn++){
          inputs[inputIn].value = "";
        }
      }else{
        alert(this.responseText);
        // alertMsg(form,'alert','Something Went Wrong : '+ this.responseText);
      }
    }
  }
  xml.open('POST','?config=supportFunction',true);
  xml.send(fd);
}
</script>
