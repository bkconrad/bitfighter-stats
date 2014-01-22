var phonecatApp = angular.module('bfstats', ['ngGrid']);
 
phonecatApp.controller('StatsCtrl', function ($scope, $http) {

	$scope.loadingStyle = 'loading';

	$scope.options = $scope.options || {
		enableColumnResize: true,
		showFooter: true,
		showHeader: true,
		pagingOptions: {
			// pageSizes: list of available page sizes.
			pageSizes: [5, 10, 40], 
			//pageSize: currently selected page size. 
			pageSize: 40,
			//currentPage: the uhm... current page.
			currentPage: 1
		},	
		enablePaging: true,
		totalServerItems: 'total',
		data: 'stats'
	};

	function getData(page, per_page) {
		$http.get('stats.php', { params: { page: page, per_page: per_page }}).success(function(data) {
			$scope.total = data.count;
			$scope.stats = data;
		}).
		error(function(data, status, headers, config) {
			console.log(status);
		});
	}

	$scope.$watchCollection('options.pagingOptions', function() {
		getData($scope.options.pagingOptions.currentPage, $scope.options.pagingOptions.pageSize);
	})

	getData($scope.options.pagingOptions.currentPage, $scope.options.pagingOptions.pageSize);
});