<?php 
	$year = Date('Y');
	$month = Date('m');	
	if($month>=7){ // the date now is 7-12 means july - december in months
		$month = "January - June";

	}else{ // month is 1 - 6 or january - june
		$year--;
		$month = "July - December";
	}
	$period = "SELECT `mfoperiod_id` from `spms_mfo_period` where `month_mfo`='$month' and `year_mfo`='$year'";
	$period = $mysqli->query($period);
	$period = $period->fetch_assoc();
	$period = $period['mfoperiod_id'];
	$sqlSelectDep = "SELECT * from `department`";
	$sqlSelectDep = $mysqli->query($sqlSelectDep);
	$table = "";
	while ($tableAr = $sqlSelectDep->fetch_assoc()) {		
		$EmpInDepartment = "SELECT * from `employees` where `employees`.`department_id`='$tableAr[department_id]'";
		$EmpInDepartment = $mysqli->query($EmpInDepartment);
		$count = $EmpInDepartment->num_rows;
		$submitted = 0;
		$unSubmitted = 0;
		$reviewed = 0;
		$departmentHead = 0;
		$pmt = 0;
		while ($empArr = $EmpInDepartment->fetch_assoc()) {
			$submittedForms = "SELECT * from `spms_performancereviewstatus` where `period_id`='$period' and `employees_id`='$empArr[employees_id]'";
			$submittedForms = $mysqli->query($submittedForms);
			if($submittedForms->num_rows>0){
				$submittedForms = $submittedForms->fetch_assoc();
				if($submittedForms['submitted']!=""){
					$submitted++;
				}else{
					$unSubmitted++;
				}

				if($submittedForms['reviewed']!=""||$submittedForms['formType']==3||$submittedForms['formType']==2||$submittedForms['formType']==5){
					$reviewed++;	
				}
				if($submittedForms['approved']!=""||$submittedForms['formType']==3||$submittedForms['formType']==5){
					$departmentHead++;	
				}
				if($submittedForms['panelApproved']!=""){
					$pmt++;	
				}
			}else{
					$unSubmitted++;
			}
		}
			$submittedBtn = "";
			$unSubmittedBtn = "";
			$reviewedBtn = "";
			$departmentHeadBtn = '';
			$pmtBtn = '';

			if($submitted==0){
					$submittedBtn = 'disabled';
			}
			if($unSubmitted==0){
					$unSubmittedBtn = 'disabled';
			}
			if($reviewed==0){
					$reviewedBtn = 'disabled';
			}
			if($departmentHead==0){
					$departmentHeadBtn = 'disabled';
			}
			if($pmt==0){
					$pmtBtn = 'disabled';
			}
		$submittedLink = md5('submitted');
		$reviewedLink = md5('reviewed');
		$departmentHeadLink = md5('departmentHead');
		$pmtLink = md5('pmt');
		$unSubmittedLink = md5('unSubmitted');

		$table .= "
	  		<table class='table table-bordered table-dark' width='20px'>
			  <thead>
			    <tr>
			      <th scope='col' colspan='4'>$tableAr[department]( Total employee/s = $count)</th>
			    </tr>
			  </thead>
			  <tbody>
			    <tr>
			      <th>Submitted</th>
			      <td colspan='2'>$submitted</td>
			      <td><button data-target='$submittedLink' data-utl='$period||$tableAr[department_id]' type='button' class='btn btn-light viewBtn' $submittedBtn>View</button></td>
			    </tr>
			    <tr>
			      <th></th>
			      <th>REVIEWED</th>
			      <td>$reviewed</td>
			      <td><button data-target='$reviewedLink' data-utl='$period||$tableAr[department_id]' type='button' class='btn btn-light viewBtn' $reviewedBtn>View</button></td>
			    </tr>
			    <tr>
			      <th></th>
			      <th>APPROVED BY DEPARTMENT HEAD</th>
			      <td>$departmentHead</td>
			      <td><button data-target='$departmentHeadLink' data-utl='$period||$tableAr[department_id]' type='button' class='btn btn-light viewBtn' $departmentHeadBtn>View</button></td> 
			    </tr>
			    <tr>
			      <th></th>
			      <th>Approved by PMT</th>
			      <td>$pmt</td>
			      <td><button data-target='$pmtLink' data-utl='$period||$tableAr[department_id]' type='button' class='btn btn-light viewBtn' $pmtBtn>View</button></td>
			    </tr>
			    <tr>
			      <th>Not submitted</th>
			      <td colspan='2'>$unSubmitted</td>
			      <td><button data-target='$unSubmittedLink' data-utl='$period||$tableAr[department_id]' type='button' class='btn btn-light viewBtn' $unSubmittedBtn>View</button></td>
			    </tr>
			  </tbody>
			</table>
		";
	}
?>
<div class="container">
	<div class="jumbotron">
	<h3>Performance Commitment Review Progress</h3>
	<h5>
	<?=$year?>
	<?=$month?>
	</h5>
	</div>
	<?=$table?>
</div>
	<script type="text/javascript">
		(function(){
			'use strict';
			var viewBtn = document.getElementsByClassName('viewBtn');
			var count_viewBtn = 0;
			while(count_viewBtn<viewBtn.length){				
				viewBtn[count_viewBtn].addEventListener('click',linkTopage);

				count_viewBtn++;
			}
			function linkTopage(){
				var el = event.target.attributes;
				var dataTarget = el['data-target'].value;
				var dataUtl = el['data-utl'].value;
				window.open('?Records='+dataTarget+'&utl='+dataUtl,'_blank');
				console.log(el);
			}
		})();
	</script>
