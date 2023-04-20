<div id="showFormsApp">
	<div class="ui basic segment center aligned" style="margin-left: 200px; margin-right: 200px;">
		<h1 class="ui header" style="margin-bottom: 0px; " v-if="period && year">{{ file_status.department }}</h1>
		<h2 style="margin-top: 0px;">{{ period + " " + year }}</h2>
	</div>

	<div class="ui segment" style="margin-left: 20px; margin-right: 20px;">
		<!-- 
		<p>
			I, <b>{{ file_status.name }}</b> , _______________________ of the <b>{{ file_status.department }}</b> commit to deliver and agree to be rated on the attainment of the following targets in accordance with the indicated measures for the period {{ period + " " + year }}.
		</p> -->

		<table style="width: 100%; _background-color:antiquewhite; border: 1px solid grey; border-collapse:collapse;">
			<tr>
				<td colspan="4" style="text-align: center;">
					<h4>{{ file_status.form_type }}</h4>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					I, <b>{{ file_status.name }}</b> , _______________________ of the <b>{{ file_status.department }}</b> commit to deliver and agree to be rated on the attainment of the following targets in accordance with the indicated measures for the period {{ period + " " + year }}.
				</td>
			</tr>
			<tr>
				<td colspan="3"></td>
				<td style="text-align: center; width: 400px;">
					<u><b>{{file_status.name}}</b></u> <br>
					Ratee
				</td>
			</tr>
			<tr style="background-color: #86fea0;">
				<td style="border: 1px solid grey;">
					<span style="font-size: 9px;">Received by:</span><br>
					<div class="ui fluid container center aligned">
						<u><b>{{file_status.name_supervisor}}</b></u> <br>
						<span>Immediate Superior</span>
					</div>
				</td>
				<td style="border: 1px solid grey;">
					<span style="font-size: 9px;">Noted by:</span><br>
					<div class="ui fluid container center aligned">
						<u><b>{{file_status.name_department_head}}</b></u> <br>
						<span>Department Head</span>
					</div>
				</td>
				<td style="border: 1px solid grey;">
					<span style="font-size: 9px;">Approved by:</span><br>
					<div class="ui fluid container center aligned">
						<u><b>{{file_status.name_head_of_agency}}</b></u> <br>
						<span>Head of Agency</span>
					</div>
				</td>
				<td style="border: 1px solid grey;">
					<span style="font-size: 9px;">Date:</span><br>
					<div class="ui fluid container center aligned">
						<u><b>{{ file_status.dateAccomplished }}</b></u> <br>
						<span style="opacity: 0;">(Date Accomplished)</span>
					</div>
				</td>
			</tr>
		</table>
		<br>

		<table class="ui structured celled table">
			<thead style="background-color: #00ffdc14; font-weight: bold; text-align: center;">
				<tr>
					<td rowspan="2">MFO / PAP</td>
					<td rowspan="2">SUCCESS INDICATOR</td>
					<td rowspan="2">ACTUAL ACCOMPLISHMENT</td>
					<td colspan="4">RATING MATRIX</td>
					<td rowspan="2">REMARKS</td>
					<td rowspan="2">OPTIONS</td>
				</tr>
				<tr>
					<td style="width: 20px;">Q</td>
					<td style="width: 20px;">E</td>
					<td style="width: 20px;">T</td>
					<td style="width: 20px;">A</td>
				</tr>
			</thead>
			<template v-for="item,i in items" :key="i">
				<!-- if no success indicators -->
				<tr v-if="item.colspan == 'all'">
					<td colspan="9">
						<div :style="getMargin(item.level)">{{ item.cf_count }} {{ item.cf_title }}</div>
					</td>
				</tr>
				<!-- else if has success indicators -->
				<tr v-else-if="item.colspan == 0">
					<td :rowspan="item.rowspan">
						<div :style="getMargin(item.level)">
							<button class="ui basic button">{{item.percent + "%"}}</button> {{ item.cf_count }} {{ item.cf_title }}
						</div>
					</td>
					<td>
						{{item.mi_succIn}}
					</td>
					<!-- if has spms_corefucndata -->
					<template v-if="item.cfd_id">
						<td>{{item.actualAcc}}</td>
						<td>{{item.q}}</td>
						<td>{{item.e}}</td>
						<td>{{item.t}}</td>
						<td style="text-align: center;">{{item.average}}</td>
						<td></td>
						<td width="150" style="text-align: center;"> <button class="ui small button" @click="review(item)"><i class="ui icon edit"></i> Review</button> </td>
					</template>
					<!-- else if disabled/not_applicable -->
					<template v-else-if="item.not_applicable == '1'">
						<td colspan="5" style="color:blue; text-align: center;"> Not Applicable </td>
						<td></td>
						<td></td>
					</template>
					<!-- else not accomplished/filled out -->
					<template v-else>
						<td colspan="5" style="color:red; text-align: center;"> Not Accomplished </td>
						<td></td>
						<td></td>
					</template>
				</tr>
				<tr v-else-if="!item.cf_title">
					<td>
						{{item.mi_succIn}}
					</td>
					<!-- if has spms_corefucndata -->
					<template v-if="item.cfd_id">
						<td>{{item.actualAcc}}</td>
						<td>{{item.q}}</td>
						<td>{{item.e}}</td>
						<td>{{item.t}}</td>
						<td style="text-align: center;">{{item.average}}</td>
						<td></td>
						<td></td>
					</template>
					<!-- else if disabled/not_applicable -->
					<template v-else-if="item.not_applicable == '1'">
						<td colspan="5" style="color:blue; text-align: center;"> Not Applicable </td>
						<td></td>
						<td></td>
					</template>
					<!-- else not accomplished/filled out -->
					<template v-else>
						<td colspan="5" style="color:red; text-align: center;"> Not Accomplished </td>
						<td></td>
						<td></td>
					</template>
				</tr>
			</template>
		</table>

		<template v-for="item,i in items" :key="i">
			<div>{{item}}</div>
			<hr>
		</template>

	</div>

	<!-- start reviewForm -->
	<div class="ui modal" id="reviewForm">
		<div class="header">
			<i style="font-weight:lighter; _color:grey;">{{itemForEdit.cf_count}} {{itemForEdit.cf_title}}</i>
		</div>

		<div class="content">
			<div class="ui form">
				<div class="field">
					<label>Success Indicators</label>
					<!-- <textarea rows="1" readonly style="border: none 0px;">{{ itemForEdit.mi_succIn }}</textarea> -->
					<p style="margin-left: 17px;">{{itemForEdit.mi_succIn}}</p>
				</div>
				<div class="field">
					<label>Actual Accomplishments</label>
					<textarea rows="2" v-model="itemForEdit.actualAcc"></textarea>
				</div>
				<div class="field" v-if="itemForEdit.q">
					<label>Quality</label>
					<select v-model="itemForEdit.q">
						<template v-for="measure,score in itemForEdit.mi_quality" :key="score">
							<option v-if="measure" :value="score">{{measure}}</option>
						</template>
					</select>
				</div>
				<div class="field" v-if="itemForEdit.e">
					<label>Efficiency</label>
					<select v-model="itemForEdit.e">
						<template v-for="measure,score in itemForEdit.mi_eff" :key="score">
							<option v-if="measure" :value="score">{{measure}}</option>
						</template>
					</select>
				</div>
				<div class="field" v-if="itemForEdit.t">
					<label>Timeliness</label>
					<select v-model="itemForEdit.t">
						<template v-for="measure,score in itemForEdit.mi_time" :key="score">
							<option v-if="measure" :value="score">{{measure}}</option>
						</template>
					</select>
				</div>
				<div class="field">
					<label>Weight Allocation(%)</label>
					<input type="number" v-model="itemForEdit.percent">
				</div>



				<div v-if="itemForEdit.critics && itemForEdit.critics.IS">
					<div class="ui segments field">
						<div class="ui green inverted segment">Immediate Supervisor</div>
						<textarea class="ui secondary" rows="2" readonly v-model="itemForEdit.critics.IS"></textarea>
					</div>
				</div>

				<div v-if="itemForEdit.critics && itemForEdit.critics.DH">
					<div class="ui segments field">
						<div class="ui orange inverted segment">Department Head</div>
						<textarea class="ui secondary" rows="2" readonly v-model="itemForEdit.critics.DH"></textarea>
					</div>
				</div>

				<!-- <div v-if="itemForEdit.critics && itemForEdit.critics.PMT"> -->
				<div class="ui segments field">
					<div class="ui red inverted segment">PMT</div>
					<textarea class="ui secondary" rows="2" v-model="pmtComments" placeholder="Enter your comments/corrections here..."></textarea>
				</div>
				<!-- </div> -->


			</div>
		</div>
		<div class="actions">
			<div class="ui deny button">
				Cancel
			</div>
			<div class="ui positive _right _labeled _icon button">
				Save
				<!-- <i class="checkmark icon"></i> -->
			</div>
		</div>
	</div>
	<!-- end reviewForm -->

