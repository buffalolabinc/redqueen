'use strict';

/**
 * @ngdoc function
 * @name redqueenUiApp.controller:RfidcardeditCtrl
 * @description
 * # RfidcardeditCtrl
 * Controller of the redqueenUiApp
 */
angular.module('redqueenUiApp')
  .controller('RfidCardEditCtrl', [ '$scope', '$location', '$routeParams', 'RfidCard', 'Schedule', function ($scope, $location, $routeParams, RfidCardResource, ScheduleResource) {
    $scope.rfidCard = null;

    $scope.schedules = [];

    RfidCardResource.find($routeParams.id).then(function(data) {
      $scope.rfidCard = data;
    });

    ScheduleResource.all().then(function(data) {
      $scope.schedules = data;
    });

    $scope.submit = function() {
      $scope.rfidCard.$save().then(function() {
        $location.path('/rfidcards');
      });
    };
  }]);
