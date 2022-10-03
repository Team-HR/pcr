<?php
/**
*
*/
class IPCR extends mysqli
{
  function __construct()
  {
    $host = "localhost";
    $password = "";
    $username = "root";
    $database = "hris";
    mysqli::__construct($host,$username,$password,$database);
    mysqli::set_charset("utf8");
  }
  public function get_data($sql){
    // to use this method you need to used foreach
    $query = mysqli::query($sql);
    if($query->num_rows==1){
      $arr = $query->fetch_assoc();
    }else{
      $arr = [];
      while($data = $query->fetch_assoc()){
        array_push($arr,$data);
      }
    }
    return $arr;
  }

}





?>
