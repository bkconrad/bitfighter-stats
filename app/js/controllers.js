var BADGE_MAP = {
    0: ["Developer", "developer.png"],
    2: ["Twenty five flags", "twenty_five_flags.png"],
    3: ["BBB Gold Medalist", "bbb_gold.png"],
    4: ["BBB Silver Medalist", "bbb_silver.png"],
    5: ["BBB Bronze Medalist", "bbb_bronze.png"],
    6: ["BBB Participant", "bbb_participation.png"],
    7: ["Level Design Contest Winner", "level_design_winner.png"],
    8: ["Zone Controller", "zone_controller.png"],
    9: ["Raging Rabid Rabbit", "raging_rabid_rabbit.png"],
    10: ["Hat Trick", "hat_trick.png"],
    11: ["Last-Second Win", "last_second_win.png"]
};

function ERROR_HANDLER(data, status) {
    console.log(data, status);
}

var PROPERTY_MAP = {
    frags: [{
        name: 'kill_count'
    }, {
        name: 'death_count'
    }, {
        name: 'suicide_count'
    }, {
        name: 'kill_death_ratio'
    }, {
        name: 'spread'
    }, {
        name: 'spread_per_game'
    }],
    flags: [{
        name: 'flag_pickups'
    }, {
        name: 'flag_drops'
    }, {
        name: 'flag_returns'
    }, {
        name: 'flag_scores'
    }, {
        name: 'flag_scores_per_game'
    }],
    games: [{
        name: 'game_count'
    }, {
        name: 'win_count'
    }, {
        name: 'lose_count'
    }, {
        name: 'tie_count'
    }, {
        name: 'points'
    }, {
        name: 'points_per_game'
    }, ],
    misc: [{
        name: 'asteroid_kills'
    }, {
        name: 'asteroid_crashes'
    }, {
        name: 'ff_kills'
    }, {
        name: 'ffs_engineered'
    }, {
        name: 'turret_kills'
    }, {
        name: 'turrets_engineered'
    }, {
        name: 'teleports_engineered'
    }, {
        name: 'distance_traveled'
    }, {
        name: 'teleport_uses'
    }]
};

