// OrgChart-related functions for RSM tree and OrgTree views

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
              // Map MFO data to orgchart format and initialize orgchart
              var orgchartData = mapMfoToOrgchart(parsedData);
              initializeOrgchart(orgchartData);
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

function mapMfoToOrgchart(mfoNodes) {
  if (!mfoNodes || mfoNodes.length === 0) return {};

  // If multiple root nodes, create a virtual root
  if (mfoNodes.length === 1) {
    return mapSingleMfoToOrgchart(mfoNodes[0]);
  } else {
    // Create a virtual root for multiple top-level nodes
    var virtualRoot = {
      name: "MFO Hierarchy",
      title: "Department Matrix",
      children: mfoNodes.map(mapSingleMfoToOrgchart)
    };
    return virtualRoot;
  }
}

function mapSingleMfoToOrgchart(mfoNode) {
  var orgNode = {
    name: mfoNode.code,
    title: '<span style="font-size: 14px; font-weight: bold;">' + mfoNode.title + '</span>'
  };

  // Add personnel in-charge as bulleted list if available
  if (mfoNode.personnel_incharge && mfoNode.personnel_incharge.length > 0) {
    var personnelList = '<ul style="margin: 5px 0 0 0; padding-left: 20px; font-size: 10px;">';
    mfoNode.personnel_incharge.forEach(function (person) {
      personnelList += '<li>' + person.full_name + '</li>';
    });
    personnelList += '</ul>';
    orgNode.title += personnelList;
  }

  if (mfoNode.children && mfoNode.children.length > 0) {
    orgNode.children = mfoNode.children.map(mapSingleMfoToOrgchart);
  }

  return orgNode;
}

function mapPersonnelToOrgchart(personnelNode) {
  var orgNode = {
    name: '',
    title: '<span style="font-size: 14px; font-weight: bold;">' + personnelNode.name + '</span>'
  };

  if (personnelNode.children && personnelNode.children.length > 0) {
    orgNode.children = personnelNode.children.map(mapPersonnelToOrgchart);
  }

  return orgNode;
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
              // Map personnel data to orgchart format and initialize orgchart
              var orgchartData = mapPersonnelToOrgchart(parsedData);
              initializeOrgchart(orgchartData, 'orgchart-container');
              // Initialize search functionality
              initSearchOrgchart('orgchart-container');
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

function initSearchOrgchart(containerId) {
  var $searchInput = $('#search-input');
  var searchTimeout;

  $searchInput.on('input', function() {
    var searchTerm = $(this).val().trim().toLowerCase();

    // Clear previous timeout
    clearTimeout(searchTimeout);

    // Debounce search to prevent excessive searches
    searchTimeout = setTimeout(function() {
      searchOrgchart(containerId, searchTerm);
    }, 300);
  });
}

function searchOrgchart(containerId, searchTerm) {
  var $container = $('#' + containerId);
  var $chart = $container.find('.orgchart');

  // Remove previous highlights
  $chart.find('.node').removeClass('highlighted');

  if (!searchTerm) {
    return;
  }

  // Search through all nodes
  var $nodes = $chart.find('.node');
  var matchedNode = null;

  $nodes.each(function() {
    var $node = $(this);
    var nodeText = $node.find('.title').text().toLowerCase();

    if (nodeText.includes(searchTerm)) {
      matchedNode = $node;
      return false; // Break the loop after first match
    }
  });

  if (matchedNode) {
    // Highlight the matched node
    matchedNode.addClass('highlighted');

    // Scroll to and center the matched node
    var $chartEl = $container.find('.orgchart');
    var current = getCurrentTranslate($chartEl);

    var containerWidth = $container.width();
    var containerHeight = $container.height();
    var nodeOffset = matchedNode.offset();
    var containerOffset = $container.offset();

    var nodeCenterX = nodeOffset.left - containerOffset.left + (matchedNode.outerWidth() / 2);
    var nodeCenterY = nodeOffset.top - containerOffset.top + (matchedNode.outerHeight() / 2);

    var targetX = containerWidth / 2;
    var targetY = containerHeight / 2;

    var newX = current.x + (targetX - nodeCenterX);
    var newY = current.y + (targetY - nodeCenterY);

    $chartEl.css('transform', 'translate(' + newX + 'px, ' + newY + 'px)');
  }
}

function getCurrentTranslate($el) {
  var transform = $el.css('transform');
  if (!transform || transform === 'none') return { x: 0, y: 0 };

  var match = transform.match(/translate\(([^,]+)px,\s*([^)]+)px\)/);
  if (match) return { x: parseFloat(match[1]), y: parseFloat(match[2]) };

  match = transform.match(/matrix\(([^)]+)\)/);
  if (match) {
    var parts = match[1].split(/,\s*/);
    if (parts.length === 6) {
      return { x: parseFloat(parts[4]), y: parseFloat(parts[5]) };
    }
  }

  return { x: 0, y: 0 };
}

function initializeOrgchart(data, containerId) {
  containerId = containerId || 'chart-container';

  var showDescendents = function (node, depth) {
    if (depth === 1) {
      return false;
    }
    $(node).siblings('.nodes').children()
      .removeClass('isCollapsedDescendant isChildrenCollapsed')
      .find('.node:first').each(function (index, node) {
        var $temp = $(node).siblings('.nodes').removeClass('hidden');
        var $children = $temp.children().removeClass('isCollapsedDescendant').find('.node:first');
        if ($children.length) {
          $children[0].style.offsetWidth = $children[0].offsetWidth;
        }
        $children.removeClass('slide-up');
        showDescendents(node, depth--);
      });
  };

  var getCurrentTranslate = function ($el) {
    var transform = $el.css('transform');
    if (!transform || transform === 'none') return { x: 0, y: 0 };

    var match = transform.match(/translate\(([^,]+)px,\s*([^)]+)px\)/);
    if (match) return { x: parseFloat(match[1]), y: parseFloat(match[2]) };

    match = transform.match(/matrix\(([^)]+)\)/);
    if (match) {
      var parts = match[1].split(/,\s*/);
      if (parts.length === 6) {
        return { x: parseFloat(parts[4]), y: parseFloat(parts[5]) };
      }
    }

    return { x: 0, y: 0 };
  };

  $('#' + containerId).orgchart({
    'data': data,
    'nodeContent': 'title',
    'zoom': true,
    'pan': true,
    // verticalLevel: 2,
    // visibleLevel: 2,
    'initCompleted': function ($chart) {
      var $container = $('#' + containerId);
      $container.scrollLeft(($container[0].scrollWidth - $container.width()) / 2);
    }

  });
}
