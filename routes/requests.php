<?php


session_start();

require_once "../assets/libs/Db.php";
require_once "../assets/libs/Route.php";

//login route

// sintax: Route::requestData('route_name','path_to_file','method_name');

Route::requestData('login', '../assets/pages/login/AuthController.php','login');



?>