<script src="/assets/pages/RSM/orgchart.js?v=<?php echo filemtime(__DIR__ . '/orgchart.js'); ?>"></script>

<style>
	/* Accordion Container */
	#mfo-accordion-container {
		max-height: 800px;
		overflow-y: auto;
		padding: 10px;
		width: 100%;
	}

	#mfo-accordion {
		width: 100%;
	}

	/* Nested accordion indentation */
	.ui.styled.accordion .content .ui.styled.accordion {
		margin-left: 20px;
		margin-top: 10px;
		width: calc(100% - 20px);
	}

	/* Accordion title styling */
	.ui.styled.accordion .title {
		font-weight: 500;
		padding: 12px 15px !important;
		color: #333;
	}

	.ui.styled.accordion .title:hover {
		background-color: #f8f9fa;
	}

	/* Collapsed title - default text color */
	.ui.styled.accordion .title:not(.active) {
		color: #333;
	}

	/* Expanded title - slightly darker */
	.ui.styled.accordion .title.active {
		color: #000;
		background-color: #f0f0f0;
	}

	/* Content styling */
	.ui.styled.accordion .content {
		padding: 15px !important;
	}

	/* Success indicators list */
	.success-indicators {
		margin-top: 6px;
	}

	.success-indicators-header {
		margin-bottom: 8px;
	}

	.success-indicators-header .si-count {
		color: #1976d2;
		font-size: 12px;
	}

	.success-indicator-item {
		padding: 10px 12px;
		margin-bottom: 8px;
		background-color: #fafafa;
		border: 1px solid #e0e0e0;
		border-left: 3px solid #1976d2;
		border-radius: 4px;
	}

	.success-indicator-item:last-child {
		margin-bottom: 0;
	}

	.success-indicator-item .si-number {
		font-weight: bold;
		color: #1976d2;
		margin-right: 4px;
	}

	/* Personnel tags */
	.personnel-tag {
		display: inline-block;
		background-color: #e3f2fd;
		color: #1976d2;
		padding: 4px 10px;
		border-radius: 4px;
		margin: 2px;
		font-size: 12px;
	}

	/* Clickable personnel tag */
	.personnel-tag-clickable {
		cursor: pointer;
		transition: filter 0.15s ease, box-shadow 0.15s ease;
	}

	.personnel-tag-clickable:hover {
		filter: brightness(0.95);
		box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
		text-decoration: underline;
	}

	/* Supervisor personnel tag (has subordinates) */
	.personnel-tag-supervisor {
		background-color: #fff3cd;
		color: #856404;
	}

	/* Department head personnel tag */
	.personnel-tag-dept-head {
		background-color: #d4edda;
		color: #155724;
		font-weight: bold;
	}

	/* Toggle button for nested accordion */
	.toggle-children-btn {
		float: right;
		padding: 4px 10px;
		font-size: 11px;
		border: 1px solid #ccc;
		border-radius: 4px;
		background-color: #f0f0f0;
		cursor: pointer;
		margin-left: 10px;
	}

	.toggle-children-btn:hover {
		background-color: #e0e0e0;
	}

	/* Controls */
	#accordion-controls {
		display: flex;
		gap: 10px;
		margin-bottom: 15px;
		padding: 10px;
		background-color: #f5f5f5;
		/* border-radius: 4px; */
	}

	#accordion-controls button {
		padding: 8px 16px;
		font-size: 14px;
		border: 1px solid #ccc;
		border-radius: 4px;
		background-color: white;
		cursor: pointer;
		transition: background-color 0.2s;
	}

	#accordion-controls button:hover {
		background-color: #e9e9e9;
	}

	/* Level indicators */
	.mfo-code {
		color: #666;
		font-weight: bold;
		margin-right: 8px;
	}

	.empty-children {
		color: #999;
		font-style: italic;
		padding: 10px;
	}

	/* Q/E/T measures */
	.qet-toggle-btn {
		display: inline-block;
		margin-top: 6px;
		padding: 2px 10px;
		font-size: 11px;
		border: 1px solid #ccc;
		border-radius: 4px;
		background-color: #f0f0f0;
		cursor: pointer;
	}

	.qet-toggle-btn:hover {
		background-color: #e0e0e0;
	}

	.qet-measures {
		display: none;
		margin-top: 8px;
		gap: 10px;
	}

	.qet-measures.qet-visible {
		display: flex;
	}

	.qet-column {
		flex: 1;
		min-width: 0;
		background-color: #fafafa;
		border: 1px solid #eee;
		border-radius: 4px;
		padding: 8px;
	}

	.qet-column-label {
		font-weight: bold;
		font-size: 12px;
		color: #1976d2;
		margin-bottom: 6px;
		border-bottom: 1px solid #eee;
		padding-bottom: 4px;
	}

	.qet-item {
		font-size: 11px;
		color: #444;
		padding: 2px 0;
		line-height: 1.4;
	}

	.qet-score {
		display: inline-block;
		min-width: 16px;
		font-weight: bold;
		color: #856404;
	}

	.mfo-edit-btn {
		cursor: pointer;
		color: #1976d2;
		margin-right: 5px;
		opacity: 0.6;
	}

	.mfo-edit-btn:hover {
		opacity: 1;
	}
</style>



<div id="rsm-header" style="display:none; margin-bottom: 0px; padding: 10px; background-color: #f8f9fa; text-align: center;">
	<h2 id="rsm-department-title" style="margin: 0 0 5px 0; color: #333;"></h2>
	<p id="rsm-period-year" style="margin: 0; color: #666; font-size: 14px;"></p>
</div>
<div id="accordion-controls">
	<button onclick="expandAllAccordion()">Expand All</button>
	<button onclick="collapseAllAccordion()">Collapse All</button>
	<button id="toggle-measures-btn" onclick="toggleAllQetMeasures()">Show All Measures</button>
</div>

<div id="mfo-accordion-container" style="position: relative; min-height: 100px;">
	<div id="mfo-loader" style="display:none; text-align:center; padding: 40px 0;">
		<div class="ui active inline loader"></div>
		<p style="margin-top: 10px; color: #666;">Loading MFO tree...</p>
	</div>
	<div class="ui styled accordion" id="mfo-accordion"></div>
</div>

<script>
	function rsmLoad(view) {
		if (view === 'table') {
			$('#allModal').modal('hide');
			var urlParams = new URLSearchParams(window.location.search);
			var period = urlParams.get('period');
			var year = urlParams.get('year');
			if (period && year) {
				rsm_load_tree(period, year, true);
			}
		}
	}

	$(document).ready(function() {
		// Check if period and year are in URL parameters
		const urlParams = new URLSearchParams(window.location.search);
		const period = urlParams.get('period');
		const year = urlParams.get('year');
		if (period && year) {
			rsm_load_tree(period, year)
		}
	});
</script>