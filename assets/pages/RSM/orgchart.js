// RSM Tree and Org Tree display functions

function rsm_load_tree(period, year) {
  // Show loader, hide accordion and controls
  $('#mfo-loader').show();
  $('#mfo-accordion').hide();
  $('#accordion-controls').hide();

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
                $('#mfo-loader').hide();
                $('#mfo-accordion').show();
                $('#accordion-controls').show();
                alert("Error: " + parsedData.error);
                return;
              }
              // Render as accordion for MFO tree
              renderMfoAccordion(parsedData);
              $('#mfo-loader').hide();
              $('#mfo-accordion').show();
              $('#accordion-controls').show();
            } catch (e) {
              $('#mfo-loader').hide();
              $('#mfo-accordion').show();
              $('#accordion-controls').show();
              console.error("JSON parse error:", e);
              console.error("Response:", treeData);
              alert("Error loading tree data. Please try again.");
            }
          }
        );
      } else {
        $('#mfo-loader').hide();
        $('#mfo-accordion').show();
        $('#accordion-controls').show();
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
              // Display as pretty JSON for personnel org tree
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

function renderMfoAccordion(treeData) {
  var $container = $('#mfo-accordion');
  $container.empty();

  // treeData is array with single root element (department)
  if (treeData && treeData.length > 0 && treeData[0].children) {
    var rootNode = treeData[0];

    // Populate header with department and period-year
    var deptName = rootNode.title || rootNode.code || 'Department';
    var deptAlias = rootNode.code || '';
    var displayTitle = deptAlias ? deptAlias + ' - ' + deptName : deptName;

    $('#rsm-department-title').text(displayTitle);

    // Get period and year from URL
    const urlParams = new URLSearchParams(window.location.search);
    const period = urlParams.get('period');
    const year = urlParams.get('year');
    $('#rsm-period-year').text(period && year ? period + ' ' + year : '');

    // Show header
    $('#rsm-header').show();

    var html = buildMfoAccordionHtml(rootNode.children);
    $container.html(html);

    // Initialize Semantic UI accordion
    $container.accordion({
      exclusive: false,
      animateChildren: false,
      duration: 200
    });
  }
}

function buildMfoAccordionHtml(mfoNodes) {
  if (!mfoNodes || mfoNodes.length === 0) {
    return '<div class="empty-children">No MFO items</div>';
  }

  var html = '';

  mfoNodes.forEach(function(node, index) {
    var hasChildren = node.children && node.children.length > 0;
    var titleClass = hasChildren ? '' : 'disabled';
    var uniqueId = 'mfo-' + node.id + '-' + index;

    html += '<div class="' + (hasChildren ? 'active' : '') + ' title ' + titleClass + '" data-mfo-id="' + uniqueId + '">';
    html += '<i class="dropdown icon"></i>';
    if (node.code) {
      html += '<span class="mfo-code">' + escapeHtml(node.code) + '</span>';
    }
    html += escapeHtml(node.title);
    html += '</div>';

    html += '<div class="' + (hasChildren ? 'active' : '') + ' content">';

    // Success indicators
    if (node.success_indicators && node.success_indicators.length > 0) {
      html += '<div class="ui list">';
      html += '<div class="item"><strong>Success Indicators:</strong></div>';
      node.success_indicators.forEach(function(si) {
        html += '<div class="success-indicator-item">';
        html += escapeHtml(si.description);
        html += '</div>';
      });
      html += '</div>';
    }

    // Personnel in charge
    if (node.personnel_incharge && node.personnel_incharge.length > 0) {
      html += '<div style="margin-top: 10px;">';
      html += '<strong>Personnel In-Charge:</strong><br>';
      node.personnel_incharge.forEach(function(person) {
        html += '<span class="personnel-tag">' + escapeHtml(person.full_name) + '</span>';
      });
      html += '</div>';
    }

    // Nested children toggle button (above children accordion)
    if (hasChildren) {
      html += '<button class="toggle-children-btn" onclick="toggleChildrenAccordion(event, \'' + uniqueId + '\')">Collapse</button>';
    }

    // Nested children accordion
    if (hasChildren) {
      html += '<div class="ui styled accordion">';
      html += buildMfoAccordionHtml(node.children);
      html += '</div>';
    }

    html += '</div>';
  });

  return html;
}

function escapeHtml(text) {
  if (!text) return '';
  return text
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function expandAllAccordion() {
  $('#mfo-accordion .title').addClass('active');
  $('#mfo-accordion .content').addClass('active');
  $('.toggle-children-btn').text('Collapse');
}

function collapseAllAccordion() {
  $('#mfo-accordion .title').removeClass('active');
  $('#mfo-accordion .content').removeClass('active');
  $('.toggle-children-btn').text('Expand');
}

function toggleChildrenAccordion(event, mfoId) {
  event.stopPropagation();

  var $title = $('[data-mfo-id="' + mfoId + '"]').first();
  var $content = $title.next('.content');
  var $nestedAccordion = $content.find('.ui.styled.accordion').first();
  var $btn = $(event.target);

  if ($nestedAccordion.length) {
    var isExpanded = $nestedAccordion.find('.title.active').length > 0;

    if (isExpanded) {
      // Collapse all children
      $nestedAccordion.find('.title').removeClass('active');
      $nestedAccordion.find('.content').removeClass('active');
      $btn.text('Expand');
    } else {
      // Expand all children
      $nestedAccordion.find('.title').addClass('active');
      $nestedAccordion.find('.content').addClass('active');
      $btn.text('Collapse');
    }
  }
}

function displayPrettyJson(data) {
  var jsonString = JSON.stringify(data, null, 2);
  $('#json-display').text(jsonString);
}
