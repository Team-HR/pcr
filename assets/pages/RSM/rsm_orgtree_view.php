<script src="/assets/pages/RSM/orgchart.js?v=<?php echo filemtime(__DIR__ . '/orgchart.js'); ?>"></script>

<style>
	/* JSON Display Container */
	#json-container {
		position: relative;
		font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
		background-color: #1e1e1e;
		border: 1px solid #333;
		border-radius: 6px;
		overflow: auto;
		max-height: 1000px;
		padding: 20px;
	}

	#json-container pre {
		margin: 0;
		white-space: pre-wrap;
		word-wrap: break-word;
		color: #d4d4d4;
	}

	/* Controls styling */
	#json-controls {
		display: flex;
		gap: 10px;
		margin-bottom: 15px;
		padding: 10px;
		background-color: #f5f5f5;
		border-radius: 4px;
	}

	#json-controls button {
		padding: 8px 16px;
		font-size: 14px;
		border: 1px solid #ccc;
		border-radius: 4px;
		background-color: white;
		cursor: pointer;
		transition: background-color 0.2s;
	}

	#json-controls button:hover {
		background-color: #e9e9e9;
	}

	#json-controls button.copied {
		background-color: #4caf50;
		color: white;
		border-color: #4caf50;
	}

	/* Custom scrollbar for dark theme */
	#json-container::-webkit-scrollbar {
		height: 12px;
		width: 12px;
	}

	#json-container::-webkit-scrollbar-track {
		background: #2d2d2d;
		border-radius: 6px;
	}

	#json-container::-webkit-scrollbar-thumb {
		background: #555;
		border-radius: 6px;
	}

	#json-container::-webkit-scrollbar-thumb:hover {
		background: #777;
	}
</style>

<div id="json-controls">
	<button id="copy-json-btn" onclick="copyJsonToClipboard()">Copy JSON</button>
</div>

<div id="json-container">
	<pre id="json-display"></pre>
</div>

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