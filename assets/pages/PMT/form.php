<div id="showFormsApp">
	<div class="ui basic segment center aligned" style="margin-left: 200px; margin-right: 200px;">
		<h1 class="ui header" style="margin-bottom: 0px; " v-if="period && year">{{ file_status.department }}</h1>
		<h2 style="margin-top: 0px;">{{ period + " " + year }}</h2>
	</div>

	<div class="ui basic segment" style="margin-left: 20px; margin-right: 20px;">
		<!-- 
		<p>
			I, <b>{{ file_status.name }}</b> , _______________________ of the <b>{{ file_status.department }}</b> commit to deliver and agree to be rated on the attainment of the following targets in accordance with the indicated measures for the period {{ period + " " + year }}.
		</p> -->
		<table class="ui celled table" style="width: 100%; _background-color:antiquewhite; _border: 1px solid grey; _border-collapse:collapse;">
			<tr>
				<td colspan="4" style="text-align: center;">
					<h4>{{ file_status.form_type }}</h4>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					I, <b>{{ file_status.name }}</b> , {{ file_status.position }} of the <b>{{ file_status.department }}</b> commit to deliver and agree to be rated on the attainment of the following targets in accordance with the indicated measures for the period {{ period + " " + year }}.
				</td>
			</tr>
			<tr>
				<td colspan="3"></td>
				<td style="text-align: center; width: 400px;">
					<u><b>{{file_status.name}}</b></u> <br>
					Ratee
				</td>
			</tr>
			<tr style="background-color: #0080003d;">
				<td>
					<span style="font-size: 9px;">Received by:</span><br>
					<div class="ui fluid container center aligned">
						<u><b>{{file_status.name_supervisor}}</b></u> <br>
						<span>Immediate Superior</span>
					</div>
				</td>
				<td>
					<span style="font-size: 9px;">Noted by:</span><br>
					<div class="ui fluid container center aligned">
						<u><b>{{file_status.name_department_head}}</b></u> <br>
						<span>Department Head</span>
					</div>
				</td>
				<td>
					<span style="font-size: 9px;">Approved by:</span><br>
					<div class="ui fluid container center aligned">
						<u><b>{{file_status.name_head_of_agency}}</b></u> <br>
						<span>Head of Agency</span>
					</div>
				</td>
				<td>
					<span style="font-size: 9px;">Date:</span><br>
					<div class="ui fluid container center aligned">
						<u><b>{{ file_status.dateAccomplished }}</b></u> <br>
						<span style="opacity: 0;">(Date Accomplished)</span>
					</div>
				</td>
			</tr>
		</table>
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

			<template v-if="!strategic_function.noStrat">
				<tr>
					<td colspan="9" style="background: lightyellow;"><b>STRATEGIC FUNCTION <span style="color: blue;">(20%)</span></b></td>
				</tr>
				<tr>
					<td>{{strategic_function.mfo}}</td>
					<td>{{strategic_function.success_indicator}}</td>
					<td>{{strategic_function.acctual_accomplishment}}</td>
					<td colspan="3" class="center aligned">{{strategic_function.final_numerical_rating}}</td>
					<td>{{strategic_function.final_average_rating}}</td>
					<td></td>
					<td></td>
				</tr>
			</template>

			<tr>
				<td colspan="9" style="background: lightyellow;"><b>CORE FUNCTIONS <span style="color: blue;">({{core_functions.percent}}%)</span></b></td>
			</tr>
			<template v-for="item,i in core_functions.rows" :key="i">
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
							{{ item.cf_count }} {{ item.cf_title }}
						</div>
					</td>
					<td>
						<a class="ui red ribbon label" style="" v-if="item.critics.PMT" @click="review(item)">View Comment/s</a>
						<button class="ui basic mini button" :class="item.corrected_percent ? 'red':''">{{item.percent + "%"}}</button>
						{{item.mi_succIn}}
					</td>
					<!-- if has spms_corefucndata -->
					<template v-if="item.cfd_id">
						<td :style="item.corrected_actualAcc ? 'color:red':''">{{item.actualAcc}}</td>
						<td :style="item.corrected_Q ? 'color:red':''">{{item.q}}</td>
						<td :style="item.corrected_E ? 'color:red':''">{{item.e}}</td>
						<td :style="item.corrected_T ? 'color:red':''">{{item.t}}</td>
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
						<a class="ui red ribbon label" style="" v-if="item.critics.PMT" @click="review(item)">View Comment/s</a>
						<button class="ui basic mini button">{{item.percent + "%"}}</button>
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
			</template>
			<tr>
				<td colspan="9" style="background: lightyellow;"><b>SUPPORT FUNCTION <span style="color: blue;">(20%)</span></b></td>
			</tr>
			<template v-for="item, in support_functions.rows" :key="item.id_suppFunc">
				<tr>
					<td>
						<template v-if="item.critics.PMT">
							<a class="ui red ribbon label" style="margin: 15px;" @click="reviewSupportFunction(item)">View Comment/s</a>
							<br>
						</template>
						<button class="ui basic mini button">{{item.percent + "%"}}</button> {{item.mfo}}
					</td>
					<td>{{item.suc_in}}</td>
					<td>{{item.accomplishment}}</td>
					<td>{{item.Q}}</td>
					<td>{{item.E}}</td>
					<td>{{item.T}}</td>
					<td>{{item.average_rating}}</td>
					<td>{{}}</td>
					<td class="center aligned"><button class="ui small button" @click="reviewSupportFunction(item)"><i class="ui icon edit"></i> Review</button></td>
				</tr>
			</template>
		</table>
		<table class="ui celled structured table">
			<tr style="background: lightyellow;">
				<td style="font-size: 9px;" colspan="2">SUMMARY OF RATING</td>
				<td style="font-size: 9px;" class="center aligned">TOTAL</td>
				<td style="font-size: 9px;" width="318">FINAL NUMERICAL RATING</td>
				<td style="font-size: 9px;" width="380">FINAL ADJECTIVAL RATING</td>
			</tr>
			<template v-for="item, i in [
					{
						name: 'Strategic Objectives',
						percent: 20,
						total: strategic_function.final_average_rating
					},
					{
						name: 'Core Functions',
						percent: core_functions.percent,
						total: core_functions.final_numerical_rating
					},
					{
						name: 'Support Functions',
						percent: support_functions.percent,
						total: support_functions.final_numerical_rating
					},
				]" :key="i">
				<tr>
					<td>{{ item.name }}</td>
					<td>Total Weight Allocation: {{ item.percent }}%</td>
					<td class="center aligned"><b>{{ item.total }}</b></td>
					<template v-if="i < 1">
						<td rowspan="3" class="center aligned"><b>{{overall_final_rating.final_numerical_rating}}</b></td>
						<td rowspan="3" class="center aligned"><b>{{overall_final_rating.final_adjectival_rating}}</b></td>
					</template>
				</tr>
			</template>
			<tr>
				<td colspan="5" style="padding: 30px;">
					<b>Comments and Recommendation For Development Purpose:</b> <br>
					<p style="min-height: 50px; text-indent: 50px;">
						{{comments_and_reccomendations}}
					</p>
				</td>
			</tr>
		</table>

		<table class="ui celled structured table" style="background-color: #c2e1c2;">
			<tr>
				<td style="width: 16%; font-size: 9px; padding: 0px; padding-left:5px;">Discussed: Date:</td>
				<td style="width: 16%; font-size: 9px; padding: 0px; padding-left:5px;">Assessed by: Date:</td>
				<td style="width: 16%; font-size: 9px; padding: 0px; padding-left:5px;"></td>
				<td style="width: 16%; font-size: 9px; padding: 0px; padding-left:5px;">Reviewed: Date:</td>
				<td style="width: 16%; font-size: 9px; padding: 0px; padding-left:5px;">Final Rating by:</td>
				<td style="width: 16%; font-size: 9px; padding: 0px; padding-left:5px;">Date:</td>
			</tr>
			<tr>
				<td class="center aligned" style="font-size: 11px; _height: 100px; vertical-align:bottom; padding: 0px;"><b>{{file_status.name}}</b></td>
				<td class="center aligned" style="font-size: 11px; _height: 100px; vertical-align:bottom; padding: 0px;">
					<p style="font-size: 10px; padding: 10px;">I certified that I discussed my assessment of the performance with the employee:</p>
					<br>
					<b>{{file_status.name_supervisor}}</b>
				</td>
				<td class="center aligned" style="font-size: 11px; _height: 100px; vertical-align:bottom; padding: 0px;">
					<p style="font-size: 10px; padding: 10px;">I certified that I discussed my assessment of the performance with the employee:</p>
					<br>
					<b>{{file_status.name_department_head}}</b>
				</td>
				<td class="center aligned" style="font-size: 11px; _height: 100px; vertical-align:bottom; padding: 0px;">
					<p style="font-size: 10px; padding: 10px;">(all PMT member will sign)</p>
				</td>
				<td class="center aligned" style="font-size: 11px; _height: 100px; vertical-align:bottom; padding: 0px;"><b>{{file_status.HeadAgency}}</b></td>
				<td class="center aligned" style="font-size: 11px; _height: 100px; vertical-align:bottom; padding: 0px;"></td>
			</tr>
			<tr>
				<td class="center aligned" style="font-size: 9px; padding: 0px; padding-left:5px;">Ratee</td>
				<td class="center aligned" style="font-size: 9px; padding: 0px; padding-left:5px;">Supervisor</td>
				<td class="center aligned" style="font-size: 9px; padding: 0px; padding-left:5px;">Department Head</td>
				<td class="center aligned" style="font-size: 9px; padding: 0px; padding-left:5px;"></td>
				<td class="center aligned" style="font-size: 9px; padding: 0px; padding-left:5px;">Head of Agency</td>
				<td class="center aligned" style="font-size: 9px; padding: 0px; padding-left:5px;">Date:</td>
			</tr>
		</table>

		<div class="ui fluid container center aligned">
			<button class="ui primary big teal button" @click="doApprove()" v-if="!isApproved"> Approve </button>
		</div>
	</div>

	<!-- START reviewForm -->
	<div class="ui scrollable modal" id="reviewForm">
		<div class="header">
			<i style="font-weight:lighter; _color:grey;">{{itemForEdit.cf_count}} {{itemForEdit.cf_title}}</i>
		</div>

		<div class="scrolling content">
			<div class="ui form">
				<div class="field">
					<label>Success Indicators</label>
					<!-- <textarea rows="1" readonly style="border: none 0px;">{{ itemForEdit.mi_succIn }}</textarea> -->
					<p style="margin-left: 17px;">{{itemForEdit.mi_succIn}}</p>
				</div>
				<div class="field">
					<label>Actual Accomplishments</label>
					<textarea rows="2" v-model="itemForEdit.actualAcc"></textarea>
					<!-- <p style="padding: 20px; background: cyan;">{{itemForEdit.actualAcc}}</p> -->
				</div>


				<template v-for="mi, _mi  in [
						{
							id: 'q',
							name: 'Quality',
							col: 'mi_quality'
						},
						{
							id: 'e',
							name: 'Efficiency',
							col: 'mi_eff'
						},
						{
							id: 't',
							name: 'Timeliness',
							col: 'mi_time'
						},
					]" :key="_mi">

					<div class="field" v-if="itemForEdit[mi.id]">
						<label>{{mi.name}}</label>
						<select :name="`${mi.col}_select`" v-model="itemForEdit[mi.id]">
							<template v-for="measure, score in itemForEdit[mi.col]" :key="score">
								<option v-if="measure && itemForEdit[mi.id] != score" :value="score">{{measure}}</option>
								<option v-else-if="measure && itemForEdit[mi.id] == score" selected :value="score">{{measure}}</option>
							</template>
						</select>
						<!-- <template v-for="measure, score in itemForEdit[mi.col]" :key="score">
							<div v-if="measure && itemForEdit[mi.id] != score"><i style="margin-left: 20px; padding: 5px; color:grey;">({{score}})</i> {{measure}}</div>
							<div v-else-if="measure && itemForEdit[mi.id] == score" style="margin-left: 20px; padding: 5px; background: cyan;"><i style="color:grey;">({{score}})</i> {{measure}}</div>
						</template> -->
					</div>

				</template>

				<div class="field">
					<label>Weight Allocation(%)</label>
					<input type="number" v-model="itemForEdit.percent"></textarea>
					<!-- <p style="padding: 20px; background: cyan;">{{itemForEdit.percent}}</p> -->
				</div>

				<div v-if="itemForEdit.critics && itemForEdit.critics.IS">
					<div class="ui segments field" style="margin-bottom: 15px;">
						<div class="ui green inverted segment">Immediate Supervisor Remark/s:</div>
						<p style="padding: 20px; color: green;">* {{itemForEdit.critics.IS}}</p>
						<!-- <textarea class="ui secondary" rows="2" disabled v-model="itemForEdit.critics.IS"></textarea> -->
					</div>
				</div>

				<div v-if="itemForEdit.critics && itemForEdit.critics.DH">
					<div class="ui segments field" style="margin-bottom: 15px;">
						<div class="ui orange inverted segment">Department Head Remark/s:</div>
						<p style="padding: 20px; color: orange;">* {{itemForEdit.critics.DH}}</p>
						<!-- <textarea class="ui secondary" rows="2" readonly v-model="itemForEdit.critics.DH"></textarea> -->
					</div>
				</div>

				<!-- <div v-if="itemForEdit.critics && itemForEdit.critics.PMT"> -->
				<div class="ui segments field" style="margin-bottom: 15px;">
					<div class="ui red inverted segment">PMT Remark/s:</div>
					<textarea class="ui secondary" v-model="itemForEdit.critics.PMT" placeholder="Enter your comments/corrections here..."></textarea>
				</div>
				<!-- </div> -->


			</div>
		</div>
		<div class="actions">
			<div class="ui deny button">
				Cancel
			</div>
			<div class="ui positive approve _right _labeled _icon button">
				Save
				<!-- <i class="checkmark icon"></i> -->
			</div>
		</div>
	</div>
	<!-- END reviewForm -->



	<!-- START reviewForm for Support Function-->
	<div class="ui scrollable modal" id="reviewFormSupport">
		<div class="header">
			<i style="font-weight:lighter; _color:grey;">{{itemForEditSupport.mfo}} ({{itemForEditSupport.percent}}%)</i>
		</div>

		<div class="scrolling content">
			<div class="ui form">
				<div class="field">
					<label>Success Indicators</label>
					<p style="margin-left: 17px;">{{itemForEditSupport.suc_in}}</p>
				</div>
				<div class="field">
					<label>Actual Accomplishments</label>
					<!-- <p style="padding: 20px; background: cyan;">{{itemForEditSupport.accomplishment}}</p> -->
					<textarea rows="3" v-model="itemForEditSupport.accomplishment"></textarea>

				</div>

				<template v-for="mi, _mi  in [
						{
							id: 'q',
							name: 'Quality',
							col: 'mi_quality'
						},
						{
							id: 'e',
							name: 'Efficiency',
							col: 'mi_eff'
						},
						{
							id: 't',
							name: 'Timeliness',
							col: 'mi_time'
						},
					]" :key="_mi">

					<div class="field" v-if="itemForEditSupport[mi.id]">
						<label>{{mi.name}}</label>
						<select v-model="itemForEditSupport[mi.id]">
							<template v-for="measure, score in itemForEditSupport[mi.col]" :key="score">
								<!-- <div v-if="measure && itemForEditSupport[mi.id] != score"><i style="padding: 5px; color:grey;">({{score}})</i> {{measure}}</div>
							<div v-else-if="measure && itemForEditSupport[mi.id] == score" style="padding: 5px; background: cyan;"><i style="color:grey;">({{score}})</i> {{measure}}</div> -->
								<option v-if="measure" :value="score">{{measure}}</option>
							</template>
						</select>
					</div>
				</template>


				<template v-for="comment, c in [
						{
							id: 'IS',
							label: 'Immediate Supervisor Remark/s:',
							color: 'green'
						},
						{
							id: 'DH',
							label: 'Department Head Remark/s:',
							color: 'orange'
						},
						{
							id: 'PMT',
							label: 'PMT Remark/s:',
							color: 'red'
						}
					]" :key="c">

					<div v-if="itemForEditSupport.critics && itemForEditSupport.critics[comment.id] && comment.id != 'PMT'">
						<div class="ui segments field" style="margin-bottom: 15px;">
							<div class="ui inverted segment" :class="comment.color">{{ comment.label }}</div>
							<p :style="'padding: 20px; color:'+ comment.color +';'">* {{itemForEditSupport.critics[comment.id]}}</p>
						</div>
					</div>

					<div v-else-if="comment.id == 'PMT'">
						<div class="ui segments field" style="margin-bottom: 15px;">
							<div class="ui inverted segment" :class="comment.color">{{ comment.label }}</div>
							<textarea class="ui secondary" v-model="pmtCommentsSupport" placeholder="Enter your comments/corrections here..."></textarea>
						</div>
					</div>

				</template>

			</div>
		</div>
		<div class="actions">
			<div class="ui deny button">
				Cancel
			</div>
			<div class="ui positive approve _right _labeled _icon button">
				Save
				<!-- <i class="checkmark icon"></i> -->
			</div>
		</div>
	</div>
	<!-- END reviewForm for Support Function -->




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
				itemForEdit: {
					critics: {
						PMT: ""
					}
				},
				itemForEditSupport: {},
				pmtComments: "",
				pmtCommentsSupport: "",
				strategic_function: {},
				core_functions: {},
				support_functions: {},
				comments_and_reccomendations: "",
				overall_final_rating: {},
				isApproved: false
			}
		},
		watch: {

		},
		computed: {

		},
		methods: {

			reviewSupportFunction(item) {
				this.itemForEditSupport = JSON.parse(JSON.stringify(item))
				this.pmtCommentsSupport = "";

				if (this.itemForEditSupport.critics && this.itemForEditSupport.critics.PMT) {
					this.pmtCommentsSupport = this.itemForEditSupport.critics.PMT
				}
				$('#reviewFormSupport').modal({
					closable: false,
					onApprove: () => {
						this.setCriticsSupport("pmt", this.itemForEditSupport)
						return false;
					}
				}).modal('show');
			},

			review(item) {
				this.itemForEdit = JSON.parse(JSON.stringify(item))
				this.pmtComments = "";

				if (this.itemForEdit.critics && this.itemForEdit.critics.PMT) {
					this.pmtComments = this.itemForEdit.critics.PMT
				}

				$('#reviewForm').modal({
					closable: false,
					onApprove: () => {
						this.setCritics(this.itemForEdit)
						// this.itemForEdit["remarks"] = this.pmtComments
						// console.log(this.itemForEdit);
						return false;
					}
				}).modal('show');
			},

			setCritics(payload) {
				// console.log(payload);
				$.post('?config=PMT', {
					setCritics: true,
					payload: payload
				}, (data, textStatus, xhr) => {
					const res = JSON.parse(data);
					// console.log(data)
					this.initLoad()
				});
			},

			setCriticsSupport(commentor, payload) {
				$.post('?config=PMT', {
					setCriticsSupport: true,
					commentor: commentor,
					payload: payload
				}, (data, textStatus, xhr) => {
					const comms = JSON.parse(data);
					// console.log(comms);
					this.initLoad()
				});
			},

			getMargin(level) {
				const margin = level * 30;
				return `margin-left:${margin}px;`
			},

			initLoad(department_id) {
				$.post('?config=PMT', {
					initLoadForm: true,
					id: this.id,
				}, (data, textStatus, xhr) => {
					const res = JSON.parse(data)

					this.period = res.period
					this.year = res.year
					this.core_functions = res.core_functions

					console.log(res.core_functions.rows)

					this.file_status = res.file_status
					this.strategic_function = res.strategic_function
					this.support_functions = res.support_functions
					this.comments_and_reccomendations = res.comments_and_reccomendations
					this.overall_final_rating = res.overall_final_rating
					this.isApproved = res.isApproved
					$('#reviewForm').modal("hide")
					$('#reviewFormSupport').modal("hide")
				});
			},

			doApprove() {
				$.post('?config=PMT', {
					doApprove: true,
					id: this.id,
				}, (data, textStatus, xhr) => {
					this.isApproved = data
				});
			}

		},
		mounted() {
			this.initLoad()
		}

	}).mount('#showFormsApp')
	/* Vue3 End*/
</script>