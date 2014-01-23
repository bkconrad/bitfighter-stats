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
			$http.get('player.php', {params: {player: row.entity.player_name, authed: row.entity.is_authenticated == '1' ? 'yes' : 'no'}})
			.success(function(data) {
				var i;
				var achievement;

				console.log(data);

				data.game_count = parseInt(data.win_count, 10) + parseInt(data.lose_count, 10) + parseInt(data.tie_count, 10) + parseInt(data.dnf_count, 10);
				data.finished_game_count = parseInt(data.win_count, 10) + parseInt(data.lose_count, 10) + parseInt(data.tie_count, 10);
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
		$http.get('stats.php', { params: { page: page, per_page: per_page }}).success(function(data) {
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

	$scope.$watchCollection('options.pagingOptions', function() {
		getData($scope.options.pagingOptions.currentPage, $scope.options.pagingOptions.pageSize);
	})

	getData($scope.options.pagingOptions.currentPage, $scope.options.pagingOptions.pageSize);
});

phonecatApp.controller('PlayerCtrl', function ($scope, $http) {
});