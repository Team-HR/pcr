<?php
$mysqli = new mysqli("localhost","root","","hris");
class obj extends mysqli
{
  private $emp;
  function __construct()
  {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $database = "hris";
    return parent::__construct($host,$user,$pass,$database);
  }
  public function selectOption($sql){
    $option = "<option value='' disabled selected>--select--</option>";
    $SQL = parent::query($sql);
    if(!$SQL){
      die("error:$sql");
    }
    while ($op = $SQL->fetch_assoc()) {
      $option .= "<option value='$op[employees_id]' >$op[firstName] $op[lastName]</option>";
    }
    return $option;
  }
}
// function for code shortcuts
function pages($val){
    return "pages/" . $val . ".php";
}
function unser($data){
    $data = unserialize($data);
    $view = "";
    for ($i=0; $i < count($data) ; $i++) {
        if($data[$i]){
            $view .= $i . " = $data[$i]<br>";
        }
    }
    return $view;
}
?>
