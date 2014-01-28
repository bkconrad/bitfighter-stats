
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
							data[data.length-1].x = data.length;
						}
					}

					var xMax = d3.max(data, function(d) { return d[xprop]; });
					var xScale = d3.scale.linear()
						.domain([1, xMax + 2])
						.range([0, w - padding])
						;

					var xAxis = d3.svg.axis()
						.scale(xScale)
						.orient('bottom')
						;

					var yMax = d3.max(data, function(d) { return parseFloat(d[yprop]); });
					console.log(yMax);
					var yScale = d3.scale.linear()
						.domain([0, yMax])
						.range([h, 0])
						;

					var yAxis = d3.svg.axis()
						.scale(yScale)
						.orient('left')
						;

					d3.select(element[0]).select('svg')
						.append('g')
							.attr('stroke', '#EEE')
							.attr('transform', 'translate(' + padding +',' + (h) + ')')
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
							return xScale(d[xprop]) + padding;
						})
						.y(function(d) {
							return yScale(d[yprop]);
						})
						;

					var svg = element.find('svg')[0];

					d3.select(svg)
						.attr('width', w + 2*padding)
						.attr('height', h + padding)
						.style('background', '#000')
						.style('margin', 'auto')
						.style('display', 'block')
						.append('svg:path')
							.data([data])
							.attr('d', line)
							.attr('stroke', '#FFF')
						;


					d3.select(svg)
						.selectAll('circle')
						.data(data)
						.enter().append('svg:circle')
							.attr('cx', function(d) {
								return xScale(d[xprop]) + padding;
							})
							.attr('cy', function(d) {
								return yScale(d[yprop]);
							})
							.attr('r', 6)
							.attr('stroke', '#FFF')
							.attr('fill', '#888')
							.on('mouseover', function(d, i) {
								var $this = d3.select(this);
								$this.attr('fill', '#CCC');
								d3.select(svg).select('text.detail')
									.text(d[yprop])
									.transition()
									.duration(300)
									.attr('x', xScale(d[xprop]))
									.attr('y', yScale(d[yprop]) - padding / 2)
									.style('opacity', 1)
									;
							})
							.on('mouseout', function(d, i) {
								var $this = d3.select(this);
								$this.attr('fill', '#888');
								d3.select(svg).select('text.detail')
									.transition()
									.duration(300)
									.style('opacity', 0)
									;	
							})
							;

					d3.select(svg)
						.append('svg:text')
						.attr('class', 'detail')
						.attr('x', padding)
						.attr('y', padding)
						.attr('stroke', '#FFF')
						;

					console.log(data);

				});
			}
		};
	})
	;
