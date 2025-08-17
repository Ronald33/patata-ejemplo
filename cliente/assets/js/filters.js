(function(){
    "use strict";

    angular.module('app').filter('capitalize', capitalize);
    angular.module('app').filter('myDate', myDate);
    angular.module('app').filter('myTime', myTime);
    angular.module('app').filter('myDateTime', myDateTime);
    angular.module('app').filter('myAmount', myAmount);
    angular.module('app').filter('dni', dni);
    angular.module('app').filter('persona', persona);
    angular.module('app').filter('personaWithDocument', personaWithDocument);
    angular.module('app').filter('myFilter', myFilter);

    capitalize.$inject = []
    function capitalize()
    {
        return function(input)
        {
            return (angular.isString(input) && input.length > 0) ? input.charAt(0).toUpperCase() + input.substr(1).toLowerCase() : input;
        };
    }

    myDate.$inject = ['$filter']
    function myDate($filter)
    {
        return function(input)
        {
            return $filter('date')(input, "dd/MM/yyyy" , '-0500');
        };
    }

    myTime.$inject = ['$filter']
    function myTime($filter)
    {
        return function(input)
        {
            return $filter('date')(input, "h:mm a" , '-0500');
        };
    }

    myDateTime.$inject = ['$filter']
    function myDateTime($filter)
    {
        return function(input)
        {
            if(input === null || input === undefined) { return ''; }
            return $filter('date')(input * 1000, "dd/MM/yyyy - h:mm a", '-0500');
        };
    }

    myAmount.$inject = ['$filter']
    function myAmount($filter)
    {
        return function(input)
        {
            if(input === null || input === undefined) { return ''; }
            return $filter('number')(input / 100, 2);
        };
    }

    dni.$inject = []
    function dni()
    {
        return function(input)
        {
            return String(input).padStart(8, "0");
        };
    }

    persona.$inject = []
    function persona()
    {
        return function(input)
        {
            if(!input) { return input; }
            return input.nombres + ' ' + input.apellidos;
        };
    }

    personaWithDocument.$inject = []
    function personaWithDocument()
    {
        return function(input)
        {
            if(!input) { return input; }
            var result = input.nombres + ' ' + input.apellidos;
            if(input.documento) { result += ' (' + input.documento + ')'; }
            return result;
        };
    }

    myFilter.$inject = ['$filter'];
    function myFilter($filter)
    {
        return response;

        function standardComparator(obj, text)
        {
            text = ('' + text).toLowerCase();
            return ('' + obj).toLowerCase().indexOf(text) > -1;
        };

        function response(input, predicate)
        {
            return $filter('filter')(input, predicate, customComparator);
        }

        function customComparator(actual, expected)
        {
            if(angular.isObject(expected))
            {
                if(expected.hasOwnProperty('startDate') && expected.hasOwnProperty('endDate'))
                {
                    if(expected.startDate == null || expected.endDate == null) { return true; }
                    else if(actual >= moment(expected.startDate).unix() && actual <= moment(expected.endDate).unix()) { return true; }
                    return false;
                }
            }

            if(angular.isObject(actual))
            {
                if(actual.hasOwnProperty('nombres') && actual.hasOwnProperty('apellidos'))
                {
                    const text = ('' + expected).toLowerCase();
                    const nombres = ('' + actual.nombres).toLowerCase();
                    const apellidos = ('' + actual.apellidos).toLowerCase();

                    return nombres.indexOf(text) > -1 || apellidos.indexOf(text) > -1;
                }
            }

            return standardComparator(actual, expected);
        }
    }
})();