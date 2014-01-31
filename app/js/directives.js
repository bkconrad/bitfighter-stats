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
            var options = scope.$eval(attributes['bfGraph']);

            scope.$watch(options.data, function (newVal, oldVal, scope) {
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
                var COLORS = ['#80100F', '#4c1348', '#142030', '#476f26'];
                var padding = 30;

                // bail if the value is falsey
                if (!newVal) {
                    return;
                }

                // convert collections to arrays
                for (k in newVal) {
                    if (newVal.hasOwnProperty(k)) {
                        data.push(newVal[k]);

                        // add an index field to the data
                        data[data.length - 1].x = data.length - 1;
                    }
                }

                var xMax = d3.max(data, function (d) {
                    return d[xprop];
                });
                var xScale = d3.scale.linear()
                    .domain([0, xMax])
                    .range([padding, w])
                    .nice();

                var xAxis = d3.svg.axis()
                    .scale(xScale)
                    .orient('bottom');

                var yMax = d3.max(data, function (d) {
                    return parseFloat(d[yprop]);
                });
                console.log(yMax);
                var yScale = d3.scale.linear()
                    .domain([0, yMax * 1.1])
                    .range([h, 0]);

                var yAxis = d3.svg.axis()
                    .scale(yScale)
                    .orient('left');

                d3.select(element[0]).select('svg')
                    .append('g')
                    .attr('stroke', '#EEE')
                    .attr('transform', 'translate(0,' + (h) + ')')
                    .call(xAxis);

                d3.select(element[0]).select('svg')
                    .append('g')
                    .attr('stroke', '#EEE')
                    .attr('transform', 'translate(' + padding + ',0)')
                    .call(yAxis);

                var line = d3.svg.line()
                    .x(function (d) {
                        return xScale(d[xprop]);
                    })
                    .y(function (d) {
                        return yScale(d[yprop]);
                    });

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
                    .style('background', '#000')
                    .style('margin', 'auto')
                    .style('display', 'block')

                // Add the data line
                .append('svg:path')
                    .data([data])
                    .attr('d', line)
                    .attr('fill', 'rgba(0,0,0,0)')
                    .attr('stroke', '#888')
                    .attr('stroke-width', 3);

                // Set the mouse event handlers
                d3.select(svg)
                    .on('mousemove', function () {

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
                            .text(datum[yprop]);

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
                            .text(datum[yprop]);
                    })
                    .on('mouseout', function () {
                        // Hide the detail pane
                        d3.select(svg).selectAll('.detail')
                            .transition()
                            .duration(300)
                            .style('opacity', 0);
                    });

                // Create the detail box
                d3.select(svg)
                    .append('svg:rect')
                    .attr('class', 'detail')
                    .attr('x', padding)
                    .attr('y', padding)
                    .attr('fill', '#222')
                    .attr('stroke', '#444');

                // Create the detail text
                d3.select(svg)
                    .append('svg:text')
                    .attr('class', 'detail')
                    .attr('x', padding)
                    .attr('y', padding)
                    .attr('stroke', '#FFF')
                    .style('vertical-align', 'middle');

                console.log(data);

            });
        }
    };
})
    .directive('bfRank', function () {
        return {
            template: '<svg></svg>',
            transclude: true,
            link: function (scope, element, attributes) {
                var prop = scope.$eval(attributes['bfRank']);
                var stats = scope.$eval('stats');
                var w = 100;
                var h = 15;
                var buckets = 30;
                var svg = d3.select(element[0]).select('svg');
                var data = [];
                var k;
                var xScale, yScale, xAxis, yAxis, hist, histData, binMax;
                var bisector;
                var leftValue, leftIndex;
                var rightValue, rightIndex;
                var values = [];
                var mean;
                var stdDev;
                var bucketWidth;
                var playerBucket;
                var playerData;
                var playerRank;
                var outlierFactor = .15;
                var barBaseSize = 3;

                for (k in stats) {
                    data.push(stats[k]);
                }

                var accessor = function (d) {
                    return parseFloat(d[prop]);
                };

                yExtent = d3.extent(data, accessor);


                xScale = d3.scale.linear()
                    .domain([0, buckets])
                    .range([0, w])
                    .nice();

                xAxis = d3.svg.axis()
                    .scale(xScale)
                    .orient('bottom');

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

                yScale = d3.scale.log()
                    .domain([0.1, binMax])
                    .range([h - barBaseSize, 0])
                    .clamp(true);

                yAxis = d3.svg.axis()
                    .scale(yScale)
                    .orient('left');

                svg.attr('width', w);
                svg.attr('height', h);

                bucketWidth = Math.round(w / buckets);
                svg.selectAll('rect')
                    .data(histData)
                    .enter().append('svg:rect')
                    .attr('x', function (d, i) {
                        return Math.floor(bucketWidth * i);
                    })
                    .attr('y', function (d, i) {
                        return Math.floor(yScale(d.y));
                    })
                    .attr('width', function (d, i) {
                        return Math.floor(bucketWidth - 2);
                    })
                    .attr('height', function (d, i) {
                        return Math.ceil(h - Math.floor(yScale(d.y))) + barBaseSize;
                    });

                scope.$watchCollection('player', function (newVal) {
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
                            return i == playerBucket ? '#CCC' : '#444';
                        });
                })
            }
        };
    })

