
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

				    x = pv.Scale.linear(data, function(d) { return d.x; }).range(0, w);
				    y = pv.Scale.linear(0, 4).range(0, h);

					/* The root panel. */
					var vis = new pv.Panel()
						.fillStyle('#000')
					    .width(w)
					    .height(h)
					    .left(30)
					    .bottom(30)
					    ;

					/* Y-axis and ticks. */
					vis.add(pv.Rule)
					    .data(y.ticks(5))
					    .bottom(y)
					    .strokeStyle(function(d) { return d ? "#222" : "#000"; })
					  .anchor("left").add(pv.Label)
					  	.textStyle('#EEE')
					    .text(y.tickFormat);

					/* X-axis and ticks. */
					vis.add(pv.Rule)
					    .data(x.ticks())
					    .visible(function(d) { return d; })
					    .left(x)
					    .bottom(-5)
					    .height(5)
					  .anchor("bottom").add(pv.Label)
					  	.textStyle('#EEE')
					    .text(x.tickFormat);

					for(k in options.y) {
						if(options.y.hasOwnProperty(k)) {
							yprop = options.y[k];
							values = [];
							min = 0;
							max = -Infinity;

							// collect the values
							for(k2 in data) {
								if(data.hasOwnProperty(k2)) {
									value = parseFloat(data[k2][yprop]);
									values.push(value);
									min = Math.min(min, value);
									max = Math.max(max, value);
								}
							}

						    y = pv.Scale.linear(min, max).range(0, h);

							vis.add(pv.Line)
								.data(data)
							    .bottom(1)
							    .left(function(d) { return x(d.x); })
							    .top(function(scale,prop,datum) { return scale(parseFloat(datum[prop])); }.bind(false, y, yprop))
							    .strokeStyle(COLORS.pop())
							    .lineWidth(3);

						}
					}
					vis.canvas(element[0]);
					vis.render();	
				});
			}
		};
	})
	;
