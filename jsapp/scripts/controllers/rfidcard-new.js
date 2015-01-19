'use strict';

/**
 * @ngdoc function
 * @name redqueenUiApp.controller:RfidcardNewCtrl
 * @description
 * # RfidcardNewCtrl
 * Controller of the redqueenUiApp
 */
angular.module('redqueenUiApp')
  .controller('RfidCardNewCtrl', [ '$scope', '$location', '$routeParams', 'RfidCard', 'Schedule', function($scope, $location, $routeParams, RfidCardResource, ScheduleResource) {
    $scope.rfidCard = new RfidCardResource();

    $scope.schedules = [];

    if (angular.isDefined($routeParams.code)) {
      $scope.rfidCard.code = $routeParams.code;
    }

    ScheduleResource.all().then(function(data) {
      $scope.schedules = data;
    });

    $scope.submit = function() {
      $scope.rfidCard.$save().then(function() {
        $location.path('/rfidcards');
      });
    };
  }]);
