<?php 
	$form = unserialize($_GET['form']);
	$IPCR = new IPCR();
	$IPCR->utl_set($utlPeriod,$utlDepartment);
?>
<div style="background: white;padding:10px">
	<?php 
		foreach ($form as $index) {
			$IPCR->EmpId_set($index);
			$IPCR->set_fileType('performance');
			echo $IPCR->_get();			
			echo "<br>";
			echo "<br>";
		}
	 ?>



</div>