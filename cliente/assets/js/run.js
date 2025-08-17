(function(){
	"use strict";
	
	angular.module('app').run(run);
	run.$inject = ['Helper', '$rootScope', '$http', '$state', '$stateParams', 'Auth', 'bootstrap3ElementModifier', 'defaultErrorMessageResolver', 'hotkeys'];
	
	function run(Helper, $rootScope, $http, $state, $stateParams, Auth, bootstrap3ElementModifier, defaultErrorMessageResolver, hotkeys)
	{
		/* Router */
		$rootScope.$state = $state;
		$rootScope.$stateParams = $stateParams;

		if(Auth.existsTokenInLocalStorage()) { Auth.updateMetadata(); }

		$rootScope.$on('$stateChangeStart', function(event, toState, toParams){

			if(toState.name == 'login' && Auth.existsTokenInLocalStorage())
			{
				event.preventDefault();
				Helper.goToCorrectStage();
			}
			if(toState.authenticate && !Auth.existsTokenInLocalStorage())
			{
				event.preventDefault();
				$state.go('login');
			}
		});

		/* Validation */
		bootstrap3ElementModifier.enableValidationStateIcons(true);
		defaultErrorMessageResolver.setI18nFileRootPath('assets/node_modules/angular-auto-validate/dist/lang');
		defaultErrorMessageResolver.setCulture('es-co');

		defaultErrorMessageResolver.getErrorMessages().then(function (errorMessages) {
			errorMessages['minlength'] = 'El valor ingresado de tener más de {0} caracteres';
			errorMessages['maxlength'] = 'El valor ingresado de tener menos de {0} caracteres';
			errorMessages['isWord'] = 'Solo está permitido letras';
			errorMessages['isWords'] = 'Solo está permitido letras y espacios';
			errorMessages['isAlphanumeric'] = 'Solo está permitido números y letras';
			errorMessages['isAlphanumericAndSpaces'] = 'Solo está permitido números, letras y espacios';
			errorMessages['isDni'] = 'Ingrese un DNI';
			errorMessages['isRuc'] = 'Ingrese un RUC';
			errorMessages['min'] = 'Debe ingresar un valor mayor a {0}';
			errorMessages['max'] = 'Debe ingresar un valor menor a {0}';
			errorMessages['isEqualTo'] = 'No cumple las condiciones necesarias';
			errorMessages['step'] = 'Debe ingresar un número múltiplo de: {0}';
			//errorMessages['anotherErrorMessage'] = 'An error message with the attribute value {0}';
		});

		/* DatePicker */
		$rootScope.daterangepicker = {};
		$rootScope.daterangepicker.opts = {};

		$rootScope.daterangepicker.opts.single = {
			singleDatePicker: true, // Activa el modo de selección única
			showDropdowns: true, 
			locale: { format: "DD/MM/YYYY", }
		}

		$rootScope.daterangepicker.opts.range = 
		{
            startDate: moment().subtract(3, 'days'), 
			endDate: moment(), 
			showDropdowns: true, 
            locale: 
			{
                applyLabel: "Aceptar",
                format: "DD/MM/YYYY",
                cancelLabel: 'Cancelar',
                customRangeLabel: 'Rango personalizado'
            },
            ranges: 
			{
                'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				'Hoy': [moment(), moment()],
				'Semana actual': [moment().startOf('week'), moment()], 
				'Semana anterior': [moment().subtract(1, 'week').startOf('week'), moment().subtract(1, 'week').endOf('week')], 
				'Mes actual': [moment().startOf('month'), moment()], 
				'Mes anterior': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')], 
				'Últimos 7 días': [moment().subtract(7, 'days'), moment()],
                'Últimos 30 días': [moment().subtract(30, 'days'), moment()], 
			}
        };

		/* StTable */
		$rootScope.itemsByPage = 100;
		$rootScope.displayedPages = 7;

		/* Hotkey */
		hotkeys.del('?');

		/* Functions */
		$rootScope.isLoading = function() { return $http.pendingRequests.length > 0; };
		$rootScope.resetDateRange = function(vm, index) { vm[index] = { startDate: null, endDate: null }; }
		$rootScope.getTotal = function(items, key)
		{
			var total = 0;

            if(items && items.length > 0)
            {
                angular.forEach(items, function(item) { total += item[key]; });
            }

            return total;
		}
	}
})();
