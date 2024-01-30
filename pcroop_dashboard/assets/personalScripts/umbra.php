<?php
// $mysqli = new mysqli("localhost","root","","hris");
require_once "Db.php";

$db = new Db();
$mysqli = $db->getMysqli();

class obj extends Db
{
  private $emp;
  function __construct()
  {
    parent::__construct();
  }

  public function selectOption($sql)
  {
    $option = "<option value='' disabled selected>--select--</option>";
    $SQL = $this->mysqli->query($sql);
    if (!$SQL) {
      die("error:$sql");
    }
    while ($op = $SQL->fetch_assoc()) {
      $option .= "<option value='$op[employees_id]' >$op[firstName] $op[lastName]</option>";
    }
    return $option;
  }
}
// function for code shortcuts
function pages($val)
{
  return "pages/" . $val . ".php";
}
function unser($data)
{
  $data = unserialize($data);
  $view = "";
  for ($i = 0; $i < count($data); $i++) {
    if ($data[$i]) {
      $view .= $i . " = $data[$i]<br>";
    }
  }
  return $view;
}
