'use strict';

/**
 * @ngdoc function
 * @name redqueenUiApp.controller:LogsCtrl
 * @description
 * # LogsCtrl
 * Controller of the redqueenUiApp
 */
angular.module('redqueenUiApp')
  .controller('LogsCtrl', [ '$scope', 'Log', function ($scope, LogResource) {
    $scope.logs = [];
    $scope.activeMenu = 'logs';

    LogResource.all().then(function(data) {
      $scope.logs = data;
    });
  }]);
