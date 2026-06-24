// RSM Tree and Org Tree display functions

// Store root node data for access by submission function
var rootNodeData = null;

// Extract the stable MFO cf_ID from a data-mfo-id value ("mfo-<cfId>-<index>")
function mfoIdFromDataAttr(dataMfoId) {
  if (!dataMfoId) return '';
  var parts = dataMfoId.split('-');
  // Drop the leading "mfo" and the trailing index, keep the cf_ID in between
  return parts.slice(1, parts.length - 1).join('-');
}

// Snapshot which MFO sections are currently expanded
function getExpandedMfoIds() {
  var ids = {};
  $('#mfo-accordion .title.active').each(function () {
    var id = mfoIdFromDataAttr($(this).attr('data-mfo-id'));
    if (id) ids[id] = true;
  });
  return ids;
}

// Re-apply expanded/collapsed state after the tree is re-rendered
function applyExpandedMfoIds(ids) {
  $('#mfo-accordion .title').each(function () {
    var $title = $(this);
    var id = mfoIdFromDataAttr($title.attr('data-mfo-id'));
    var $content = $title.next('.content');
    if (ids[id]) {
      $title.addClass('active');
      $content.addClass('active');
    } else {
      $title.removeClass('active');
      $content.removeClass('active');
    }
  });

  // Sync the nested toggle button labels with the restored state
  $('#mfo-accordion .toggle-children-btn').each(function () {
    var $btn = $(this);
    var $nested = $btn.nextAll('.ui.styled.accordion').first();
    var expanded = $nested.find('.title.active').length > 0;
    $btn.text(expanded ? 'Collapse' : 'Expand');
  });
}