angular.module('bfstats.controllers', ['ngGrid'])
    .controller('StatsCtrl', function ($scope, $http) {

        function getAllStats() {
            var row;
            var params = {
                month: $scope.selectedPeriod.month + 1,
                year: $scope.selectedPeriod.year
            };

            $http.get('stats.php', {
                cache: true,
                params: params
            })
                .success(function (data) {
                    var i;
                    var j;

                    // convert integer strings to actual integers
                    for (i = 0; i < data.length; i++) {
                        for (j in data[i]) {
                            if (data[i].hasOwnProperty(j)) {
                                if ((+data[i][j]).toString() === data[i][j]) {
                                    data[i][j] = +data[i][j];
                                }
                            }
                        }

                        row = data[i];
                        row.game_count = row.win_count + row.lose_count + row.tie_count;
                        row.finished_game_count = row.win_count + row.lose_count + row.tie_count;
                        row.flag_scores_per_game = row.flag_scores / row.game_count;
                        row.spread = row.kill_count - row.death_count;
                        row.spread_per_game = (row.kill_count - row.death_count) / row.game_count;
                        row.points_per_game = row.points / row.game_count;
                    }

                    $scope.stats = data;
                })
                .error(ERROR_HANDLER);
        }


        function getPlayerStats() {
            var params;

            if(!$scope.selectedPlayer) {
                return;
            }

            params = {
                month: $scope.selectedPeriod.month + 1,
                year: $scope.selectedPeriod.year,
                player: $scope.selectedPlayer.player_name,
                authed: $scope.selectedPlayer.is_authenticated === 1 ? 'yes' : 'no'
            };

            $http.get('player.php', {
                cache: true,
                params: params
            })
                .success(function (data) {
                    var i;
                    var achievement;

                    // convert integer strings to actual integers
                    for (i in data) {
                        if (data.hasOwnProperty(i)) {
                            if ((+data[i]).toString() === data[i]) {
                                data[i] = +data[i];
                            }
                        }
                    }

                    // Clean up the data once we receive it
                    data.game_count = data.win_count + data.lose_count + data.tie_count + data.dnf_count;
                    data.finished_game_count = data.win_count + data.lose_count + data.tie_count;
                    data.flag_scores_per_game = data.flag_scores / data.game_count;
                    data.spread = data.kill_count - data.death_count;
                    data.spread_per_game = (data.kill_count - data.death_count) / data.game_count;
                    data.points_per_game = data.points / data.game_count;

                    for (i in data.achievements) {
                        if (data.achievements.hasOwnProperty(i)) {
                            achievement = data.achievements[i];
                            achievement.hint = BADGE_MAP[achievement.achievement_id][0];
                            achievement.image = BADGE_MAP[achievement.achievement_id][1];
                        }
                    }

                    $scope.player = data;
                })
                .error(ERROR_HANDLER);
        }

        $scope.propertyMap = PROPERTY_MAP;

        $scope.options = {
            enableColumnResize: true,
            showColumnMenu: true,
            showFilter: true,
            showHeader: true,
            afterSelectionChange: function (row) {
                $scope.selectedPlayer = row.entity;
            },
            multiSelect: false,
            columnDefs: [{
                cellTemplate: '<span class="{{row.entity.is_authenticated ? \'authed\' : \'\'}}">{{row.entity[col.field]}}</span>',
                cellClass: 'ngCellText',
                field: 'player_name',
                visible: true,
                displayName: 'Player Name',
                width: '100'
            }, {
                field: 'kill_death_ratio',
                visible: true,
                displayName: 'KDR'
            }, {
                field: 'kill_count',
                visible: true,
                displayName: 'Kills'
            }, {
                field: 'death_count',
                visible: true,
                displayName: 'Deaths'
            }, {
                field: 'suicide_count',
                visible: false,
                displayName: 'Suicides'
            }, {
                field: 'game_count',
                visible: true,
                displayName: 'Games'
            }, {
                field: 'win_count',
                visible: true,
                displayName: 'Wins'
            }, {
                field: 'lose_count',
                visible: false,
                displayName: 'Losses'
            }, {
                field: 'tie_count',
                visible: false,
                displayName: 'Ties'
            }, {
                field: 'points',
                visible: false,
                displayName: 'Points'
            }, {
                field: 'flag_drops',
                visible: false,
                displayName: 'Flags Dropped'
            }, {
                field: 'flag_pickups',
                visible: false,
                displayName: 'Flags Picked Up'
            }, {
                field: 'flag_returns',
                visible: false,
                displayName: 'Flags Returned'
            }, {
                field: 'flag_scores',
                visible: false,
                displayName: 'Flags Scored'
            }, {
                field: 'teleport_uses',
                visible: false,
                displayName: 'Times Teleported'
            }, {
                field: 'asteroid_crashes',
                visible: false,
                displayName: 'Asteroid Mishaps'
            }, {
                field: 'switched_team_count',
                visible: false,
                displayName: 'Team Switches'
            }, {
                field: 'last_played',
                visible: true,
                cellTemplate: '<span>{{row.entity[col.field] | fromNow }}</span>',
                cellClass: 'ngCellText',
                displayName: 'Last Played'
            }],
            data: 'stats'
        };

        $scope.periods = [];
        var i;
        var t;
        var data;
        for (i = 5; i >= 0; i--) {
            t = moment().subtract('month', i);
            data = {
                pretty: t.format('MMMM YYYY'),
                month: t.month(),
                year: t.year()
            };
            $scope.periods.push(data);
        }
        $scope.selectedPeriod = $scope.periods[5];

        $scope.$watch('selectedPeriod', getAllStats);
        $scope.$watch('selectedPeriod', getPlayerStats);
        $scope.$watch('selectedPlayer', getPlayerStats);

        getAllStats();
    })

.controller('GamesCtrl', function ($scope, $http) {
    $scope.playersPerGameOptions = {
        header: 'Players Per Game',
        data: 'games',
        x: 'day_date',
        y: 'players_per_game'
    };

    $scope.uniquePlayersOptions = {
        header: 'Unique Players',
        data: 'games',
        x: 'day_date',
        y: 'unique_player_count'
    };

    $http.get('players_per_game.php', { cache: true })
        .success(function (data) {
            $scope.games = data;
        })
        .error(ERROR_HANDLER);

    $http.get('game_times.php', { cache: true })
        .success(function (data) {
            $scope.gameTimes = data;
        })
        .error(ERROR_HANDLER);
})

.controller('RecordsCtrl', function ($scope, $http) {
    $scope.propertyMap = PROPERTY_MAP;
    $http.get('records.php', { cache: true })
        .success(function (data) {
            $scope.records = data;
        })
        .error(ERROR_HANDLER);
})
