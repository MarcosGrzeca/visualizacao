function teste() {
	// body...
/*
  var numbers = [ 5, 4, 10, 1 ],
  data = [
  { date: '2014-01-01', amount: 10 },
  { date: '2014-02-01', amount: 20 },
  { date: '2014-03-01', amount: 40 },
  { date: '2014-04-01', amount: 80 }
  ];

  d3.min(numbers);
  d3.max(data, function(d, i) { return d.amount });
  d3.extent(numbers);
  */
    var sales = [
    { product: 'Hoodie',  count: 7 },
    { product: 'Jacket',  count: 6 },
    { product: 'Snuggie', count: 9 },
    ];

    var svg = d3.select('svg');
    svg.size();
    var rects = svg.selectAll('rect').data(sales);
    rects.size();
    console.info("TESTE");
    var newRects = rects.enter();
var maxCount = d3.max(sales, function(d, i) {
  return d.count;
});
var x = d3.scale.linear()
  .range([0, 300])
  .domain([0, maxCount]);
var y = d3.scale.ordinal()
  .rangeRoundBands([0, 75])
  .domain(sales.map(function(d, i) {
    return d.product;
  }));

newRects.append('rect')
  .attr('x', x(0))
  .attr('y', function(d, i) {
    return y(d.product);
  })
  .attr('height', y.rangeBand())
  .attr('width', function(d, i) {
    return x(d.count);
  });
}

function toggle() {
  sales = (sales == days[0]) ? days[1] : days[0];
  update();
}

function update() {
  var rects = svg.selectAll('rect')
    .data(sales, function(d, i) {
      return d.product
    });

  // When we enter, we just want to create the rect,
  rects.enter()
    .append('rect');

  // We configure the rects here so the values
  // apply to it applies to both new and existing
  // rects
  rects
    .attr('x', x(0))
    .attr('y', function(d, i) {
      return y(d.product);
    })
    .attr('height', y.rangeBand())
    .attr('width', function(d, i) {
      return x(d.count);
    });
};
    