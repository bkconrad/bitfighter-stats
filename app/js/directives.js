
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
	 *     y: ['y_property1', 'y_property2' ]
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
					var yprop;
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
							newVal[k].players_per_game = parseFloat(newVal[k].players_per_game);
							data.push(newVal[k]);

							// add an index field to the data
							data[data.length-1].x = data.length;
						}
					}

					var xMax = d3.max(data, function(d) { return d.x; });
					var xScale = d3.scale.linear()
						.domain([1, xMax + 2])
						.range([0, w - padding])
						;

					var xAxis = d3.svg.axis()
						.scale(xScale)
						.orient('bottom')
						;

					var yMax = d3.max(data, function(d) { return d.players_per_game; });
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

					d3.select(element[0]).select('svg')
						.attr('width', w + 2*padding)
						.attr('height', h + padding)
						.style('background', '#000')
						.style('margin', 'auto')
						.style('display', 'block')
					.selectAll('rect')
						.data(data)
						.enter()
						.append('rect')
						.attr('fill', '#EEE')
						.attr('x', function(d) {
							return xScale(d.x) + padding + w / data.length / 2;
						})
						.attr('y', function(d) {
							return h - yScale(d.players_per_game);
						})
						.attr('width', w / data.length - 5)
						.attr('height', function(d) {
							return yScale(d.players_per_game);
						})
						;
					console.log(data);
				});
			}
		};
	})
	;