.directive('bfGameTimes', function () {
    return {
        template: '<svg></svg>',
        transclude: true,
        link: function (scope, element, attributes) {
            var times = scope.$eval(attributes['bfGameTimes']);
            var data = [];
            var w = 24 * 7 * 6 - 5;
            var h = 150;
            var padding = 30;
            var svg = d3.select(element[0]).select('svg');
            var k;
            var xScale, yScale, xAxis, yAxis;
            var yMax;
            var dayWidth = w / 7;
            var hourWidth = dayWidth / 24;
            var xTickFormat;

            for (k in times) {
                data.push(times[k]);
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
                .attr('stroke', '#FFF')
                .attr('transform', 'translate(0,' + (h + 2) + ')')
            //.style('font-size', '7.5px')
            .style('font-weight', 'normal')
                .call(xAxis);

            scope.$watchCollection(attributes['bfGameTimes'], function (newVal) {
                var data = [];
                for (k in newVal) {
                    data.push(newVal[k]);
                }

                // yscale can only be made after the data is available
                yMax = d3.max(data, accessor);
                yScale = d3.scale.linear()
                    .domain([0, yMax])
                    .range([h, 0])
                    .clamp(true);

                yAxis = d3.svg.axis()
                    .scale(yScale)
                    .orient('left');

                svg.selectAll('rect')
                    .data(data)
                    .enter().append('svg:rect')
                    .attr('x', function (d, i) {
                        var dataMoment = moment([d.day, d.hour, '+0300'].join(' '), 'E H ZZ');
                        console.log(dataMoment.toString());
                        return Math.floor(xScale(dataMoment.toDate()));
                    })
                    .attr('y', h)
                    .attr('width', function (d, i) {
                        return Math.floor(w / 7 / 24);
                    })
                    .attr('height', 0)
                    .attr('fill', '#FFF')
                    .on('mouseover', function (d) {
                        var mousePos = d3.mouse(svg.node());
                        var textBBox;
                        var detailRectPadding = 3;
                        var text = moment([d.day, d.hour, '+0300'].join(' '), 'E H ZZ').format('ddd h A');
                        var boxPos;

                        // create the detail box for this rect
                        svg.append('g')
                            .attr('class', 'detail')
                            .style('opacity', 0)
                            .append('rect')
                            .attr('fill', '#222')
                            .attr('stroke', '#111')
                            .attr('height', 30)
                            .attr('width', 60)
                            .attr('y', .5 * -padding);

                        // show detail text
                        svg.select('g.detail')
                            .append('text')
                            .attr('class', 'detail')
                            .attr('fill', '#888')
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
                            .duration(300)
                            .delay(50)
                            .style('opacity', 1);

                        // fade bar to highlight color
                        d3.select(this)
                            .transition()
                            .duration(100)
                            .attr('fill', '#AAA');
                    })
                    .on('mouseout', function (d) {

                        // remove old detail boxes
                        svg.select('g.detail')
                            .remove();

                        // fade bar back to normal color
                        d3.select(this)
                            .transition()
                            .duration(100)
                            .attr('fill', '#FFF');
                    })
                    .transition()
                    .duration(500)
                    .delay(function (d, i) {
                        return (+d.day * 24 + (+d.hour)) * 20;
                    })
                    .attr('y', function (d, i) {
                        return Math.floor(yScale(accessor(d)));
                    })
                    .attr('height', function (d, i) {
                        return h - Math.floor(yScale(accessor(d)));
                    });

            })
        }
    };
});