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
	.success-indicator-item {
		padding: 8px 0;
		border-bottom: 1px solid #eee;
	}

	.success-indicator-item:last-child {
		border-bottom: none;
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
</style>



<div id="rsm-header" style="display:none; margin-bottom: 0px; padding: 10px; background-color: #f8f9fa; text-align: center;">
	<h2 id="rsm-department-title" style="margin: 0 0 5px 0; color: #333;"></h2>
	<p id="rsm-period-year" style="margin: 0; color: #666; font-size: 14px;"></p>
</div>
<div id="accordion-controls">
	<button onclick="expandAllAccordion()">Expand All</button>
	<button onclick="collapseAllAccordion()">Collapse All</button>
</div>

<div id="mfo-accordion-container" style="position: relative; min-height: 100px;">
	<div id="mfo-loader" style="display:none; text-align:center; padding: 40px 0;">
		<div class="ui active inline loader"></div>
		<p style="margin-top: 10px; color: #666;">Loading MFO tree...</p>
	</div>
	<div class="ui styled accordion" id="mfo-accordion"></div>
</div>

<script>
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