'use strict';

/**
 * @ngdoc function
 * @name redqueenUiApp.controller:SchedulesCtrl
 * @description
 * # SchedulesCtrl
 * Controller of the redqueenUiApp
 */
angular.module('redqueenUiApp')
  .controller('SchedulesCtrl', [ '$scope', '$location', 'Schedule', function ($scope, $location, ScheduleResource) {
    $scope.schedules = [];
    $scope.activeMenu = 'schedules';

    ScheduleResource.all().then(function(data) {
      $scope.schedules = data;
    });

    $scope.edit = function SchedulesCtrlEdit(rfidCard) {
      $location.path('/schedules/' + rfidCard.id + '/edit');
    };

  }]);
