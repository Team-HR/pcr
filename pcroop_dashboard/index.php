<?php
require_once "assets\personalScripts\umbra.php";
$query = new obj();
if (isset($_GET['login']))
{
  require_once pages("login/main");
}
elseif (isset($_GET['page']))
{
  $page = $_GET['page'];
  require_once pages("structures/head");
  if ($page=="homepage")
  {
  }
  elseif ($page=="createUser"){
    require_once pages("createUser/userForm");
  }
  elseif ($page=="userList"){
    require_once pages("userList/userList");
  }
  elseif ($page=="supportFunction"){
    require_once pages("supportFunction/supportFunction");
  }
  elseif ($page=="supportList"){
    require_once pages("supportList/supportList");
  }
  require_once pages("structures/foot");
}
elseif(isset($_GET['config'])){
    $config = $_GET['config'];
    if($config=="createUser")
    {
      require_once pages("createUser/config");
    }
    elseif ($config == "userList")
    {
      require_once pages("userList/config");
    }
    elseif ($config == "supportFunction")
    {
      require_once pages("supportFunction/config");
    }
    elseif ($config == "supportList")
    {
      require_once pages("supportList/config");
    }
}
?> 
