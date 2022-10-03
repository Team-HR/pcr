<?php  
	
	$records = $_GET['Records'];
	$submittedLink = md5('submitted');
	$reviewedLink = md5('reviewed');
	$departmentHeadLink = md5('departmentHead');
	$pmtLink = md5('pmt');
	$unSubmittedLink = md5('unSubmitted');
	$data = "";
	list($utlPeriod,$utlDepartment) = explode('||',$_GET['utl']);
	if(isset($_GET['form'])){
        require_once("assets/pages/Records/form.php");
	}else{
		if($records==$submittedLink){
	        require_once("assets/pages/Records/File/submitted.php");
		}elseif($records==$reviewedLink){
	        require_once("assets/pages/Records/File/reviewed.php");
		}elseif($records==$departmentHeadLink){
	        require_once("assets/pages/Records/File/departmentHead.php");
		}elseif($records==$pmtLink){
	        require_once("assets/pages/Records/File/pmt.php");
		}elseif($records==$unSubmittedLink){
	        require_once("assets/pages/Records/File/unSubmitted.php");
		}else{
	        require_once("assets/pages/Records/content.php");
		}
	}
 ?>
