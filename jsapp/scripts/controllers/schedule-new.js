'use strict';

/**
 * @ngdoc function
 * @name redqueenUiApp.controller:ScheduleNewCtrl
 * @description
 * # ScheduleNewCtrl
 * Controller of the redqueenUiApp
 */
angular.module('redqueenUiApp')
  .controller('ScheduleNewCtrl', [ '$scope', '$location', '$routeParams', 'Schedule', function($scope, $location, $routeParams, ScheduleResource) {
    $scope.schedule = new ScheduleResource();

    $scope.submit = function() {
      $scope.schedule.$save().then(function() {
        $location.path('/schedules');
      });
    };
  }]);
