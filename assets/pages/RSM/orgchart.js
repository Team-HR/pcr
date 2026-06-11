// JSON display functions for RSM org tree view

// Store current JSON data for copy functionality
var currentJsonData = null;

function rsm_load_tree(period, year) {
  $.post(
    "?config=rsm",
    {
      period_check: period,
      year: year,
    },
    function (data, textStatus, xhr) {
      if (data == 1) {
        // Period is valid, load tree data
        $.post(
          "?config=rsm",
          {
            get_mfo_tree: true,
          },
          function (treeData, textStatus, xhr) {
            try {
              var parsedData = JSON.parse(treeData);
              if (parsedData.error) {
                alert("Error: " + parsedData.error);
                return;
              }
              // Display as pretty JSON for MFO tree
              displayPrettyJson(parsedData);
            } catch (e) {
              console.error("JSON parse error:", e);
              console.error("Response:", treeData);
              alert("Error loading tree data. Please try again.");
            }
          }
        );
      } else {
        alert(data);
      }
    }
  );
}

function org_load_tree(period, year) {
  $.post(
    "?config=rsm",
    {
      period_check: period,
      year: year,
    },
    function (data, textStatus, xhr) {
      if (data == 1) {
        // Period is valid, load org tree data
        $.post(
          "?config=rsm",
          {
            get_org_tree: true,
          },
          function (treeData, textStatus, xhr) {
            try {
              var parsedData = JSON.parse(treeData);
              if (parsedData.error) {
                alert("Error: " + parsedData.error);
                return;
              }
              // Display as pretty JSON
              displayPrettyJson(parsedData);
            } catch (e) {
              console.error("JSON parse error:", e);
              console.error("Response:", treeData);
              alert("Error loading org tree data. Please try again.");
            }
          }
        );
      } else {
        alert(data);
      }
    }
  );
}

function displayPrettyJson(data) {
  // Store for copy functionality
  currentJsonData = data;

  // Format JSON with indentation and display as plain text
  var jsonString = JSON.stringify(data, null, 2);
  $('#json-display').text(jsonString);
}

function copyJsonToClipboard() {
  if (!currentJsonData) {
    alert("No JSON data to copy");
    return;
  }

  var jsonString = JSON.stringify(currentJsonData, null, 2);

  // Create temporary textarea to copy
  var $temp = $('<textarea>');
  $('body').append($temp);
  $temp.val(jsonString).select();

  try {
    document.execCommand('copy');
    showCopySuccess();
  } catch (err) {
    console.error('Failed to copy:', err);
    alert('Failed to copy to clipboard');
  }

  $temp.remove();
}

function showCopySuccess() {
  var $btn = $('#copy-json-btn');
  var originalText = $btn.text();

  $btn.text('Copied!').addClass('copied');

  setTimeout(function() {
    $btn.text(originalText).removeClass('copied');
  }, 2000);
}
