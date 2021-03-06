Sequence = (function($) {

    // Dimensions of sunburst.
    var width = 850;
    var height = 700;
    var radius = Math.min(width, height) / 2;

    // Breadcrumb dimensions: width, height, spacing, width of tip/tail.
    var b = {
        w: 200,
        h: 30,
        s: 3,
        t: 8
    };

    // Mapping of step names to colors.
    /*var colors = {
        "Feminino": "#5687d1",
        "Masculino": "#7b615c",
        "2000": "#de783b",
        "2001": "#6ab975",
        "2002": "#a173d1",
        "2003": "#bbbbbb",
        "1996": getRandomColor(),
        "1997": getRandomColor(),
        "1998": getRandomColor(),
        "1999": getRandomColor(),
        "451": getRandomColor(),
    };*/

    var colors = {};

    // Total size of all segments; we set this later, after loading the data.
    var totalSize = 0;
    var vis, partition, arc;

    function initialize() {
        $("#chart > svg").remove();
        $("#sequence > trail > svg").remove();
        $("#legend > svg").remove();
        colors = {};

        wait();
        vis = d3.select("#escopo_sequence #chart").append("svg:svg")
            .attr("width", width)
            .attr("height", height)
            .append("svg:g")
            .attr("id", "container")
            .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

        partition = d3.layout.partition()
            .size([2 * Math.PI, radius * radius])
            .value(function(d) {
                return d.size;
            });

        arc = d3.svg.arc()
            .startAngle(function(d) {
                return d.x;
            })
            .endAngle(function(d) {
                return d.x + d.dx;
            })
            .innerRadius(function(d) {
                return Math.sqrt(d.y);
            })
            .outerRadius(function(d) {
                return Math.sqrt(d.y + d.dy);
            });

        $.ajax({
                url: "http://localhost/visualizacao/servidor/sequence.php?codMun=" + getIdMunicipioSelecionado() + "&nivel1=" + $("#nivel1").val() + "&nivel2=" + $("#nivel2").val() + "&nivel3=" + $("#nivel3").val() + "&nivel4=" + $("#nivel4").val() + "&nivel5=" + $("#nivel5").val() + "&nivel6=" + $("#nivel6").val()
            })
            .done(function(data) {
                renderSubBurst();
            });
    }

    function renderSubBurst() {
        d3.text("http://localhost/visualizacao/servidor/sequence.csv", function(text) {
            var csv = d3.csv.parseRows(text);
            var json = buildHierarchy(csv);
            createVisualization(json);
        });
        closeWait();
    }

    // Main function to draw and set up the visualization, once we have the data.
    function createVisualization(json) {

        // Basic setup of page elements.
        d3.select("#escopo_sequence #togglelegend").on("click", toggleLegend);

        // Bounding circle underneath the sunburst, to make it easier to detect
        // when the mouse leaves the parent g.
        vis.append("svg:circle")
            .attr("r", radius)
            .style("opacity", 0);

        // For efficiency, filter nodes to keep only those large enough to see.
        var nodes = partition.nodes(json)
            .filter(function(d) {
                return (d.dx > 0.005); // 0.005 radians = 0.29 degrees
            });

        var path = vis.data([json]).selectAll("#escopo_sequence path")
            .data(nodes)
            .enter().append("svg:path")
            .attr("display", function(d) {
                return d.depth ? null : "none";
            })
            .attr("d", arc)
            .attr("fill-rule", "evenodd")
            .style("fill", function(d) {
                if (!colors[d.name]) {
                    while (true)  {
                        var corTmp = getRandomColor();
                        var pode = true;
                        $.each(colors, function(key, value) {
                            if (value == corTmp) {
                                pode = false;
                            }
                        });
                        if (pode) {
                            colors[d.name] = corTmp;
                            break;
                        }
                    }
                }
                return colors[d.name];
            })
            .style("opacity", 1)
            .on("mouseover", mouseover);

            initializeBreadcrumbTrail();
            drawLegend();
        

        // Add the mouseleave handler to the bounding circle.
        d3.select("#escopo_sequence #container").on("mouseleave", mouseleave);

        // Get total size of the tree = value of root node from partition.
        totalSize = path.node().__data__.value;
    };

    // Fade all but the current sequence, and show it in the breadcrumb trail.
    function mouseover(d) {

        var percentage = (100 * d.value / totalSize).toPrecision(3);
        var percentageString = percentage + "%";
        if (percentage < 0.1) {
            percentageString = "< 0.1%";
        }

        percentageString = percentageString.replace(".", ",");

        d3.select("#escopo_sequence #percentage")
            .text(percentageString);

        d3.select("#escopo_sequence #explanation")
            .style("visibility", "");

        var sequenceArray = getAncestors(d);
        updateBreadcrumbs(sequenceArray, percentageString);

        // Fade all the segments.
        d3.selectAll("#escopo_sequence path")
            .style("opacity", 0.3);

        // Then highlight only those that are an ancestor of the current segment.
        vis.selectAll("#escopo_sequence path")
            .filter(function(node) {
                return (sequenceArray.indexOf(node) >= 0);
            })
            .style("opacity", 1);
    }

    // Restore everything to full opacity when moving off the visualization.
    function mouseleave(d) {

        // Hide the breadcrumb trail
        d3.select("#escopo_sequence #trail")
            .style("visibility", "hidden");

        // Deactivate all segments during transition.
        d3.selectAll("#escopo_sequence path").on("mouseover", null);

        // Transition each segment to full opacity and then reactivate it.
        d3.selectAll("#escopo_sequence path")
            .transition()
            .duration(1000)
            .style("opacity", 1)
            .each("end", function() {
                d3.select(this).on("mouseover", mouseover);
            });

        d3.select("#escopo_sequence #explanation")
            .style("visibility", "hidden");
    }

    // Given a node in a partition layout, return an array of all of its ancestor
    // nodes, highest first, but excluding the root.
    function getAncestors(node) {
        var path = [];
        var current = node;
        while (current.parent) {
            path.unshift(current);
            current = current.parent;
        }
        return path;
    }

    function initializeBreadcrumbTrail() {
        // Add the svg area.
        var trail = d3.select("#escopo_sequence #sequence").append("svg:svg")
            .attr("width", width)
            .attr("height", 50)
            .attr("id", "trail");
        // Add the label at the end, for the percentage.
        trail.append("svg:text")
            .attr("id", "endlabel")
            .style("fill", "#000");
    }

    // Generate a string that describes the points of a breadcrumb polygon.
    function breadcrumbPoints(d, i) {
        var points = [];
        points.push("0,0");
        points.push(b.w + ",0");
        points.push(b.w + b.t + "," + (b.h / 2));
        points.push(b.w + "," + b.h);
        points.push("0," + b.h);
        if (i > 0) { // Leftmost breadcrumb; don't include 6th vertex.
            points.push(b.t + "," + (b.h / 2));
        }
        return points.join(" ");
    }

    // Update the breadcrumb trail to show the current sequence and percentage.
    function updateBreadcrumbs(nodeArray, percentageString) {
        // Data join; key function combines name and depth (= position in sequence).
        var g = d3.select("#escopo_sequence #trail")
            .selectAll("g")
            .data(nodeArray, function(d) {
                return d.name + d.depth;
            });

        // Add breadcrumb and label for entering nodes.
        var entering = g.enter().append("svg:g");

        entering.append("svg:polygon")
            .attr("points", breadcrumbPoints)
            .style("fill", function(d) {
                return colors[d.name];
            });

        entering.append("svg:text")
            .attr("x", (b.w + b.t) / 2)
            .attr("y", b.h / 2)
            .attr("dy", "0.35em")
            .attr("text-anchor", "middle")
            .text(function(d) {
                if (d.name.length > 28) {
                    return d.name.substr(0, 28);
                }
                return d.name;
            });

        // Set position for entering and updating nodes.
        g.attr("transform", function(d, i) {
            return "translate(" + i * (b.w + b.s) + ", 0)";
        });

        // Remove exiting nodes.
        g.exit().remove();

        // Now move and update the percentage at the end.
        d3.select("#escopo_sequence #trail").select("#endlabel")
            .attr("x", (nodeArray.length + 0.5) * (b.w + b.s))
            .attr("y", b.h / 2)
            .attr("dy", "0.35em")
            .attr("text-anchor", "middle")
            .text(percentageString);

        // Make the breadcrumb trail visible, if it's hidden.
        d3.select("#escopo_sequence #trail")
            .style("visibility", "");

    }

    function drawLegend() {

        // Dimensions of legend item: width, height, spacing, radius of rounded rect.
        var li = {
            w: 190,
            h: 30,
            s: 3,
            r: 3
        };

        var legend = d3.select("#escopo_sequence #legend").append("svg:svg")
            .attr("width", li.w)
            .attr("height", d3.keys(colors).length * (li.h + li.s));

        var g = legend.selectAll("g")
            .data(d3.entries(colors))
            .enter().append("svg:g")
            .attr("transform", function(d, i) {
                return "translate(0," + i * (li.h + li.s) + ")";
            });

        g.append("svg:rect")
            .attr("rx", li.r)
            .attr("ry", li.r)
            .attr("width", li.w)
            .attr("height", li.h)
            .style("fill", function(d) {
                return d.value;
            });

        g.append("svg:text")
            .attr("x", li.w / 2)
            .attr("y", li.h / 2)
            .attr("dy", "0.35em")
            .attr("text-anchor", "middle")
            .text(function(d) {
                return d.key;
            });
    }

    function toggleLegend() {
        var legend = d3.select("#escopo_sequence #legend");
        if (legend.style("visibility") == "hidden") {
            legend.style("visibility", "");
        } else {
            legend.style("visibility", "hidden");
        }
    }

    // Take a 2-column CSV and transform it into a hierarchical structure suitable
    // for a partition layout. The first column is a sequence of step names, from
    // root to leaf, separated by hyphens. The second column is a count of how 
    // often that sequence occurred.
    function buildHierarchy(csv) {
        var root = { "name": "root", "children": [] };
        for (var i = 0; i < csv.length; i++) {
            var sequence = csv[i][0];
            var size = +csv[i][1];
            if (isNaN(size)) { // e.g. if this is a header row
                continue;
            }
            var parts = sequence.split("-");
            var currentNode = root;
            for (var j = 0; j < parts.length; j++) {
                var children = currentNode["children"];
                var nodeName = parts[j];
                var childNode;
                if (j + 1 < parts.length) {
                    // Not yet at the end of the sequence; move down the tree.
                    var foundChild = false;
                    for (var k = 0; k < children.length; k++) {
                        if (children[k]["name"] == nodeName) {
                            childNode = children[k];
                            foundChild = true;
                            break;
                        }
                    }
                    // If we don't already have a child node for this branch, create it.
                    if (!foundChild) {
                        childNode = { "name": nodeName, "children": [] };
                        children.push(childNode);
                    }
                    currentNode = childNode;
                } else {
                    // Reached the end of the sequence; create a leaf node.
                    childNode = { "name": nodeName, "size": size };
                    children.push(childNode);
                }
            }
        }
        return root;
    };

    return {
        'initialize': initialize,
    };
})(jQuery);
//htmlprettify
