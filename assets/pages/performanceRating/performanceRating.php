<div id="performanceRatingBody">
	<div style="margin:auto;width:500px;padding-top:50px">
		<h1 class="ui icon header">
			<i class="book icon"></i>
			<div class="content">
				Performance Commitment & Review
				<div class="sub header">Input or Review the data that you have inputted</div>
			</div>
		</h1>
	</div>
	<div style="margin-left: 30%">
		<form class="noSubmit" onsubmit="performanceRatingCore()">
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
						<input type="submit" class="ui submit button" value="Go" >
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
function reviewFormType()
{
	e = event.target;
	formEl = e.form.elements;
	immediateSup = formEl.immediateSup;
	departmentHead = formEl.departmentHead;
	if(e.value == 1){
		immediateSup.parentElement.classList.remove('disabled');
		departmentHead.parentElement.classList.remove('disabled');
	}else if(e.value == 2||e.value == 4){
		immediateSup.parentElement.classList.remove('disabled');
		departmentHead.parentElement.classList.remove('disabled');
	}else if(e.value == 3){
		immediateSup.parentElement.classList.add('disabled');
		departmentHead.parentElement.classList.add('disabled');
	}
}



</script>



























<!--  -->
