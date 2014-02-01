// Declare app level module which depends on filters, and services
angular.module('bfstats', [
    'ngRoute',
    // 'ngAnimate',
    'chieffancypants.loadingBar',
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
        $routeProvider.otherwise({
            templateUrl: 'app/partials/stats.html',
            controller: 'StatsCtrl'
        });
    }
]);