Parallel = (function($) {

    var m = [30, 10, 10, 10],
        w = 1100,
        h = 500;

    var x = d3.scale.ordinal().rangePoints([0, w], 1),
        y = {};

    var line = d3.svg.line(),
        axis = d3.svg.axis().orient("left"),
        fisheye = d3.fisheye.scale(d3.scale.identity).domain([0, w]).focus(w / 2).distortion(3),
        background,
        foreground;


    // Returns the path for a given data point.
    function path(d) {
        return line(dimensions.map(function(p) {
            return [fisheye(x(p)), y[p](d[p])];
        }));
    };

    // Handles a brush event, toggling the display of foreground lines.
    function brush() {
        var actives = dimensions.filter(function(p) {
                return !y[p].brush.empty();
            }),
            extents = actives.map(function(p) {
                return y[p].brush.extent();
            });
        foreground.style("display", function(d) {
            return actives.every(function(p, i) {
                return extents[i][0] <= d[p] && d[p] <= extents[i][1];
            }) ? null : "none";
        });
    };


    function initialize() {
        $("#parallel > svg").remove();
        $.ajax({
                url: "http://localhost/visualizacao/servidor/parallel.php?codMun=" + getIdMunicipioSelecionado()
            })
            .done(function(data) {
                _renderParallel();
            });
    }

    function _renderParallel() {

        var svg = d3.select("#parallel").append("svg:svg")
            .attr("width", w + m[1] + m[3])
            .attr("height", h + m[0] + m[2])
            .append("svg:g")
            .attr("transform", "translate(" + m[3] + "," + m[0] + ")");

        d3.csv("http://localhost/visualizacao/servidor/parallel.csv", function(cars) {

            // Extract the list of dimensions and create a scale for each.
            x.domain(dimensions = d3.keys(cars[0]).filter(function(d) {
                return d != "name" && d != "group" && (y[d] = d3.scale.linear()
                    .domain(d3.extent(cars, function(p) {
                        return +p[d];
                    }))
                    .range([h, 0]));
            }));

            // Add grey background lines for context.
            background = svg.append("svg:g")
                .attr("class", "background")
                .selectAll("path")
                .data(cars)
                .enter().append("svg:path")
                .attr("d", path);

            // Add blue foreground lines for focus.
            foreground = svg.append("svg:g")
                .attr("class", "foreground")
                .selectAll("path")
                .data(cars)
                .enter().append("svg:path")
                .attr("d", path);

            // Add a group element for each dimension.
            var g = svg.selectAll(".dimension")
                .data(dimensions)
                .enter().append("svg:g")
                .attr("class", "dimension")
                .attr("transform", function(d) {
                    return "translate(" + fisheye(x(d)) + ")";
                });

            // Add an axis and title.
            g.append("svg:g")
                .attr("class", "axis")
                .each(function(d) { d3.select(this).call(axis.scale(y[d])); })
                .append("svg:text")
                .attr("text-anchor", "middle")
                .attr("y", -9)
                .text(String);

            // Add and store a brush for each axis.
            g.append("svg:g")
                .attr("class", "brush")
                .each(function(d) { d3.select(this).call(y[d].brush = d3.svg.brush().y(y[d]).on("brush", brush)); })
                .selectAll("rect")
                .attr("x", -8)
                .attr("width", 16);

            // Update fisheye effect with mouse move.
            svg.on("mousemove", function() {
                fisheye.focus(d3.mouse(this)[0]);

                foreground.attr("d", path);
                background.attr("d", path);
                g.attr("transform", function(d) {
                    return "translate(" + fisheye(x(d)) + ")";
                });
            });
        });

        closeWait();
    }

    return {
        'initialize': initialize

    };
})(jQuery);
