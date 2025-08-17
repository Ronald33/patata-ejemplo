(function(){
    "use strict";

    var app = angular.module('app');
    
    app.directive('isWord', isWord);
    app.directive('isWords', isWords);
    app.directive('isAlphanumeric', isAlphanumeric);
    app.directive('isAlphanumericAndSpaces', isAlphanumericAndSpaces);
    app.directive('isDni', isDni);
    app.directive('isRuc', isRuc);
    app.directive('isEqualTo', isEqualTo);
    app.directive('stDateSearch', stDateSearch);
    app.directive('goBack', goBack);

    isWord.$inject = [];
    function isWord()
    {
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function (scope, element, attributes, control) {
                control.$validators.isWord = function (modelValue, viewValue) {
                    if(control.$isEmpty(viewValue) && !attributes.required) { return true; }
                    return /^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$/.test(viewValue);
                };
            }
        };
    }

    isWords.$inject = [];
    function isWords()
    {
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function (scope, element, attributes, control) {
                control.$validators.isWords = function (modelValue, viewValue) {
                    if(control.$isEmpty(viewValue) && !attributes.required) { return true; }
                    return /^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/.test(viewValue);
                };
            }
        };
    }

    isAlphanumeric.$inject = [];
    function isAlphanumeric()
    {
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function (scope, element, attributes, control) {
                control.$validators.isAlphanumeric = function (modelValue, viewValue) {
                    if(control.$isEmpty(viewValue) && !attributes.required) { return true; }
                    return /^[a-zA-Z0-9áéíóúñÑ]+$/.test(viewValue);
                };
            }
        };
    }

    isAlphanumericAndSpaces.$inject = [];
    function isAlphanumericAndSpaces()
    {
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function (scope, element, attributes, control) {
                control.$validators.isAlphanumericAndSpaces = function (modelValue, viewValue) {
                    if(control.$isEmpty(viewValue) && !attributes.required) { return true; }
                    return /^[a-zA-Z0-9áéíóúñÑ ]+$/.test(viewValue);
                };
            }
        };
    }

    isDni.$inject = [];
    function isDni()
    {
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function (scope, element, attributes, control) {
                control.$validators.isDni = function (modelValue, viewValue) {
                    if(control.$isEmpty(viewValue) && !attributes.required) { return true; }
                    return /^[0-9]{8}$/.test(viewValue);
                };
            }
        };
    }

    isRuc.$inject = [];
    function isRuc()
    {
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function (scope, element, attributes, control) {
                control.$validators.isRuc = function (modelValue, viewValue) {
                    if(control.$isEmpty(viewValue) && !attributes.required) { return true; }
                    return /^[0-9]{11}$/.test(viewValue);
                };
            }
        };
    }

    isEqualTo.$inject = [];
    function isEqualTo()
    {
        return {
            restrict: 'A', 
            require: 'ngModel', 
            scope: {
                isEqualTo: '='
            }, 
            link: function(scope, element, attributes, control)
            {
                var valueCache = null;

                scope.$watch('isEqualTo', function(newValue, oldValue){
                    valueCache = newValue;
                    validate(control.$viewValue);
                });
                
                var validate = function (value) {
                    control.$setValidity("isEqualTo", value === valueCache);
                    return value === valueCache ? value : undefined;
                };
                
                control.$parsers.unshift(validate);
            }
        };
    }

    stDateSearch.$inject = [];
    function stDateSearch()
    {
        var directive = {
            require: {
                table: '^stTable', 
                model: 'ngModel'
            },
            link: link,
            restrict: "A", 
            scope: {
                model: '=ngModel'
            }
        };

        return directive;

        function link(scope, element, attr, ctrl)
        {
            var tableCtrl = ctrl.table;

            scope.$watch('model', watchModel, true);

            function watchModel(newValue, oldValue)
            {
                if(newValue === oldValue) { return; }
                tableCtrl.search(newValue, attr.stDateSearch);
            }
        }
    }

    goBack.$inject = ['$window', 'Dialog'];
    function goBack($window, Dialog)
    {
        return {
            restrict: 'A', 
            scope: { confirm: '@?goBack' },
            link: function(scope, element)
            {
                element.on("click", function()
                {
                    if(scope.confirm == "confirm")
                    {
                        Dialog.wconfirm('Se volverá atras, ¿Desea continuar?', function() { $window.history.back(); });
                    }
                    else { $window.history.back(); }
                });
            }
        };
    }
    
})();

