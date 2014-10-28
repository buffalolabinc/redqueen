'use strict';

/**
 * @ngdoc service
 * @name redqueenUiApp.log
 * @description
 * # log
 * Service in the redqueenUiApp.
 */
angular.module('redqueenUiApp')
  .service('Log', [ '$q', '$timeout', '$http', 'underscore', function($q, $timeout, $http, _) {

    function Log(data) {
      angular.extend(this, data);
    }

    Log.all = function LogResourceAll() {
      var deferred = $q.defer();

      $http.get('/api/logs').then(function(data) {
        var logs = _.map(data.data, function(log) {
          return new Log(log);
        });

        deferred.resolve(logs);
      }, function() {
        deferred.reject();
      });

      return deferred.promise;
    };

    return Log;
  }]);
