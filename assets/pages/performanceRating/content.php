<?php
require_once "assets/pages/performanceRating/config.php";
if (isset($_POST['page'])) {
	$page = $_POST['page'];
	$period_id = $_POST['period_id'];
	if ($page == 'coreFunction') {
		$go = "";
		if (isset($_POST['gotoStep'])) {
			$go = $_POST['gotoStep'];
		}

		$user->set_period($period_id);
		$period = $user->get_period('mfoperiod_id');
		$empId = $user->get_emp('employees_id');

		$view = "";

		$table = new table($user->hideCol);
		$table->formType($user->get_status('formType'));
		//step
		if ($user->signatoriesCount < 1) {
			$step->_set('signatories');
		} elseif ($user->core_countTotal > 0 && $user->core_countEmpty > 0) {
			$step->_set('core');
		} elseif ($user->support_countEmpty > 0) {
			$step->_set('support');
		} elseif ($user->get_status('assembleAll') == "0") {
			$step->_set('strategic');
		} elseif ($user->commentCount < 1) {
			$step->_set('comment');
		} elseif ($user->get_status('assembleAll') == "1") {
			$step->_set('final');
		}



		// pagecontent
		if ($user->signatoriesCount < 1 || $go == "signatories") {
			$view = $user->form_signatories();
		} elseif ($user->core_countEmpty > 0 || $go == "core") {
			$table->set_body($user->get_coreView());
			$view = $table->_get();
		} elseif ($user->support_countEmpty > 0 || $go == "support") {
			$table->set_body($user->get_supportView());
			$view = $table->_get();
		} elseif ($user->get_status('assembleAll') == "0" || $go == "strategic") {
			$table->set_body($user->get_strategicView());
			if ($user->strategic_count >= 1) {
				$view .= $table->_get();
			}
			$view .= $user->form_strategicView();
		} elseif ($user->commentCount < 1 || $go == "comment") {
			$view = $user->form_comment();
		} elseif ($user->get_status('assembleAll') == "1" || $go == "final") {
			$user->hideNextBtn();
			$rows = $user->get_strategicView() . $user->get_coreView() . $user->get_supportView();
			$table->set_head($user->tableHeader());
			$table->set_body($rows);
			$table->set_foot($user->tableFooter() . "<br class='noprint'>" . $user->get_approveBTN());
			$view = $table->_get();
		}
		if ($user->core_countTotal > 0) {
			$step->set_disable($user->hideCol);
			echo $step->_get();
			// json_encode($user->update_prrlist()) .
			echo $view;
		} else {

			// echo json_encode($_SESSION["emp_info"]["department_id"]);
			echo "error";

			// echo "
			// <div style='margin:auto;width:500px'>
			// <h2 class='ui icon header'>
			// <i class='ui red exclamation triangle icon'></i>
			// <div class='content'>
			// Rating Scale Required
			// <div class='sub header'>You Dont have Rating Matrix Yet. Please consult your Department Head For this Matter Test
			// </div>
			// </div>
			// </h2>
			// </div>
			// ";





		}
	} elseif (false) {
	} else {
		echo "<center><h1 style='color:#888888de'>Content is not defined</h1></center>";
	}
} elseif (isset($_POST['findP'])) {
	$period = $_POST['period'];
	$year = $_POST['year'];
	$sql = "SELECT * from spms_mfo_period where month_mfo='$period' and year_mfo='$year'";
	$sql = $mysqli->query($sql);
	if (!$sql) {
		echo $mysqli->error;
		die();
	} else {
		$sql = $sql->fetch_assoc();
		$period_id = $sql['mfoperiod_id'];
		$_SESSION['period_pr'] = $period_id;
		$_SESSION['iMatrix_period'] = $sql['mfoperiod_id'];
		echo $period_id;
	}
} elseif (isset($_POST['saveSiData'])) {
	$acc = addslashes($_POST['acc']);
	if ($acc != "") {
		$sql = "INSERT INTO `spms_corefucndata` (`cfd_id`, `type`, `p_id`, `empId`, `actualAcc`, `Q`, `E`, `T`, `remarks`,`percent`)
		VALUES (NULL, '', '$_POST[saveSiData]', '$_SESSION[emp_id]', '$acc', '$_POST[qual]', '$_POST[eff]', '$_POST[time]', '$_POST[remark]','$_POST[perc]')";
		$sql = $mysqli->query($sql);
		if (!$sql) {
			die($mysqli->error);
		} else {
			print(1);
		}
	} else {
		echo "Please Add Your Actual Accomplishments";
	}
} elseif (isset($_POST['RemoveCoreFuncDataPost'])) {
	$sql = "DELETE FROM `spms_corefucndata` WHERE `spms_corefucndata`.`cfd_id` = '$_POST[RemoveCoreFuncDataPost]'";
	$sql = $mysqli->query($sql);
	if (!$sql) {
		die($mysqli->error);
	} else {
		print(1);
	}
} elseif (isset($_POST['EditCoreFuncDataSaveChangesPost'])) {
	$criticInput = $_POST['criticInput'];
	$criticInputTarget = $_POST['criticInputTarget'];
	$dataId = $_POST['EditCoreFuncDataSaveChangesPost'];
	$userId = $_SESSION['emp_id'];
	$sqlCheck = "SELECT * from spms_corefucndata where cfd_id='$dataId'";
	$sqlCheck = $mysqli->query($sqlCheck);
	$sqlCheck = $sqlCheck->fetch_assoc();
	$accCore = $mysqli->real_escape_string($_POST['acc']);
	$effCore = addslashes($_POST['eff']);
	$remarkCore =  $mysqli->real_escape_string($_POST['remark']);
	if ($userId != $sqlCheck['empId']) {
		if ($sqlCheck['critics']) {
			$critics = unserialize($sqlCheck['critics']);
		} else {
			$critics = ['IS' => "", 'DH' => "", 'PMT' => ""];
		}
		$critics[$criticInputTarget] = $criticInput;
		$critics = $mysqli->real_escape_string(serialize($critics));
		$storeArr = [];
		$ar = ["", [], ""];
		$ar[0] = $dataId;
		$ar[2] = date("d-m-Y");
		if ($_POST['acc'] != $sqlCheck['actualAcc']) {
			array_push($ar[1], ["actualAcc", $sqlCheck['actualAcc'], $_POST['acc']]);
		}
		if ($_POST['qual'] != $sqlCheck['Q']) {
			array_push($ar[1], ["Q", $sqlCheck['Q'], $_POST['qual']]);
		}
		if ($_POST['eff'] != $sqlCheck['E']) {
			array_push($ar[1], ["E", $sqlCheck['E'], $_POST['eff']]);
		}
		if ($_POST['time'] != $sqlCheck['T']) {
			array_push($ar[1], ["T", $sqlCheck['T'], $_POST['time']]);
		}
		if ($_POST['remark'] != $sqlCheck['remarks']) {
			array_push($ar[1], ["remarks", $sqlCheck['remarks'], $_POST['remark']]);
		}
		if ($_POST['percEdit'] != $sqlCheck['percent']) {
			array_push($ar[1], ["percent", $sqlCheck['percent'], $_POST['percEdit']]);
		}
		if (count($ar[1]) > 0 || $critics != $sqlCheck['critics']) {
			if ($sqlCheck['supEdit'] != '') {
				$storeArr = unserialize($sqlCheck['supEdit']);
				array_push($storeArr, $ar);
				$storeArr = serialize($storeArr);
				$storeArr = $mysqli->real_escape_string($storeArr);
			} else {
				array_push($storeArr, $ar);
				$storeArr = serialize($storeArr);
				$storeArr = $mysqli->real_escape_string($storeArr);
			}
			$sql = "UPDATE `spms_corefucndata`
							SET `actualAcc` = '$accCore', `Q` = '$_POST[qual]',
							`E` = '$effCore', `T` = '$_POST[time]',
							`remarks` = '$remarkCore', `supEdit` = '$storeArr',
							`critics`='$critics',`percent`='$_POST[percEdit]' WHERE `spms_corefucndata`.`cfd_id` ='$dataId'";
			$sql = $mysqli->query($sql);
			if (!$sql) {
				die($mysqli->error);
			} else {
				print(1);
			}
		} else {
			print(1);
		}
	} else {
		$sql = "UPDATE `spms_corefucndata` SET `actualAcc` = '$accCore', `Q` = '$_POST[qual]', `E` = '$effCore',
		`T` = '$_POST[time]', `remarks` = '$remarkCore',`percent`='$_POST[percEdit]' WHERE `spms_corefucndata`.`cfd_id` ='$dataId'";
		$sql = $mysqli->query($sql);
		if (!$sql) {
			die($mysqli->error);
		} else {
			print(1);
		}
	}
} elseif (isset($_POST['addSuppAccomplishementSave'])) {
	$empId = $_SESSION['emp_id'];
	$period = $_POST['period_id'];
	$parentId = $_POST['addSuppAccomplishementSave'];
	$parent = "SELECT * from `spms_supportfunctions` where `id_suppFunc`='$parentId'";
	$parent = $mysqli->query($parent);
	$parent = $parent->fetch_assoc();
	$qual = $_POST['qual'];
	$eff = $_POST['eff'];
	$time = $_POST['time'];
	$acc = addslashes($_POST['acc']);
	$remark = addslashes($_POST['remark']);
	$sql = "INSERT INTO `spms_supportfunctiondata` (`sfd_id`,`parent_id`,`emp_id`,`period_id`,`accomplishment`, `Q`, `E`, `T`, `remark`,`percent`)
	VALUES (NULL, '$parentId','$empId', '$period', '$acc', '$qual', '$eff', '$time', '$remark','$parent[percent]')";
	$sql = $mysqli->query($sql);
	if (!$sql) {
		die($mysqli->error);
	} else {
		print(1);
		// print($empId."|".$period);
	}
} elseif (isset($_POST['suppFuncRemoveEmpDataPost'])) {
	$empdataId = $_POST['suppFuncRemoveEmpDataPost'];
	$sql = "DELETE FROM `spms_supportfunctiondata` WHERE `sfd_id` = '$empdataId'";
	$sql = $mysqli->query($sql);
	if (!$sql) {
		echo "<h1>Error Message</h1>";
		die($mysqli->error);
	} else {
		print(1);
	}
} elseif (isset($_POST['editSuppAccomplishementdataPost'])) {
	$dataId = $_POST['editSuppAccomplishementdataPost'];
	$acc = addslashes($_POST['acc']);
	$efficiency = $_POST['efficiency'];
	$quality = $_POST['quality'];
	$timeliness = $_POST['timeliness'];
	$remarks = addslashes($_POST['remarks']);
	$sqlCheck = "SELECT * from spms_supportfunctiondata where sfd_id='$dataId'";
	$sqlCheck = $mysqli->query($sqlCheck);
	$sqlCheck = $sqlCheck->fetch_assoc();
	if ($sqlCheck['emp_id'] != $_SESSION['emp_id']) {
		$supAr = [];
		$a = ["", [], ""];
		$a[0] = $dataId;
		$a[2] = date("d-m-Y");
		if ($sqlCheck['accomplishment'] != $acc) {
			array_push($a[1], ['accomplishment', $sqlCheck['accomplishment'], $acc]);
		}
		if ($sqlCheck['Q'] != $quality) {
			array_push($a[1], ['Q', $sqlCheck['Q'], $quality]);
		}
		if ($sqlCheck['E'] != $efficiency) {
			array_push($a[1], ['E', $sqlCheck['E'], $efficiency]);
		}
		if ($sqlCheck['T'] != $timeliness) {
			array_push($a[1], ['T', $sqlCheck['T'], $timeliness]);
		}
		if ($sqlCheck['remark'] != $remarks) {
			array_push($a[1], [$sqlCheck['remark'], $remarks]);
		}

		if ($sqlCheck['supEdit'] != "") {
			$supAr = unserialize($sqlCheck['supEdit']);
			array_push($supAr, $a);
			$supAr = serialize($supAr);
		} else {
			// editng
			array_push($supAr, $a);
			$supAr = serialize($supAr);
		}
		$sql = "UPDATE `spms_supportfunctiondata`
		SET `accomplishment` = '$acc', `Q` = '$quality', `E` = '$efficiency', `T` = '$timeliness', `remark` = '$remarks', `supEdit` = '$supAr'
		WHERE `spms_supportfunctiondata`.`sfd_id` ='$dataId'";
	} else {
		$sql = "UPDATE `spms_supportfunctiondata`
		SET `accomplishment` = '$acc', `Q` = '$quality', `E` = '$efficiency', `T` = '$timeliness', `remark` = '$remarks'
		WHERE `spms_supportfunctiondata`.`sfd_id` ='$dataId'";
	}
	$sql = $mysqli->query($sql);
	if (!$sql) {
		die($mysqli->error);
	} else {
		print(1);
	}
} elseif (isset($_POST['saveStrategicFuncPost'])) {
	$emp = $_SESSION['emp_id'];
	$period = $_POST['period_id'];
	$mfo = addslashes($_POST['mfo']);
	$suc_in = addslashes($_POST['suc_in']);
	$acc = addslashes($_POST['acc']);
	$stratAverage = $_POST['stratAverage'];
	$remark = addslashes($_POST['remark']);
	$noStrat = 0;
	if (isset($_POST['noStrat'])) {
		$noStrat = 1;
	}
	// $quality = $_POST['quality'];
	// $time = $_POST['time'];
	// $sql = "INSERT INTO `spms_strategicfuncdata`
	// (`strategicFunc_id`, `period_id`, `emp_id`, `mfo`, `succ_in`, `acc`, `Q`, `T`, `remark`)
	// VALUES (NULL, '$period', '$emp', '$mfo', '$suc_in', '$acc', '$quality', '$time', '$remark')";
	$sql = "INSERT INTO `spms_strategicfuncdata`
	(`strategicFunc_id`, `period_id`, `emp_id`, `mfo`, `succ_in`, `acc`, `average` , `remark`, `noStrat`)
	VALUES (NULL, '$period', '$emp', '$mfo', '$suc_in', '$acc', '$stratAverage' ,'$remark','$noStrat')";
	$sql = $mysqli->query($sql);
	if (!$sql) {
		die($mysqli->error);
	} else {
		print(1);
	}
} elseif (isset($_POST['strategicDeletePost'])) {
	$dataId = $_POST['strategicDeletePost'];
	$sql = "DELETE FROM `spms_strategicfuncdata` WHERE `strategicFunc_id` ='$dataId'";
	$sql = $mysqli->query($sql);
	if (!$sql) {
		die($mysqli->error);
	} else {
		print(1);
	}
} elseif (isset($_POST['finishperformanceReviewPost'])) {
	$empId = $_SESSION['emp_id'];
	$period = $_POST['period_id'];
	$assembleAll = $_POST['assembleAll'];
	$approved = $_POST['approved'];
	$sql = "UPDATE `spms_performancereviewstatus` SET `assembleAll` = '1', `approved` = '$approved'  WHERE `period_id` = '$period' and `employees_id`='$empId'";
	$sql = $mysqli->query($sql);
	if (!$sql) {
		die($mysqli->error);
	} else {
		print(1);
	}
}

