function rsm_load_tree(period, year, preserveScroll) {
  // Remember scroll position and expanded sections so saves don't reset the view
  var savedScrollTop = preserveScroll ? $('#mfo-accordion-container').scrollTop() : 0;
  var savedExpanded = preserveScroll ? getExpandedMfoIds() : null;

  // Show loader, hide accordion and controls (skip when refreshing in place to avoid flicker)
  if (!preserveScroll) {
    $('#mfo-loader').show();
    $('#mfo-accordion').hide();
    $('#accordion-controls').hide();
  }

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
              if (preserveScroll) {
                if (savedExpanded) {
                  applyExpandedMfoIds(savedExpanded);
                }
                $('#mfo-accordion-container').scrollTop(savedScrollTop);
              }
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
    rootNodeData = rootNode; // Store for submission function

    // Populate header with department and period-year
    var deptName = rootNode.title || rootNode.code || 'Department';
    //var deptAlias = rootNode.code || '';
    var displayTitle = deptName; //deptAlias ? deptAlias + ' - ' + deptName : deptName;

    $('#rsm-department-title').text(displayTitle);

    // Get period and year from URL
    const urlParams = new URLSearchParams(window.location.search);
    const period = urlParams.get('period');
    const year = urlParams.get('year');
    $('#rsm-period-year').text(period && year ? period + ' ' + year : '');

    // Show header
    $('#rsm-header').show();

    // Show "Add New MFO" and "Submit" only when editing is enabled for this period
    if (rootNode.edit_enabled) {
      $('#add-mfo-btn').show();
      $('#submit-rsm-btn').show();
    } else {
      $('#add-mfo-btn').hide();
      $('#submit-rsm-btn').hide();
    }

    // When the RSM for this period is empty, the period is editable, and a
    // previous-period RSM exists, offer to duplicate it (reuses copyRSM()).
    var isEmpty = !rootNode.children || rootNode.children.length === 0;
    if (isEmpty && rootNode.edit_enabled && rootNode.prev_rsm_exists) {
      $container.html(
        '<div class="empty-children" style="text-align:center; padding:30px;">'
        + '<p>No Rating Scale Matrix has been created for this period yet.</p>'
        + '<button class="ui green large button" onclick="copyRSM()"><i class="copy icon"></i>Copy Previous RSM</button>'
        + '</div>'
      );
      return;
    }

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

function openMfoActionsModal(event, mfoId) {
  event.stopPropagation();
  $("#allModal").modal("setting", "closable", false).modal("show");
  $("#modalContL").html(
    "<div style='text-align: center'><img src='assets/img/loading.gif' style='transform: scale(.1);height:500px'></div>"
  );
  $.post(
    "?config=rsm",
    {
      get_mfo_actions: mfoId,
    },
    function (data) {
      $("#modalContL").html(data);
      $("#modalContL").find(".ui.dropdown").dropdown();
    }
  );
}

function openAddMfoModal() {
  $("#allModal").modal("setting", "closable", false).modal("show");
  var html = ''
    + '<div class="ui form">'
    + '<h2 class="ui horizontal divider"><i class="blue plus square outline icon"></i> Add New MFO <i class="blue plus square outline icon"></i></h2>'
    + '<div class="field">'
    + '<label>Count (e.g. 1 or I.A)</label>'
    + '<input type="text" id="new-mfo-count" placeholder="Count">'
    + '</div>'
    + '<div class="field">'
    + '<label>Title</label>'
    + '<input type="text" id="new-mfo-title" placeholder="MFO/PAP Title">'
    + '</div>'
    + '<button class="mini ui positive fluid button" onclick="saveNewMfo(this)"><i class="save icon"></i> Save</button>'
    + '</div>';
  $("#modalContL").html(html);
}

function saveNewMfo(btn) {
  var count = ($("#new-mfo-count").val() || '').trim();
  var title = ($("#new-mfo-title").val() || '').trim();
  if (!count || !title) {
    alert("Please fill in both Count and Title.");
    return;
  }
  $(btn).addClass('loading disabled');
  $.post(
    "?config=rsm",
    {
      addRSMData: title,
      rsmCount: count,
      pid: '',
    },
    function (data) {
      if (data == 1) {
        rsmLoad("table");
      } else {
        $(btn).removeClass('loading disabled');
        alert(data);
      }
    }
  );
}

function buildMfoAccordionHtml(mfoNodes) {
  if (!mfoNodes || mfoNodes.length === 0) {
    return '<div class="empty-children">No MFO items</div>';
  }

  var html = '';

  mfoNodes.forEach(function (node, index) {
    var hasChildren = node.children && node.children.length > 0;
    var titleClass = hasChildren ? '' : 'disabled';
    var uniqueId = 'mfo-' + node.id + '-' + index;

    html += '<div class="' + (hasChildren ? 'active' : '') + ' title ' + titleClass + '" data-mfo-id="' + uniqueId + '">';
    html += '<i class="dropdown icon"></i>';
    if (node.can_edit) {
      html += '<i class="edit icon mfo-edit-btn" title="Edit MFO" onclick="openMfoActionsModal(event, \'' + node.id + '\')"></i>';
    }
    if (node.code) {
      html += '<span class="mfo-code">' + escapeHtml(node.code) + '</span>';
    }
    html += escapeHtml(node.title);
    html += '</div>';

    html += '<div class="' + (hasChildren ? 'active' : '') + ' content">';

    // Success indicators (with personnel in-charge shown per indicator)
    if (node.success_indicators && node.success_indicators.length > 0) {
      html += '<div class="ui list">';
      html += '<div class="item"><strong>Success Indicators:</strong></div>';
      node.success_indicators.forEach(function (si) {
        html += '<div class="success-indicator-item">';
        html += buildSiCorrectionsHtml(si);
        html += '<div class="si-header">';
        html += '<div class="si-description">' + escapeHtml(si.description) + '</div>';
        if (node.can_edit) {
          html += '<div class="si-actions">';
          html += '<i class="edit icon si-edit-btn" title="Edit Success Indicator" onclick="event.stopPropagation(); siEditOpenModal(\'' + si.id + '\')"></i>';
          html += '<i class="trash icon si-delete-btn" title="Delete Success Indicator" onclick="event.stopPropagation(); deleteOpenModal(\'' + si.id + '\')"></i>';
          html += '</div>';
        }
        html += '</div>';
        html += buildQetMeasuresHtml(si);
        if (si.personnel_incharge && si.personnel_incharge.length > 0) {
          html += '<div class="si-personnel" style="margin-top: 8px;">';
          html += '<strong>Personnel In-Charge:</strong><br>';
          html += buildPersonnelTagsHtml(si.personnel_incharge);
          html += '</div>';
        }
        html += '</div>';
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

function buildSiCorrectionsHtml(si) {
  if (!si.corrections || si.corrections.length === 0) return '';

  var anyPending = si.corrections.some(function (c) { return !c.accomplished; });
  var ribbonColor = anyPending ? 'red' : 'green';

  var html = '';
  html += '<div class="si-corrections">';
  html += '<a class="ui ' + ribbonColor + ' ribbon label"><i class="exclamation circle icon"></i> Corrections</a>';
  html += '<div class="si-correction-list">';
  si.corrections.forEach(function (c) {
    var stateLabel = c.accomplished
      ? '<span class="si-correction-status accomplished">Accomplished</span>'
      : '<span class="si-correction-status unaccomplished">Unaccomplished</span>';
    // c.comment is system-generated HTML (name/date/text)
    html += '<div class="si-correction-item">' + c.comment + ' - ' + stateLabel + '</div>';
  });
  html += '</div>';
  html += '</div>';
  return html;
}

function buildPersonnelTagsHtml(personnel) {
  var html = '';
  personnel.forEach(function (person) {
    var tagClass = 'personnel-tag personnel-tag-clickable';
    if (person.is_department_head) {
      tagClass = 'personnel-tag personnel-tag-clickable personnel-tag-dept-head';
    } else if (person.is_supervisor) {
      tagClass = 'personnel-tag personnel-tag-clickable personnel-tag-supervisor';
    }
    html += '<span class="' + tagClass + '" title="View Individual Rating Scale" onclick="openPersonnelIpcr(event, \'' + person.employee_id + '\')">' + escapeHtml(person.full_name) + '</span>';
  });
  return html;
}

function buildQetMeasuresHtml(si) {
  var measures = [
    { key: 'quality', label: 'Quality' },
    { key: 'efficiency', label: 'Efficiency' },
    { key: 'timeliness', label: 'Timeliness' }
  ];

  // Check if there is any QET data at all
  var hasAny = measures.some(function (m) {
    return si[m.key] && si[m.key].length > 0;
  });
  if (!hasAny) return '';

  var html = '';
  html += '<button class="qet-toggle-btn" onclick="toggleQetMeasures(event, this)">Show Measures</button>';
  html += '<div class="qet-measures">';

  measures.forEach(function (m) {
    var items = si[m.key] || [];
    if (items.length === 0) return;
    html += '<div class="qet-column">';
    html += '<div class="qet-column-label">' + m.label + '</div>';
    items.forEach(function (item) {
      html += '<div class="qet-item"><span class="qet-score">' + escapeHtml(String(item.score)) + '</span> ' + escapeHtml(item.descriptor) + '</div>';
    });
    html += '</div>';
  });

  html += '</div>';
  return html;
}

function openPersonnelIpcr(event, employeeId) {
  event.stopPropagation();
  if (!employeeId) return;

  var urlParams = new URLSearchParams(window.location.search);
  var period = urlParams.get('period');
  var year = urlParams.get('year');

  var url = '?config=RSMipcrView&emp=' + encodeURIComponent(employeeId);
  if (period && year) {
    url += '&period=' + encodeURIComponent(period) + '&year=' + encodeURIComponent(year);
  }
  window.open(url, '_blank');
}

function printRsmMatrix() {
  var urlParams = new URLSearchParams(window.location.search);
  var period = urlParams.get('period');
  var year = urlParams.get('year');

  var url = '?config=rsm&rsm_print=1';
  if (period && year) {
    url += '&period=' + encodeURIComponent(period) + '&year=' + encodeURIComponent(year);
  }
  window.open(url, '_blank');
}

function submitRsmMatrix() {
  if (!rootNodeData || !rootNodeData.rsm_status_id) {
    alert("Unable to submit: RSM status ID not found.");
    return;
  }
  var rsmStatusId = rootNodeData.rsm_status_id;
  var confirmed = confirm("Are you sure you want to submit the Rating Scale Matrix? This will lock editing for this period.");
  if (!confirmed) return;

  $.post(
    "?config=rsm",
    {
      closeRsm: rsmStatusId
    },
    function (data, textStatus, xhr) {
      if (data == 1) {
        rsmLoad("table");
      } else {
        alert("Submission failed: " + data);
      }
    }
  );
}

function toggleQetMeasures(event, btn) {
  event.stopPropagation();
  var $btn = $(btn);
  var $measures = $btn.next('.qet-measures');
  if ($measures.hasClass('qet-visible')) {
    $measures.removeClass('qet-visible');
    $btn.text('Show Measures');
  } else {
    $measures.addClass('qet-visible');
    $btn.text('Hide Measures');
  }
}

function toggleAllQetMeasures() {
  var $measures = $('#mfo-accordion .qet-measures');
  if ($measures.length === 0) return;

  // If any is hidden, show all; otherwise hide all
  var anyHidden = $measures.not('.qet-visible').length > 0;
  if (anyHidden) {
    $measures.addClass('qet-visible');
    $('.qet-toggle-btn').text('Hide Measures');
    $('#toggle-measures-btn').text('Hide All Measures');
  } else {
    $measures.removeClass('qet-visible');
    $('.qet-toggle-btn').text('Show Measures');
    $('#toggle-measures-btn').text('Show All Measures');
  }
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
