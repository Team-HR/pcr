<?php 
	// NOTE:
	// $utlPeroid and $utlDepartment are variables that can be found on departmentFiles.php


	$getDepName = "SELECT * FROM `department` where `department_id`='$utlDepartment'";
	$getDepName = $mysqli->query($getDepName);
	$getDepName = $getDepName->fetch_assoc();
	$depName = $getDepName['department'];
	$sql = "SELECT * from `spms_performancereviewstatus` left join `employees` on `spms_performancereviewstatus`.`employees_id`=`employees`.`employees_id` where `spms_performancereviewstatus`.`period_id`='$utlPeriod' and `spms_performancereviewstatus`.`department_id`='$utlDepartment'";
	$sql = $mysqli->query($sql);
	$tableRow = "";
	$dataId = [];
	$done = "<img src='assets/image/done.PNG' width='20px'>";
	$not = "<img src='assets/image/X.PNG' width='20px'>";
	while($files = $sql->fetch_assoc()){
		$formType = "";
		$reviewImg = $not;
		$dhImg = $not;
		$pmtImg = $not;

		if($files['reviewed']!=""){
			$reviewImg = $done;
		}
		if($files['approved']!=""){
			$dhImg = $done;
		}
		if($files['panelApproved']!=""){
			$pmtImg = $done;
		}
		if($files['formType']==1){
			$formType = "IPCR";
		}elseif($files['formType']==2){
			$formType = "SPCR";
			$reviewImg = $done;
		}elseif($files['formType']==3) {
			$formType = "DPCR(Department Head)";
			$reviewImg = $done;
			$dhImg = $done;
		}elseif($files['formType']==4) {
			$formType = "DPCR(Division)";			
			$reviewImg = $done;
		}elseif($files['formType']==5){
			$formType = "IPCR(National Agencies)";
			$reviewImg = $done;
			$dhImg = $done;
		}
		$form = [$files['employees_id']];
		$form = serialize($form);
		$tableRow.= "
				    <tr>
				      <td scope='row'><a href='?Records=$_GET[Records]&utl=$_GET[utl]&form=$form'>go to</a></td>
				      <td>$files[lastName] $files[firstName] $files[extName]</td>
				      <td>$formType</td>
				      <td style='text-align: center'>$reviewImg</td>
				      <td style='text-align: center'>$dhImg</td>
				      <td style='text-align: center'>$pmtImg</td>
				    </tr> 
		";
		array_push($dataId, $files['employees_id']);
	}

	$dataId = serialize($dataId);
?>
 <div class="container">
 	<div class="jumbotron">
	  <h1 class="display-4">Submitted files</h1>
	  <p class="lead"><?=$depName?></p>
	  <p><a href='?Records=<?=$_GET['Records']?>&utl=<?=$_GET['utl']?>&form=<?=$dataId?>'>View All</a></p>
	</div>
 	<div class="table-responsive">

		<table class="table table-bordered">
		  <thead style="text-align: center">
		    <tr>
		      <th rowspan="2">View</th>
		      <th rowspan="2">Fullname</th>
		      <th rowspan="2">File Type</th>
		      <th colspan="3">Status</th>
		    </tr>
		    <tr >
		    	<td>Reviewed</td>
		    	<td>Approved By DH</td>
		    	<td>Approved By PMT</td>
		    </tr>
		  </thead>
		  <tbody>
		  	<?=$tableRow?>
		  </tbody>
		</table>
 	</div>
 </div>