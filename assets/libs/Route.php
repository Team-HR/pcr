<?php
class Route {
    public function __construct() {
    }

    public static function getPage($link,$page)
    {
        if(isset($_GET[$link])){
             return require_once $page;
        }  
    }

    public  static function requestData($link,$file,$method = "render"){

        if(isset($_GET[$link])){
            require_once $file;
            
            // genrate the class name from the file name
            $array = explode('/',$file);
            $name = $array[count($array)-1];
            $name = explode('.',$name);
            $name = $name[0];
    

            // render the class
            $class = new $name();
            $class->$method();
            return $class;

        }

    }
}


?>

