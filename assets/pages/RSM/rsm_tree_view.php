<script src="/node_modules/orgchart/dist/js/jquery.orgchart.min.js"></script>
<link rel="stylesheet" href="/node_modules/orgchart/dist/css/jquery.orgchart.min.css">
<script src="/assets/pages/RSM/orgchart.js?v=<?php echo filemtime(__DIR__ . '/orgchart.js'); ?>"></script>

<style>
	#chart-container {
		position: relative;
		font-family: Arial;
		height: 1560px;
		border: 1px solid #aaa;
		overflow: auto;
		text-align: center;
		background-color: white;
	}

	/* Target the title element inside the node container */
	.orgchart .node .content {
		height: auto !important;
		/* Removes the restrictive fixed height */
		line-height: 1.4 !important;
		/* Provides proper vertical spacing for text rows */
		white-space: normal !important;
		/* Overrides 'nowrap' to allow actual text wrapping */
		overflow: visible !important;
		/* Ensures long text strings aren't clipped */
		text-overflow: clip !important;
		/* Removes the trailing ellipsis (...) */
		padding: 8px 4px;
		/* Adds padding around wrapped sentences */
	}

	/* Optional: Set a fixed or minimum width for your MFO nodes */
	.orgchart .node {
		min-width: 80px !important;
		/* Adjust this value based on your UI needs */
	}

	/* Always show edge chevrons and color them blue */
	.orgchart .node .edge {
		opacity: 1 !important;
		visibility: visible !important;
		color: #1a73e8 !important;
	}
	.orgchart .node .edge::before {
		color: #1a73e8 !important;
	}
</style>

<div id="chart-container"></div>

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