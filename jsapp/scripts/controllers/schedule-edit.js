'use strict';

/**
 * @ngdoc function
 * @name redqueenUiApp.controller:ScheduleeditCtrl
 * @description
 * # ScheduleeditCtrl
 * Controller of the redqueenUiApp
 */
angular.module('redqueenUiApp')
  .controller('ScheduleEditCtrl', [ '$scope', '$location', '$routeParams', 'Schedule', function ($scope, $location, $routeParams, ScheduleResource) {
    $scope.schedule = null;

    ScheduleResource.find($routeParams.id).then(function(data) {
      $scope.schedule = data;
    });

    $scope.submit = function() {
      $scope.schedule.$save().then(function() {
        $location.path('/schedules');
      });
    };
  }]);