# submit method
# should also update prrlist from ihris
elseif (isset($_POST['submitPerformance'])) {
	$empId = $_SESSION['emp_id'];
	$period = $_POST['period_id'];
	$date = date("m/d/y");
	$sql = "UPDATE `spms_performancereviewstatus` SET `submitted` = 'Done', `dateAccomplished`='$date'  WHERE `period_id` = '$period' and `employees_id`='$empId'";
	$sql = $mysqli->query($sql);
	if (!$sql) {
		die($mysqli->error);
	} else {
		print(1);
	}
} elseif (isset($_POST['signatoriesAddPost'])) {
	$department = $user->get_emp('department_id');
	$empId = $_SESSION['emp_id'];
	$period = $_POST['period_id'];
	$immediateSup = $_POST['immediateSup'];
	$departmentHead = $_POST['departmentHead'];
	$formType = $_POST['formType'];
	$headAgency =  addslashes(strtoupper($_POST['headAgency']));
	$date = date('m-d-Y');
	$check = "SELECT * FROM spms_performancereviewstatus where period_id='$period' and employees_id='$empId'";
	$check = $mysqli->query($check);
	$countCheck = $check->num_rows;
	if ($countCheck > 0) {
		$check = $check->fetch_assoc();
		// , `department_id` = '$department'
		$sql = "UPDATE `spms_performancereviewstatus`
		SET `ImmediateSup` = '$immediateSup', `DepartmentHead` = '$departmentHead', `HeadAgency` = '$headAgency',`formType`='$formType'
		WHERE `spms_performancereviewstatus`.`performanceReviewStatus_id` = '$check[performanceReviewStatus_id]'";
	} else {
		#
		$sql = "INSERT INTO `spms_performancereviewstatus`
	(`performanceReviewStatus_id`, `period_id`, `employees_id`, `ImmediateSup`, `DepartmentHead`, `HeadAgency`, `PMT`, `submitted`,`panelApproved`, `approved`, `dateAccomplished`,`formType`,`department_id`,`assembleAll`)
	VALUES (NULL, '$period', '$empId', '$immediateSup', '$departmentHead', '$headAgency', '0','','', '', '','$formType','$department','0')";
		#
	}
	$sql = $mysqli->query($sql);
	if (!$sql) {
		die($mysqli->error);
	} else {
		print(1);
	}
} elseif (isset($_POST['commentRecPost'])) {
	$empId = $_SESSION['emp_id'];
	$period = $_POST['period_id'];
	// $com = addslashes($_POST['commentRecPost']);
	$com = $mysqli->real_escape_string($_POST['commentRecPost']);
	$sql = "SELECT * from	spms_commentrec where period_id='$period' and emp_id='$empId'";
	$sql = $mysqli->query($sql);
	$count = $sql->num_rows;
	if ($count > 0) {
		$sql = $sql->fetch_assoc();
		$q = "UPDATE `spms_commentrec` SET `comment` = '$com' WHERE `spms_commentrec`.`comRec_id` ='$sql[comRec_id]' ";
	} else {
		$q = "INSERT INTO `spms_commentrec` (`comRec_id`, `period_id`, `emp_id`, `comment`)
		VALUES ('', '$period', '$empId', '$com')";
	}
	$q = $mysqli->query($q);
	if (!$q) {
		die($mysqli->error);
	} else {
		print(1);
	}
} elseif (isset($_POST['EditStrategicFuncPost'])) {
	$dataId = $_POST['EditStrategicFuncPost'];
	$mfo =  addslashes($_POST['mfo']);
	$suc_in = addslashes($_POST['suc_in']);
	$acc = addslashes($_POST['acc']);
	// $quality = $_POST['quality'];
	// $time = $_POST['time'];
	$remark = addslashes($_POST['remark']);
	$stratAverage = $_POST['stratAverage'];
	// $sql = "UPDATE `spms_strategicfuncdata`
	// SET `mfo` = '$mfo', `succ_in` = '$suc_in', `acc` = '$acc', `Q` = '$quality', `T` = '$time', `remark` = '$remark'
	// WHERE `spms_strategicfuncdata`.`strategicFunc_id` = '$dataId'
	// ";
	$sql = "UPDATE `spms_strategicfuncdata`
	SET `mfo` = '$mfo', `succ_in` = '$suc_in', `acc` = '$acc',`average` = '$stratAverage', `remark` = '$remark'
	WHERE `spms_strategicfuncdata`.`strategicFunc_id` = '$dataId'
	";
	$sql = $mysqli->query($sql);
	if (!$sql) {
		die($mysqli->error);
	} else {
		print(1);
	}
} elseif (isset($_POST['getCommentRecommendationForm'])) {
	$commentsql = "SELECT * from spms_commentrec where period_id='$_POST[commentRecommendationPeriod]' and emp_id='$_POST[commentRecommendationEmpId]'";
	$commentsql = $mysqli->query($commentsql);
	$countRow = $commentsql->num_rows;
	$comment = "";
	if ($countRow) {
		$commentsql = $commentsql->fetch_assoc();
		$comment = $commentsql['comment'];
	}
	$getStatusId = new Employee_data();
	$getStatusId->set_emp($_POST['commentRecommendationEmpId']);
	$getStatusId->set_period($_POST['commentRecommendationPeriod']);
	echo "
	<div class='ui stacked segments'>
	  <div class='ui secondary inverted green segment'>
			<p><b>One last thing we the HR recommend you to fill up this form</b></p>
	  </div>
	  <div class='ui tertiary inverted green segment'>
			<label>Giving your subordinate a verbal comments & recommendation for His/Her overall performance result is a must. This is for His/Her improvement</label>
	  </div>
	</div>
	<form class='ui form' style='width:40%;margin:auto;padding:20px'
	onsubmit='return commentReccomendationOfSupp($_POST[commentRecommendationPeriod],$_POST[commentRecommendationEmpId]," . $getStatusId->get_status('performanceReviewStatus_id') . ")'>
	<div class='field'>
	<label>Comments and Recommendation</label>
	<textarea id='comRec' onkeyup='commentRecInputType()' required>$comment</textarea>
	</div>
	<button class='ui fluid primary button' type='submit' name='commentBtn'>Approve</button>
	</form>
	";
} elseif (isset($_POST["commentReccomendationOfSuppSave"])) {
	$sql = "SELECT * from spms_commentrec where period_id='$_POST[commentReccomendationOfSuppPeriod]' and emp_id='$_POST[commentReccomendationOfSuppEmpId]'";
	$sql = $mysqli->query($sql);
	$com = $mysqli->real_escape_string($_POST['commentReccomendationOfSuppSave']);
	if ($sql->num_rows) {
		$sql = $sql->fetch_assoc();
		$query = "UPDATE `spms_commentrec` SET `comment`='$com' WHERE `spms_commentrec`.`comRec_id` = '$sql[comRec_id]'";
	} else {
		$query = "INSERT INTO `spms_commentrec` (`comRec_id`, `period_id`, `emp_id`, `comment`)
		VALUES (NULL, '$_POST[commentReccomendationOfSuppPeriod]', '$_POST[commentReccomendationOfSuppEmpId]', '$com')";
	}

	// echo $query;
	$query = $mysqli->query($query);
	if (!$query) {
		echo "something went wrong";
	} else {
		echo 1;
	}
} elseif (isset($_POST['editPercent'])) {
	$percent = $_POST['percent'];
	$dataId = $_POST['dataId'];
	$sql = "UPDATE `spms_corefucndata` SET `percent` = '$percent' WHERE `spms_corefucndata`.`cfd_id` = $dataId";
	$sql = $mysqli->query($sql);
	if (!$sql) {
		echo $mysqli->error;
	} else {
		echo 1;
	}
} elseif (false) {
} else {
	echo "<center><h1 style='color:#888888de'>Page Not Found</h1></center>";
}
