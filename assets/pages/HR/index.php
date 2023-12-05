<div id='hrHomePageContainer' class="ui segment" style="margin-left: 25px; margin-right: 25px;">
	<h1 class="ui header block">HRMO Dashboard</h1>
	<div class="ui fluid basic segment center aligned">
		<a class="ui button primary" style="padding: 100px; width: 565px;" href="?HR&FinalNumericalRatings">
			<h1><i class="icon percent"></i> Final Numerical Ratings</h1>
		</a>
		<a class="ui button primary" style="padding: 100px; width: 565px;" href="?HR&MFORatings">
			<h1><i class="icon table"></i> MFO Ratings</h1>
		</a>
	</div>

	<?php if ($_SESSION['emp_info']['department_id'] == 32) { ?>
		<h1 class="ui header block">Peer Rating Tools</h1>
		<div class="ui fluid basic segment center aligned">
			<a class="ui button primary" style="padding: 100px; width: 565px;" href="?peerRatingTools&personnelHeirarchy">
				<h1><i class="icon users"></i> Personnel Hierarchy</h1>
			</a>
		</div>
	<?php } ?>

</div>