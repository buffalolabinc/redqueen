'use strict';

/**
 * @ngdoc function
 * @name redqueenUiApp.controller:RfidcardeditCtrl
 * @description
 * # RfidcardeditCtrl
 * Controller of the redqueenUiApp
 */
angular.module('redqueenUiApp')
  .controller('RfidCardEditCtrl', [ '$scope', '$location', '$routeParams', 'RfidCard', function ($scope, $location, $routeParams, RfidCardResource) {
    $scope.rfidCard = null;

    RfidCardResource.find($routeParams.id).then(function(data) {
      $scope.rfidCard = data;
    });

    $scope.submit = function() {
      $scope.rfidCard.$save().then(function() {
        $location.path('/rfidcards');
      });
    };
  }]);
