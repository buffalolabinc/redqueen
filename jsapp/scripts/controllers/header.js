'use strict';

/**
 * @ngdoc function
 * @name redqueenUiApp.controller:HeaderCtrl
 * @description
 * # HeaderCtrl
 * Controller of the redqueenUiApp
 */
angular.module('redqueenUiApp')
  .controller('HeaderCtrl', [ '$scope', '$location', function ($scope, $location) {
    $scope.isActive = function(path) {
      return path === $location.path();
    };
  }]);
