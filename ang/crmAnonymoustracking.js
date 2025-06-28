(function (angular, $, _) {
  angular.module('crmAnonymoustracking', [])
    .directive('anonymousTrackingInit', function () {
      return {
        restrict: 'A',
        link: function (scope) {
          scope.anonymous_tracking_field = CRM.crmAnonymoustracking.anonymous_tracking_field_id;
        }
      };
    });
})(angular, CRM.$, CRM._);