</div>


<script>
	/* Vue3 Start*/
	const {
		createApp
	} = Vue

	createApp({
		data() {
			return {
				file_status: {},
				period: "",
				year: "",
				department: "",
				id: new URL(window.location.href).searchParams.get("id"),
				items: null,
				itemForEdit: {},
				pmtComments: ""
			}
		},
		watch: {

		},
		computed: {

		},
		methods: {

			review(item) {
				this.itemForEdit = JSON.parse(JSON.stringify(item))
				this.pmtComments = "";
				if (this.itemForEdit.critics && this.itemForEdit.critics.PMT) {
					this.pmtComments = this.itemForEdit.critics.PMT
				}
				// console.log(this.itemForEdit);
				$('#reviewForm').modal({
					closable: false
				}).modal('show');
				// console.log(item);
			},
			getMargin(level) {
				const margin = level * 30;
				return `margin-left:${margin}px;`
			},
			initLoad(department_id) {
				console.log(department_id);
				$.post('?config=PMT', {
					initLoadForm: true,
					id: this.id,
				}, (data, textStatus, xhr) => {
					const res = JSON.parse(data)
					this.period = res.period
					this.year = res.year
					this.items = res.data
					this.file_status = res.file_status
				});
			}


		},
		mounted() {
			this.initLoad()
		}

	}).mount('#showFormsApp')
	/* Vue3 End*/
</script>