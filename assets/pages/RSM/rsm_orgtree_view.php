<script src="/node_modules/orgchart/dist/js/jquery.orgchart.min.js"></script>
<link rel="stylesheet" href="/node_modules/orgchart/dist/css/jquery.orgchart.min.css">
<script src="/assets/pages/RSM/orgchart.js?v=<?php echo filemtime(__DIR__ . '/orgchart.js'); ?>"></script>

<style>
	#orgchart-container {
		position: relative;
		font-family: Arial;
		height: 560px;
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
	}

	/* Custom scrollbar styling */
	#orgchart-container::-webkit-scrollbar {
		height: 20px;
		width: 12px;
	}

	#orgchart-container::-webkit-scrollbar-track {
		background: #f1f1f1;
		border-radius: 6px;
	}

	#orgchart-container::-webkit-scrollbar-thumb {
		background: grey;
		border-radius: 6px;
	}

	#orgchart-container::-webkit-scrollbar-thumb:hover {
		background: teal;
	}

	/* Optional: Set a fixed or minimum width for your MFO nodes */
	.orgchart .node {
		min-width: 80px !important;
		/* Adjust this value based on your UI needs */
	}

	/* Highlighted node styling */
	.orgchart .node.highlighted {
		background-color: #ffeb3b !important;
		border: 2px solid #fbc02d !important;
		box-shadow: 0 0 10px rgba(251, 192, 45, 0.5) !important;
	}

	/* Search input styling */
	#search-container {
		margin-bottom: 15px;
		padding: 10px;
		background-color: #f5f5f5;
		border-radius: 4px;
	}

	#search-input {
		width: 100%;
		padding: 8px 12px;
		font-size: 14px;
		border: 1px solid #ccc;
		border-radius: 4px;
		box-sizing: border-box;
	}

	#search-input:focus {
		outline: none;
		border-color: #007bff;
	}
</style>

<div id="search-container">
	<input type="text" id="search-input" placeholder="Search by name...">
</div>

<div id="orgchart-container"></div>

<script>
	$(document).ready(function() {
		// Check if period and year are in URL parameters
		const urlParams = new URLSearchParams(window.location.search);
		const period = urlParams.get('period');
		const year = urlParams.get('year');
		if (period && year) {
			org_load_tree(period, year)
		}
	});
</script>