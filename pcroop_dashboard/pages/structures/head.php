<!DOCTYPE html>
<html>
<title>Performance Commiment Review Dashboard</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="assets\w3css\w3.css">
<link rel="stylesheet" href="assets\font-awesome\4.7.0\css\font-awesome.min.css">
<link rel="stylesheet" href="assets\w3css\mytheme.css">
<script src="assets\personalScripts\umbra.js" charset="utf-8"></script>
<style>
html,body,h1,h2,h3,h4,h5 {font-family: "Raleway", sans-serif}
</style>
<style media="screen and (max-width: 600px)">
  table{
    /* color:red !important; */
    font-size: 10px !important;
  }
</style>
<body class="w3-light-grey">
<!-- Sidebar/menu -->
<nav class="w3-sidebar w3-collapse w3-white w3-animate-left w3-theme-l3" style="z-index:3;width:250px;" id="mySidebar"><br>
  <div class="w3-container w3-row">
    <div class="w3-col w3-bar w3-center">
      <span class="w3-bar-item w3-right w3-hide-medium">
        <img src="assets\IMG\LOGO.png" width="50%" alt="">
      </span>
    </div>
  </div>
  <div class="w3-container w3-row">
    <!-- <div class="w3-col s4">
      <img src="../w3images/avatar2.png" class="w3-circle w3-margin-right" style="width:46px">
    </div> -->
    <div class="w3-col w3-bar w3-center">
      <span>Welcome, <strong>Mike</strong></span><br>
      <!-- <a href="#" class="w3-bar-item w3-button"><i class="fa fa-envelope"></i></a>
      <a href="#" class="w3-bar-item w3-button"><i class="fa fa-user"></i></a>
      <a href="#" class="w3-bar-item w3-button"><i class="fa fa-cog"></i></a> -->
    </div>
  </div>
  <hr>
  <div class="w3-container">
    <h5>Dashboard</h5>
  </div>
  <div class="w3-bar-block">
    <!-- <a href="#" class="w3-bar-item w3-button w3-padding-16 w3-hide-large w3-dark-grey w3-hover-black" onclick="w3_close()" title="close menu"><i class="fa fa-remove fa-fw"></i>  Close Menu</a> -->
    <a href="#" class="w3-bar-item w3-button w3-padding w3-hover-theme"><i class="fa fa-users fa-fw"></i>  Overview</a>
    <a href="#" class="w3-bar-item w3-button w3-padding w3-hover-theme"><i class="fa fa-eye fa-fw"></i>  Views</a>
    <a href="#" class="w3-bar-item w3-button w3-padding w3-hover-theme"><i class="fa fa-users fa-fw"></i>  Traffic</a>
    <a href="#" class="w3-bar-item w3-button w3-padding w3-hover-theme"><i class="fa fa-bullseye fa-fw"></i>  Geo</a>
    <a href="#" class="w3-bar-item w3-button w3-padding w3-hover-theme"><i class="fa fa-diamond fa-fw"></i>  Orders</a>
    <a href="#" class="w3-bar-item w3-button w3-padding w3-hover-theme"><i class="fa fa-bell fa-fw"></i>  News</a>
    <a href="#" class="w3-bar-item w3-button w3-padding w3-hover-theme"><i class="fa fa-bank fa-fw"></i>  General</a>
    <a href="#" class="w3-bar-item w3-button w3-padding w3-hover-theme"><i class="fa fa-history fa-fw"></i>  History</a>
    <a href="#" class="w3-bar-item w3-button w3-padding w3-hover-theme"><i class="fa fa-cog fa-fw"></i>  Settings</a><br><br>
  </div>
</nav>
<!-- Overlay effect when opening sidebar on small screens -->
<div class="w3-overlay w3-hide-large w3-animate-opacity" onclick="w3_close()" style="cursor:pointer" title="close side menu" id="myOverlay"></div>
<!-- Top container -->
<!-- !PAGE CONTENT! -->
<div class="w3-main" style="margin-left:250px">
  <div class="w3-bar w3-theme-d1 w3-large w3-hide-large" style="z-index:4">
    <button class="w3-bar-item w3-button w3-hide-large w3-hover-none w3-hover-text-light-grey" onclick="w3_open();"><i class="fa fa-bars"></i>  Menu</button>
    <span class="w3-bar-item w3-right">
      <img src="assets\IMG\LOGO.png" width="60px" alt="">
    </span>
  </div>
