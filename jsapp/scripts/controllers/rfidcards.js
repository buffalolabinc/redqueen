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

    //$scope.remove = function RfidCardsCtrlRemove(rfidCard) {
    //  rfidCard.$remove().then(function() {
    //    $scope.rfidCards = _.reject($scope.rfidCards, function(c) {
    //      return c.id == rfidCard.id;
    //    });
    //  });
    //};

  }]);
