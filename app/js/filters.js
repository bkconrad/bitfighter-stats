angular.module('bfstats.filters', [])
	.filter('percentage', function() {
		return function(input) {
			var val = parseFloat(input) || 0;
			return (Math.round(val * 10000) / 100).toFixed(2) + '%';
		};
	});
