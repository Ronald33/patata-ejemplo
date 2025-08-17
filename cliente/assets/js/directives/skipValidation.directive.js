(function(){
    "use strict";

    angular.module('app').directive('skipValidation', skipValidation);

    skipValidation.$inject = [];
    function skipValidation()
    {
        var directive = {
            restrict: 'A', 
            require: '?form', 
            link: link
        };

        return directive;

        function link(scope, element, iAttrs, formController)
        {
            if(! formController) { return; }
        
            // Remove this form from parent controller
            var parentFormController = element.parent().controller('form');
            parentFormController.$removeControl(formController);

            // Replace form controller with a "null-controller"
            var nullFormCtrl = {
                $addControl: angular.noop,
                $removeControl: angular.noop,
                $setValidity: angular.noop,
                $setDirty: angular.noop,
                $setPristine: angular.noop
            };
        
            angular.extend(formController, nullFormCtrl);
        }
    }
})();