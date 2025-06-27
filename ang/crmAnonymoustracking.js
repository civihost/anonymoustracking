/*
(function(angular, $, _) {
  angular.module('crmAnonymoustracking', []);
})(angular, CRM.$, CRM._);

  angular.module('crmAnonymoustracking', ['crmUi'])

  .run(function($rootScope, settings) {
    // Aggiunge il modulo ai contesti CiviCRM già caricati
    $rootScope.$on('mailing:edit:load', function(evt, scope) {
      const fieldName = settings.anonymous_tracking_field_id;

      // Inizializza il campo se non è ancora presente
      if (scope.mailing && typeof scope.mailing[fieldName] === 'undefined') {
        scope.mailing[fieldName] = '0';
      }

      // Se vuoi log/debug
      console.log('Anonymous tracking field bound as', fieldName);
    });
  });
  */
(function (angular, $, _) {
  console.log("crmAnonymoustracking.js loaded"); // <-- debug log
  angular
    .module("crmAnonymoustracking", ['crmUi', 'crmUtil', 'crmResource'])
/*
    .run(function ($rootScope, config) {
      var fieldName = config.settings.anonymous_tracking_field_id;

      // Guarda quando viene impostato `mailing` in qualunque controller
      $rootScope.$watch("mailing", function (mailing) {
        if (!mailing) return;
        console.log("anonymousTrackingInit watch, field:", fieldName); // <-- debug log

        // Inizializza il campo solo se non presente
        if (typeof mailing[fieldName] === "undefined") {
          if (mailing.values && mailing.values[fieldName]) {
            mailing[fieldName] = mailing.values[fieldName];
          } else {
            mailing[fieldName] = "0"; // valore di default (non tracciato)
          }
        }
      });
    })*/
    .directive("anonymousTrackingInit", function () {
      return {
        restrict: "A",
        link: function (scope, element, attrs) {
          var fieldName = CRM.crmAnonymoustracking.anonymous_tracking_field_id;
          console.log("anonymousTrackingInit active, field:", fieldName); // <-- debug log

          scope.$watch("mailing", function (mailing) {
            if (mailing && typeof mailing[fieldName] === "undefined") {
              mailing[fieldName] = "0"; // default unchecked
            }
          console.log("mailing gield name, field:",mailing[fieldName]); // <-- debug log

          });
        },
      };
    });
})(angular, CRM.$, CRM._);
