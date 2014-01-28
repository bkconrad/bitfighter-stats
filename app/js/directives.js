
angular.module('bfstats.directives', [])
	/**
	 * Create overlapping graphs of data
	 * Usage:
	 * 
	 *   <div bf-graph="graphOptions"></div>
	 *   ...
	 *   $scope.graphOptions = {
	 *     data: 'data',
	 *     x: 'x_property',
	 *     y: 'y_property'
	 *   }
	 *   $scope.data = []; // data goes here
	 */
	.directive('bfGraph', function() {
		return {
			template: '<svg></svg>',
			link: function(scope, element, attributes) {
				var options = scope.$eval(attributes['bfGraph']);

				scope.$watch(options.data, function(newVal, oldVal, scope) {
					/* Sizing and scales. */
					var w = 300 * 1.618;
					var h = 300;
					var data = [];
					var x;
					var y;
					var xprop = options.x;
					var yprop = options.y;
					var k;
					var k2;
					var COLORS = [ '#80100F', '#4c1348', '#142030', '#476f26' ];
					var padding = 30;

					// bail if the value is falsey
					if(!newVal) {
						return;
					}

					// convert collections to arrays
					for(k in newVal) {
						if(newVal.hasOwnProperty(k)) {
							data.push(newVal[k]);

							// add an index field to the data
							data[data.length-1].x = data.length-1;
						}
					}

					var xMax = d3.max(data, function(d) { return d[xprop]; });
					var xScale = d3.scale.linear()
						.domain([0, xMax])
						.range([padding, w])
						.nice()
						;

					var xAxis = d3.svg.axis()
						.scale(xScale)
						.orient('bottom')
						;

					var yMax = d3.max(data, function(d) { return parseFloat(d[yprop]); });
					console.log(yMax);
					var yScale = d3.scale.linear()
						.domain([0, yMax * 1.1])
						.range([h, 0])
						;

					var yAxis = d3.svg.axis()
						.scale(yScale)
						.orient('left')
						;

					d3.select(element[0]).select('svg')
						.append('g')
							.attr('stroke', '#EEE')
							.attr('transform', 'translate(0,' + (h) + ')')
							.call(xAxis)
						;

					d3.select(element[0]).select('svg')
						.append('g')
							.attr('stroke', '#EEE')
							.attr('transform', 'translate(' + padding + ',0)')
							.call(yAxis)
						;

					var line = d3.svg.line()
						.x(function(d) {
							return xScale(d[xprop]);
						})
						.y(function(d) {
							return yScale(d[yprop]);
						})
						;

					var svg = element.find('svg')[0];

					// Add a backdrop to trigger the mousemove event
					d3.select(svg).append('svg:rect')
						.attr('x', padding)
						.attr('width', w - padding)
						.attr('height', h)
						// .attr('fill', 'red')
						;

					// Setup the chart properties
					d3.select(svg)
						.attr('width', w + 2*padding)
						.attr('height', h + padding)
						.style('background', '#000')
						.style('margin', 'auto')
						.style('display', 'block')

						// Add the data line
						.append('svg:path')
							.data([data])
							.attr('d', line)
							.attr('fill', 'rgba(0,0,0,0)')
							.attr('stroke', '#888')
							.attr('stroke-width', 3)
						;

					// Set the mouse event handlers
					d3.select(svg)
						.on('mousemove', function() {

							// Find the datum with the closest x-distance
							var x = Math.round(xScale.invert(d3.mouse(svg)[0]));
							console.log(x);
							var datum = data[x];

							// Move the detail text to the closest data point
							d3.select(svg).select('text.detail')
								.transition()
								.ease('linear')
								.duration(100)
								.style('opacity', 1)
								.attr('x', xScale(x))
								.attr('y', yScale(datum[yprop]))
								.text(datum[yprop])
								;

							var bbox = d3.select(svg).select('text.detail').node().getBBox();
							var textWidth = bbox.width;
							var textHeight = bbox.height;

							var BOX_PADDING = 3;
							// Move the detail box to the closest data point
							d3.select(svg).select('rect.detail')
								.transition()
								.ease('linear')
								.duration(100)
								.style('opacity', 1)
								.attr('x', xScale(x) - BOX_PADDING)
								.attr('y', yScale(datum[yprop]) - BOX_PADDING - textHeight)
								.attr('width', textWidth + 2 * BOX_PADDING)
								.attr('height', textHeight + 2 * BOX_PADDING)
								.text(datum[yprop])
								;
						})
						.on('mouseout', function() {
							// Hide the detail pane
							d3.select(svg).selectAll('.detail')
								.transition()
								.duration(300)
								.style('opacity', 0)
								;
						})
						;

					// Create the detail box
					d3.select(svg)
						.append('svg:rect')
						.attr('class', 'detail')
						.attr('x', padding)
						.attr('y', padding)
						.attr('fill', '#222')
						.attr('stroke', '#444')
						;

					// Create the detail text
					d3.select(svg)
						.append('svg:text')
						.attr('class', 'detail')
						.attr('x', padding)
						.attr('y', padding)
						.attr('stroke', '#FFF')
						.style('vertical-align', 'middle')
						;

					console.log(data);

				});
			}
		};
	})
	;
