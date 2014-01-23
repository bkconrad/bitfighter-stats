var phonecatApp = angular.module('bfstats', ['ngGrid']);

var BADGE_MAP = {
  0: ["Developer", "developer.png"], 
  2: ["Twenty five flags", "twenty_five_flags.png"], 
  3: ["BBB Gold Medalist", "bbb_gold.png"], 
  4: ["BBB Silver Medalist", "bbb_silver.png"], 
  5: ["BBB Bronze Medalist", "bbb_bronze.png"], 
  6: ["BBB Participant", "bbb_participation.png"], 
  7: ["Level Design Contest Winner", "level_design_winner.png"], 
  8: ["Zone Controller", "zone_controller.png"]
};
 
phonecatApp.controller('StatsCtrl', function ($scope, $http) {

	$scope.loadingStyle = 'loading';

	$scope.options = $scope.options || {
		enableColumnResize: true,
		showFooter: true,
		showHeader: true,
		afterSelectionChange: function(row, event) {
			var params = {
				month: $scope.selectedPeriod.month + 1,
				year: $scope.selectedPeriod.year,
				player: row.entity.player_name,
				authed: row.entity.is_authenticated == '1' ? 'yes' : 'no'
			};

			$http.get('player.php', {params: params})
			.success(function(data) {
				var i;
				var achievement;

				console.log(data);

				// convert integer strings to actual integers
				for(i in data) {
					if(parseInt(data[i], 10).toString() === data[i]) {
						data[i] = parseInt(data[i], 10);
					}
				}

				data.game_count = data.win_count + data.lose_count + data.tie_count + data.dnf_count;
				data.finished_game_count = data.win_count + data.lose_count + data.tie_count;

				for(i in data.achievements) {
					achievement = data.achievements[i];
					achievement.hint = BADGE_MAP[achievement.achievement_id][0];
					achievement.image = BADGE_MAP[achievement.achievement_id][1];
				}
				$scope.player = data;
			});
		},
		pagingOptions: {
			pageSizes: [5, 10, 40], 
			pageSize: 40,
			currentPage: 1
		},	
		enablePaging: true,
		multiSelect: false,
		totalServerItems: 'total',
		columnDefs: [
			{field: 'player_name', displayName: 'Player Name', width: '100'},
			{field: 'kill_death_ratio', displayName: 'KDR'},
			{field: 'kill_count', displayName: 'Kills'},
			{field: 'death_count', displayName: 'Deaths'},
			{field: 'suicide_count', displayName: 'Suicides'},
			{field: 'game_count', displayName: 'Games'},
			{field: 'win_count', displayName: 'Wins'},
			{field: 'lose_count', displayName: 'Losses'},
			{field: 'tie_count', displayName: 'Ties'},
			{field: 'points', displayName: 'Points'},
			{field: 'flag_drops', displayName: 'Flags Dropped'},
			{field: 'flag_pickups', displayName: 'Flags Picked Up'},
			{field: 'flag_returns', displayName: 'Flags Returned'},
			{field: 'flag_scores', displayName: 'Flags Scored'},
			{field: 'teleport_uses', displayName: 'Times Teleported'},
			{field: 'asteroid_crashes', displayName: 'Asteroid Mishaps'},
			{field: 'switched_team_count', displayName: 'Team Switches'},
			{field: 'last_played', displayName: 'Last Played'}
		],
		data: 'stats'
	};

	function getData(page, per_page) {
		var i;
		var row;
		var params = {
			month: $scope.selectedPeriod.month + 1,
			year: $scope.selectedPeriod.year,
			page: page,
			per_page: per_page
		};

		$http.get('stats.php', { params: params}).success(function(data) {
			$scope.total = data.count;
			$scope.stats = data;

			for(i in data) {
				row = data[i];
				row.last_played = moment(row.last_played + " GMT+0100").fromNow();
			}

		}).
		error(function(data, status, headers, config) {
			console.log(status);
		});
	}

	$scope.periods = [];
	var i;
	var t;
	var data;
	for(i = 5; i >= 0; i--) {
		t = moment().subtract('month', i);
		data = {
			pretty: t.format('MMMM YYYY'),
			month: t.month(),
			year: t.year()
		};
		$scope.periods.push(data);
	}
	$scope.selectedPeriod = $scope.periods[5];

	$scope.$watch('selectedPeriod', function() {
		getData($scope.options.pagingOptions.currentPage, $scope.options.pagingOptions.pageSize);
	});

	$scope.$watchCollection('options.pagingOptions', function() {
		getData($scope.options.pagingOptions.currentPage, $scope.options.pagingOptions.pageSize);
	})

	getData($scope.options.pagingOptions.currentPage, $scope.options.pagingOptions.pageSize);
});

phonecatApp.controller('PlayerCtrl', function ($scope, $http) {
});

phonecatApp.filter('percentage', function() {
	return function(input) {
		var val = parseFloat(input) || 0;
		return (Math.round(val * 10000) / 100).toFixed(2) + '%';
	};
});