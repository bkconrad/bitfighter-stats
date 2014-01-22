var phonecatApp = angular.module('bfstats', ['ngGrid']);
 
phonecatApp.controller('StatsCtrl', function ($scope, $http) {

	$scope.loadingStyle = 'loading';

	$http.get('stats.php').success(function(data) {
		$scope.stats = data;
	});

	$scope.options = {
		enablePaging: true,
		enableColumnResize: true,
		showFooter: true,
		showHeader: true,
		pagingOptions: {
			// pageSizes: list of available page sizes.
			pageSizes: [5, 10, 40], 
			//pageSize: currently selected page size. 
			pageSize: 5,
			//totalServerItems: Total items are on the server. 
			totalServerItems: 99,
			//currentPage: the uhm... current page.
			currentPage: 1
		},	
		data: 'stats'
	};
});