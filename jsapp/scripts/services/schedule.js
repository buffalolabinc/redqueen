'use strict';

/**
 * @ngdoc service
 * @name redqueenUiApp.Schedule
 * @description
 * # Schedule
 * Service in the redqueenUiApp.
 */
angular.module('redqueenUiApp')
  .service('Schedule', [ '$q', '$timeout', '$http', 'underscore', function($q, $timeout, $http, _) {

    function Schedule(data) {
      angular.extend(this, data);

      this.$isNew = (typeof(this.id) === 'undefined' || !this.id);
    }

    Schedule.all = function ScheduleResourceAll() {
      var deferred = $q.defer();

      $http.get('/api/schedules').then(function(data) {
        var schedules = _.map(data.data, function(card) {
          return new Schedule(card);
        });

        deferred.resolve(schedules);
      }, function() {
        deferred.reject();
      });

      return deferred.promise;
    };

    Schedule.find = function ScheduleResourceFind(id) {
      var deferred = $q.defer();

      $http.get('/api/schedules/' + id).then(function(data) {
        var schedule = new Schedule(data.data);

        deferred.resolve(schedule);
      }, function() {
        deferred.reject();
      });

      return deferred.promise;
    };

    Schedule.prototype.$save = function ScheduleSave() {
      var deferred = $q.defer();
      var self = this;
      var url = null;
      var method = null;

      var fixTime = function(time) {
        return time.length < 8 ? time + ':00' : time;
      };

      var data = {
        name: self.name,
        mon: self.mon === true,
        tue: self.tue === true,
        wed: self.wed === true,
        thu: self.thu === true,
        fri: self.fri === true,
        sat: self.sat === true,
        sun: self.sun === true,
        startTime: fixTime(self.startTime),
        endTime: fixTime(self.endTime)
      };

      if (self.$isNew) {
        url = '/api/schedules';
        method = 'POST';
      } else {
        url = '/api/schedules/' + self.id;
        method = 'PUT';
      }

      $http({
        url: url,
        method:  method,
        data: data
      }).then(function(data) {
        var schedule = new Schedule(data.data);

        deferred.resolve(schedule);
      }, function() {
        deferred.reject();
      });

      return deferred.promise;
    };

    return Schedule;
  }]);
