<?php 
	$getDepName = "SELECT * FROM `department` where `department_id`='$utlDepartment'";
	$getDepName = $mysqli->query($getDepName);
	$getDepName = $getDepName->fetch_assoc();
	$depName = $getDepName['department'];

	$emp = "SELECT * from `employees` where `department_id`='$utlDepartment'";
	$emp = $mysqli->query($emp);
	$tableRow ="";
	while ($empAr = $emp->fetch_assoc()) {
		$files = "SELECT * from `spms_performancereviewstatus` where `period_id`='$utlPeriod' and `department_id`='$utlDepartment'";
		$files = $mysqli->query($files);
		if($files->num_rows){
			$count = 0;
			while ($fileArr = $files->fetch_assoc()) {
				$count++;
				if($empAr['employees_id']==$fileArr['employees_id']){	
					break;
				}elseif($count==$files->num_rows){
				$tableRow.= "
				    <tr>
				      <td scope='row'>No File</td>
				      <td>$empAr[lastName] $empAr[firstName] $empAr[extName]</td>
				      <td></td>
				      <td style='text-align:center'><img src='assets/image/X.PNG' width='20px'></td>
				      <td style='text-align:center'><img src='assets/image/X.PNG' width='20px'></td>
				      <td style='text-align:center'><img src='assets/image/X.PNG' width='20px'></td>
				     </tr> 
					";								
				}
			}
		}else{
				$tableRow.= "
				    <tr>
				      <td scope='row'>No File</td>
				      <td>$empAr[lastName] $empAr[firstName] $empAr[extName]</td>
				      <td></td>
				      <td style='text-align:center'><img src='assets/image/X.PNG' width='20px'></td>
				      <td style='text-align:center'><img src='assets/image/X.PNG' width='20px'></td>
				      <td style='text-align:center'><img src='assets/image/X.PNG' width='20px'></td>
				     </tr> 
					";								
		}
	}
 ?>
 <div class="container">
 	<div class="jumbotron">
 		<h1 class="display-4">Outcast </h1>
	 	<p class="lead"><?=$depName?></p>
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