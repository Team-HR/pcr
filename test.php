<?php
$serial = 'a:3:{i:0;a:3:{i:0;s:5:"24380";i:1;a:4:{i:0;a:3:{i:0;s:7:"percent";i:1;s:1:"5";i:2;s:1:"1";}i:1;a:3:{i:0;s:9:"actualAcc";i:1;s:70:"250/1,200 (79% below) heads of ducklings dispersed by EO December 2022";i:2;s:71:"250/1,200 (79% below) heads of ducklings dispersed by EO December 2022s";}i:2;a:3:{i:0;s:1:"E";i:1;s:1:"5";i:2;s:1:"2";}i:3;a:3:{i:0;s:1:"T";i:1;s:1:"3";i:2;s:1:"2";}}i:2;s:10:"03-05-2023";}i:1;a:3:{i:0;s:5:"24380";i:1;a:1:{i:0;a:3:{i:0;s:1:"E";i:1;s:1:"2";i:2;s:1:"4";}}i:2;s:10:"03-05-2023";}i:2;a:3:{i:0;s:5:"24380";i:1;a:4:{i:0;a:3:{i:0;s:7:"percent";i:1;s:1:"1";i:2;s:1:"5";}i:1;a:3:{i:0;s:9:"actualAcc";i:1;s:71:"250/1,200 (79% below) heads of ducklings dispersed by EO December 2022s";i:2;s:78:"250/1,200 (79% below) heads of ducklings dispersed by EO December 2022 testing";}i:2;a:3:{i:0;s:1:"E";i:1;s:1:"4";i:2;s:1:"3";}i:3;a:3:{i:0;s:1:"T";i:1;s:1:"2";i:2;s:1:"4";}}i:2;s:10:"03-05-2023";}}';

$unserial = unserialize($serial);

echo date("d-m-Y");
echo "<br>";
echo json_encode($unserial);
