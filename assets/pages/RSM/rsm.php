
<div id="rsmCont" style="min-height:500px">
	<?php
	if($user->authorization){
		for ($index = 0; $index <= count($user->authorization) ; $index++) {
			if($index == count($user->authorization)){
				echo	Authorization_Error();
			}
			else if($user->authorization[$index]=="Matrix") {
				?>
				<div style="margin:auto;width:400px;padding-top:50px">
					<h1 class="ui icon header">
						<i class="handshake outline icon"></i>
						<div class="content">
							Rating Scale Matrix
							<div class="sub header">Create or Review the Matrix of your Department</div>
						</div>
					</h1>
				</div>
				<br>
				<br>
				<div style="margin-left: 30%">
					<div class="ui form">
						<div class="two fields">
							<div class="ui four wide field">
								<select id="period">
									<option value="January - June">January - June</option>
									<option value="July - December">July - December</option>
								</select>
							</div>
							<div class="ui four wide field">
								<select id="year">
									<?=$year->get_year()?>
								</select>
							</div>
							<div class="ui four wide field">
								<div class="ui submit button" onclick="rsm_period(this)">Go</div>
							</div>
						</div>
					</div>
				</div>
				<?php
				break;
			}
		}
	}else{
		echo Authorization_Error();
	}
	?>
</div>
