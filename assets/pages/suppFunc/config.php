<?php
if(isset($_POST['StoreSF'])){
  $percent = $_POST['percent'];
  $owner = $_POST['owner'];
  $suppMfo = addslashes($_POST['suppMfo']);
  $suppMfo = addslashes($_POST['suppMfo']);
  $succIndi = addslashes($_POST['succIndi']);
  $q = addslashes(serialize($_POST['q']));
  $e = addslashes(serialize($_POST['e']));
  $t = addslashes(serialize($_POST['t']));
  $sql = "INSERT INTO
            `spms_supportfunctions` (`id_suppFunc`, `mfo`, `suc_in`, `Q`, `E`, `T`, `percent`,`owner`)
            VALUES (NULL, '$suppMfo', '$succIndi', '$q', '$e', '$t', '$percent','$owner')";
  $sql = $mysqli->query($sql);
  if(!$sql){
    die($mysqli->error);
  }else{
    print(1);
  }
}else if(isset($_POST['removeSupport'])){
  $sql = "DELETE FROM `spms_supportfunctions` WHERE `spms_supportfunctions`.`id_suppFunc` = '$_POST[removeSupport]'";
  $sql = $mysqli->query($sql);
  if(!$sql){
    die($mysqli->error);
  }else{
    print(1);
  }

}
function rating($type){
  $view = "
  <div class='ui segment'>
    <div class='field'>
      <div class='ui toggle checkbox'>
        <input type='checkbox'  class='hidden' id='cb$type' onchange='cbutton(this,\"$type\")'>
        <label>$type</label>
      </div>
    </div>
  </div>
  <span  id='in_$type' style='display:none'>
  <div class='ui piled segment'>
  <div class='field'>
  <label>$type</label>
  <div class='ui labeled input'>
  <div class='ui label basic'>5 =</div>
  <input type='text' placeholder='' id='input5$type'>
  </div>
  <div class='ui labeled input'>
  <div class='ui label basic'>4 =</div>
  <input type='text' placeholder='' id='input4$type'>
  </div>
  <div class='ui labeled input'>
  <div class='ui label basic'>3 =</div>
  <input type='text' placeholder='' id='input3$type'>
  </div>
  <div class='ui labeled input'>
  <div class='ui label basic'>2 =</div>
  <input type='text' placeholder='' id='input2$type'>
  </div>
  <div class='ui labeled input'>
  <div class='ui label basic'>1 =</div>
  <input type='text' placeholder='' id='input1$type'>
  </div>
  </div>
  </div>
  </span>
  ";
  return $view;
}
 ?>
