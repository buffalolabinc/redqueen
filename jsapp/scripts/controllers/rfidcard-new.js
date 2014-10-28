'use strict';

/**
 * @ngdoc function
 * @name redqueenUiApp.controller:RfidcardNewCtrl
 * @description
 * # RfidcardNewCtrl
 * Controller of the redqueenUiApp
 */
angular.module('redqueenUiApp')
  .controller('RfidCardNewCtrl', [ '$scope', '$location', '$routeParams', 'RfidCard', function($scope, $location, $routeParams, RfidCardResource) {
    $scope.rfidCard = new RfidCardResource();

    if (angular.isDefined($routeParams.code)) {
      $scope.rfidCard.code = $routeParams.code;
    }

    $scope.submit = function() {
      $scope.rfidCard.$save().then(function() {
        $location.path('/rfidcards');
      });
    };
  }]);
