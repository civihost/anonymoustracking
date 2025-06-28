(function (angular, $, _) {
  angular.module('crmAnonymoustracking', [])
    .directive('anonymousTrackingInit', function () {
      return {
        restrict: 'A',
        link: function (scope) {
          var fieldId = CRM.crmAnonymoustracking.anonymous_tracking_field_id;
          scope.anonymous_tracking_field = fieldId;
          const unwatch = scope.$watch('mailing', function (mailing) {
            if (mailing) {
              if (typeof mailing[fieldId] === 'undefined' || mailing[fieldId] === null) {
                mailing[fieldId] = String(CRM.crmAnonymoustracking.anonymous_tracking_default);
              }
              unwatch();
            }
          });
        }
      };
    });
})(angular, CRM.$, CRM._);
