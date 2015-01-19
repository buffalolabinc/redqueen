'use strict';

/**
 * @ngdoc function
 * @name redqueenUiApp.controller:RfidcardsCtrl
 * @description
 * # RfidcardsCtrl
 * Controller of the redqueenUiApp
 */
angular.module('redqueenUiApp')
  .controller('RfidCardsCtrl', [ '$scope', '$location', 'RfidCard', function ($scope, $location, RfidCardResource) {
    $scope.rfidCards = [];
    $scope.activeMenu = 'cards';

    RfidCardResource.all().then(function(data) {
      $scope.rfidCards = data;
    });

    $scope.edit = function RfidCardsCtrlEdit(rfidCard) {
      $location.path('/rfidcards/' + rfidCard.id + '/edit');
    };

  }]);
