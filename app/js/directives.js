var COLOR = {
    axis: '#FFF',
    datum: '#888',
    line: '#888',
    highlight: '#EEE',
    outline: '#444',
    backdrop: '#000',
    background: '#222',
    text: '#CCC'
};

var DURATION = 300;

function bfAxis(axis) {
    return axis
        .attr('stroke', COLOR.axis)
        .selectAll('text')
        .attr('stroke', 'transparent')
        .attr('fill', COLOR.axis)
        .style('font-weight', 'normal');
}

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
.directive('bfGraph', function () {
    return {
        template: '<svg></svg>',
        link: function (scope, element, attributes) {
            var options = scope.$eval(attributes.bfGraph);

            scope.$watch(options.data, function (newVal) {
                var w = 300 * 1.618;
                var h = 300;
                var data = [];
                var k;
                var padding = 30;
                var xprop = options.x;
                var yprop = options.y;
                var headerText;
                var headerBBox;
                var min;
                var max;

                // bail if the value is falsey
                if (!newVal) {
                    return;
                }

                // convert collections to arrays
                for (k in newVal) {
                    if (newVal.hasOwnProperty(k)) {
                        data.push(newVal[k]);
                    }
                }

                // find the x position of the given datum
                function xOf(d) {
                	return xScale(moment(d[xprop], 'YYYY-MM-DD').toDate());
                }

                min = data.reduce(function(a, b) {
                	return a[xprop] < b[xprop] ? a : b;
                })[xprop];

                max = data.reduce(function(a, b) {
                	return a[xprop] > b[xprop] ? a : b;
                })[xprop];

                var domain = [
                	moment(min, 'YYYY-MM-DD').toDate(),
                	moment(max, 'YYYY-MM-DD').toDate()
                ];

                var xScale = d3.time.scale()
                    .domain(domain)
                    .range([padding, w])
                    .nice();

                var xAxis = d3.svg.axis()
                    .scale(xScale)
                    .ticks(4)
                    .orient('bottom');

                // draw x axis
                d3.select(element[0]).select('svg')
                    .append('g')
                    .attr('stroke', COLOR.axis)
                    .attr('transform', 'translate(0,' + h + ')')
                    .call(xAxis)
                    .call(bfAxis);

                // build a y scale from 0 to the max y value plus some head room
                var yMax = d3.max(data, function (d) {
                    return parseFloat(d[yprop]);
                });

                var yScale = d3.scale.linear()
                    .domain([0, yMax * 1.1])
                    .range([h, 0]);

                var yAxis = d3.svg.axis()
                    .scale(yScale)
                    .orient('left');

                // draw y axis
                d3.select(element[0]).select('svg')
                    .append('g')
                    .attr('stroke', COLOR.axis)
                    .attr('transform', 'translate(' + padding + ',0)')
                    .call(yAxis)
                    .call(bfAxis);

                // create line generator
                var line = d3.svg.line()
                    .x(function (d) {
                        return xOf(d);
                    })
                    .y(function (d) {
                        return yScale(d[yprop]);
                    });

                // shorthand svg variable
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
                    .attr('width', w + 2 * padding)
                    .attr('height', h + padding)
                    .style('background', COLOR.backdrop)
                    .style('margin', 'auto')
                    .style('display', 'block')

                // Add the data line
                .append('svg:path')
                    .data([data])
                    .attr('d', line)
                    .attr('fill', 'rgba(0,0,0,0)')
                    .attr('stroke', COLOR.line)
                    .attr('stroke-width', 1);


                // create the detail box for this rect
                d3.select(svg).append('g')
                    .attr('class', 'detail')
                    .style('opacity', 0)
                    .append('rect')
                    .attr('fill', COLOR.background)
                    .attr('stroke', COLOR.outline);

                // create detail text
                d3.select(svg).select('g.detail')
                    .append('text')
                    .attr('class', 'detail')
                    .attr('fill', COLOR.text);

                // Set the mouse event handlers
                d3.select(svg)
                    .on('mousemove', function () {

                        // Find the datum with the closest x-distance
                        var date = moment(xScale.invert(d3.mouse(svg)[0])).format('YYYY-MM-DD');
                        var datum = data.reduce(function(last, current, index) {
                        	if(current[xprop] == date) {
                        		return current;
                        	}
                        	return last;
                        }, false);

                        var prettyDate = moment(date, 'YYYY-MM-DD').format('MMM DD')
                        var textBBox;
                        var detailRectPadding = 3;
                        var boxPos;

                        // show detail text
                        d3.select(svg).select('g.detail text')
                            .text(prettyDate + ': ' + datum[yprop]);

                        textBBox = d3.select(svg).select('text.detail')
                            .node().getBBox();

                        boxPos = {
                            x: xOf(datum) - textBBox.width / 2,
                            y: yScale(datum[yprop])
                        };

                        boxPos.x = Math.min(Math.max(boxPos.x, 0), w - textBBox.width);
                        boxPos.y = Math.min(Math.max(boxPos.y, textBBox.height), h);

                        // size detail box to text
                        d3.select(svg).select('g.detail rect')
                            .attr('width', textBBox.width + detailRectPadding * 2)
                            .attr('height', textBBox.height + detailRectPadding * 2)
                            .attr('x', -detailRectPadding)
                            .attr('y', -textBBox.height);

                        // fade in detail box
                        d3.select(svg).select('g.detail')
                            .transition()
                            .duration(100)
                            .attr('transform', 'translate(' + boxPos.x + ',' + boxPos.y + ')')
                            .style('opacity', 1);
                    })
                    .on('mouseout', function () {
                        // Hide the detail pane
                        d3.select(svg).selectAll('g.detail')
                            .transition()
                            .duration(DURATION)
                            .style('opacity', 0);
                    });

                // Add a title header
                headerText = d3.select(svg)
                    .append('svg:text')
                    .text(options.header)
                    .attr('class', 'header')
                    .attr('fill', COLOR.text)
                    .attr('font-weight', 'bold');

                headerBBox = headerText.node().getBBox();
                headerText
                    .attr('text-anchor', 'middle')
                    .attr('x', (w - padding) / 2 + padding)
                    .attr('y', headerBBox.height);
            });
        }
    };
})
    .directive('bfRank', function () {
        return {
            template: '<svg></svg>',
            transclude: true,
            link: function (scope, element, attributes) {
                var barBaseSize = 3;
                var binMax;
                var bucketWidth;
                var buckets = 30;
                var data = [];
                var h = 15;
                var hist;
                var histData;
                var k;
                var playerBucket;
                var playerRank;
                var prop = scope.$eval(attributes.bfRank);
                var stats = scope.$eval('stats');
                var svg = d3.select(element[0]).select('svg');
                var w = 100;
                var yExtent;
                var yScale;

                var accessor = function (d) {
                    return parseFloat(d[prop]);
                };

                scope.$watchCollection('stats', function(newVal) {
                	data = [];
	                for (k in newVal) {
	                    if (newVal.hasOwnProperty(k)) {
	                        data.push(newVal[k]);
	                    }
	                }

	                yExtent = d3.extent(data, accessor);

	                hist = d3.layout.histogram()
	                    .value(function (d) {
	                        return d[prop];
	                    })
	                    .range(yExtent)
	                    .bins(buckets);

	                histData = hist(data);

	                binMax = d3.max(histData, function (d) {
	                    return d.y;
	                });

	                var yMin = 0.1;
	                yScale = d3.scale.log()
	                    .domain([yMin, binMax])
	                    .range([h - barBaseSize, 0])
	                    .clamp(true);

	                svg.attr('width', w);
	                svg.attr('height', h);

	                bucketWidth = Math.round(w / buckets);

	                function configureBins(selection) {
	                	selection
		                    .attr('x', function (d, i) {
		                        return Math.floor(bucketWidth * i);
		                    })
		                    .attr('y', function (d) {
		                        return Math.floor(yScale(Math.max(d.y, yMin)));
		                    })
		                    .attr('width', Math.floor(bucketWidth - 2))
		                    .attr('height', function (d) {
		                        return Math.ceil(h - Math.floor(yScale(Math.max(d.y, yMin)))) + barBaseSize;
		                    });
	                }

	                svg.selectAll('rect')
	                    .data(histData)
	                    .call(configureBins)
                    .enter().append('svg:rect')
	                    .call(configureBins)
	                    ;
                });

                scope.$watchCollection('player', function (newVal) {
                    var i;

                    // find bucket
                    for (i = 0; i < histData.length; i++) {
                        playerBucket = i;
                        if (histData[i].x >= parseFloat(newVal[prop])) {
                            break;
                        }
                    }

                    // find rank
                    data = data.sort(function (a, b) {
                        return (+a[prop]) - (+b[prop]);
                    });
                    for (i = data.length - 1; i >= 0; i--) {
                        playerRank = data.length - i;
                        if (accessor(data[i]) <= accessor(newVal)) {
                            break;
                        }
                    }
                    scope.rank = playerRank;

                    svg.selectAll('rect')
                        .attr('fill', function (d, i) {
                            return i === playerBucket ? COLOR.highlight : COLOR.datum;
                        });
                });
            }
        };
    })

