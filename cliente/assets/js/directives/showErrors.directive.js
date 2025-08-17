(function(){
    "use strict";

    angular.module('app').directive('showErrors', showErrors);

    showErrors.$inject = [];
    function showErrors()
    {
        var directive = {
            scope: {
                errors: '='
            },
            restrict: 'E',
            replace: true,
            templateUrl: 'partials/directives/showErrors/errors.html'
        };

        return directive;
    }
})();