'use strict';

/**
 * @ngdoc overview
 * @name redqueenUiApp
 * @description
 * # redqueenUiApp
 *
 * Main module of the application.
 */
angular
  .module('redqueenUiApp', [
    'ngAnimate',
    'ngCookies',
    'ngResource',
    'ngRoute',
    'ngSanitize',
    'ngTouch'
  ])
  .config(function ($routeProvider) {
    $routeProvider
      .when('/rfidcards/new', {
        templateUrl: 'views/rfidcards/form.html',
        controller: 'RfidCardNewCtrl'
      })
      .when('/rfidcards/:id/edit', {
        templateUrl: 'views/rfidcards/form.html',
        controller: 'RfidCardEditCtrl'
      })
      .when('/rfidcards', {
        templateUrl: 'views/rfidcards.html',
        controller: 'RfidCardsCtrl'
      })
      .when('/logs', {
        templateUrl: 'views/logs.html',
        controller: 'LogsCtrl'
      })
      .when('/schedules', {
        templateUrl: 'views/schedules.html',
        controller: 'SchedulesCtrl'
      })
      .when('/schedules/new', {
        templateUrl: 'views/schedules/form.html',
        controller: 'ScheduleNewCtrl'
      })
      .when('/schedules/:id/edit', {
        templateUrl: 'views/schedules/form.html',
        controller: 'ScheduleEditCtrl'
      })
      .otherwise({
        redirectTo: '/'
      });
  });
