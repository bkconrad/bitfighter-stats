// Declare app level module which depends on filters, and services
angular.module('bfstats', [
    'ngRoute',
    'bfstats.filters',
    'bfstats.directives',
    'bfstats.controllers'
]).
config(['$routeProvider',
    function ($routeProvider) {
        $routeProvider.when('/records', {
            templateUrl: 'app/partials/records.html',
            controller: 'RecordsCtrl'
        });
        $routeProvider.when('/games', {
            templateUrl: 'app/partials/games.html',
            controller: 'GamesCtrl'
        });
        $routeProvider.when('/game_times', {
            templateUrl: 'app/partials/game_times.html',
            controller: 'GameTimesCtrl'
        });
        $routeProvider.otherwise({
            templateUrl: 'app/partials/stats.html',
            controller: 'StatsCtrl'
        });
        // $routeProvider.when('/view2', {templateUrl: 'partials/partial2.html', controller: 'MyCtrl2'});
        // $routeProvider.otherwise({redirectTo: '/view1'});
    }
]);