.directive('bfGameTimes', function () {
    return {
        template: '<svg></svg>',
        transclude: true,
        link: function (scope, element, attributes) {
            var times = scope.$eval(attributes.bfGameTimes);
            var data = [];
            var w = 24 * 7 * 6 - 5;
            var h = 150;
            var padding = 30;
            var svg = d3.select(element[0]).select('svg');
            var k;
            var xScale, yScale;
            var xAxis;
            var yMax;

            for (k in times) {
                if (times.hasOwnProperty(k)) {
                    data.push(times[k]);
                }
            }

            var accessor = function (d) {
                return parseFloat(d.count);
            };

            // The domain is the first week of 0AD
            var domain = [
                moment([1, 0, '+0300'].join(' '), 'E H ZZ'),
                moment([7, 23, '+0300'].join(' '), 'E H ZZ')
            ];

            xScale = d3.time.scale()
                .domain(domain)
                .range([0, w]);

            // xTickFormat = xScale.tickFormat('%H');

            xAxis = d3.svg.axis()
                .scale(xScale)
                .orient('bottom')
                .ticks(d3.time.day, 1)
                .tickFormat(d3.time.format('%A'));

            svg.attr('width', w);
            svg.attr('height', h + 2 * padding);
            svg.append('g')
                .attr('stroke', COLOR.axis)
                .attr('transform', 'translate(0,' + (h + 2) + ')')
                .call(xAxis)
                .call(bfAxis);

            scope.$watchCollection(attributes.bfGameTimes, function (newVal) {
                var k2;
                data = [];
                for (k2 in newVal) {
                    if (newVal.hasOwnProperty(k2)) {
                        data.push(newVal[k2]);
                    }
                }

                // yscale can only be made after the data is available
                yMax = d3.max(data, accessor);
                yScale = d3.scale.linear()
                    .domain([0, yMax])
                    .range([h, 0])
                    .clamp(true);

                svg.selectAll('rect')
                    .data(data)
                    .enter().append('svg:rect')
                    .attr('x', function (d) {
                        var dataMoment = moment([d.day, d.hour, '+0300'].join(' '), 'E H ZZ');
                        return Math.floor(xScale(dataMoment.toDate()));
                    })
                    .attr('y', h)
                    .attr('width', function () {
                        return Math.floor(w / 7 / 24);
                    })
                    .attr('height', 0)
                    .attr('fill', COLOR.datum)
                    .on('mouseover', function (d) {
                        var textBBox;
                        var detailRectPadding = 3;
                        var text = moment([d.day, d.hour, '+0300'].join(' '), 'E H ZZ').format('ddd h A');
                        var boxPos;

                        // create the detail box for this rect
                        svg.append('g')
                            .attr('class', 'detail')
                            .style('opacity', 0)
                            .append('rect')
                            .attr('fill', COLOR.background)
                            .attr('stroke', COLOR.outline)
                            .attr('height', 30)
                            .attr('width', 60)
                            .attr('y', 0.5 * -padding);

                        // show detail text
                        svg.select('g.detail')
                            .append('text')
                            .attr('class', 'detail')
                            .attr('fill', COLOR.text)
                            .text(text + ': ' + d.count + ' games');

                        textBBox = svg.select('text.detail')
                            .node().getBBox();

                        boxPos = {
                            x: this.getAttribute('x') - textBBox.width / 2,
                            y: yScale(d.count)
                        };

                        boxPos.x = Math.min(Math.max(boxPos.x, 0), w - textBBox.width);
                        boxPos.y = Math.min(Math.max(boxPos.y, textBBox.height), h);

                        // size detail box to text
                        svg.select('g.detail rect')
                            .attr('width', textBBox.width + detailRectPadding * 2)
                            .attr('height', textBBox.height + detailRectPadding * 2)
                            .attr('x', -detailRectPadding)
                            .attr('y', -textBBox.height - detailRectPadding);

                        // fade in detail box
                        svg.select('g.detail')
                            .attr('transform', 'translate(' + boxPos.x + ',' + boxPos.y + ')')
                            .interrupt()
                            .transition()
                            .duration(DURATION)
                            .delay(50)
                            .style('opacity', 1);

                        // fade bar to highlight color
                        d3.select(this)
                            .transition()
                            .ease('ease-out')
                            .duration(100)
                            .attr('fill', COLOR.highlight);
                    })
                    .on('mouseout', function () {

                        // remove old detail boxes
                        svg.select('g.detail')
                            .remove();

                        // fade bar back to normal color
                        d3.select(this)
                            .transition()
                            .duration(DURATION)
                            .attr('fill', COLOR.datum);
                    })
                    .transition()
                    .duration(DURATION)
                    .delay(function (d) {
                        return (+d.day * 24 + (+d.hour)) * 10;
                    })
                    .attr('y', function (d) {
                        return Math.floor(yScale(accessor(d)));
                    })
                    .attr('height', function (d) {
                        return h - Math.floor(yScale(accessor(d)));
                    });
            });
        }
    };
});