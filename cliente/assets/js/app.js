(function(){
	"use strict";
	
	angular.module('app', ['ui.router', 'ui.router.state.events', 'ngResource', 'ngStorage', 'ngResource', 'ui.bootstrap', 'jcs-autoValidate', 'ngAnimate', 'smart-table', 'ui.select', 'daterangepicker', 'cfp.hotkeys']);

	angular.module('app').config(config);
	config.$inject = ['$urlRouterProvider', '$stateProvider', '$uibModalProvider'];
	
	function config($urlRouterProvider, $stateProvider, $uibModalProvider)
	{
		var global_config =  { controllerAs: 'vm', authenticate: true };

		$stateProvider
		.state('login', {
			url: '/login',
			title: 'Bienvenido', 
			templateUrl: 'login.html', 
			controller: 'LoginController',
			controllerAs: 'vm', 
			authenticate: false
		})
		.state('admin', {
			templateUrl: 'admin.html',
			abstract: true
		})
		.state('admin.cuenta_list', {
			url: '/cuenta/lista',
			title: 'Lista de cuentas',
			templateUrl: 'partials/cuenta/list.html',
			controller: 'CuentaListController',
			... global_config
		})
		.state('admin.usuario_list', {
			url: '/usuario/lista',
			title: 'Lista de usuarios',
			templateUrl: 'partials/usuario/list.html',
			controller: 'UsuarioListController',
			... global_config
		})
		.state('admin.usuario_form', {
			url: '/usuario/form/:id?',
			params: {id: null}, 
			title: 'Formulario usuarios',
			templateUrl: 'partials/usuario/form.html',
			controller: 'UsuarioFormController',
			... global_config
		})
		.state('admin.terminal_list', {
			url: '/terminal/lista',
			title: 'Lista de terminales',
			templateUrl: 'partials/terminal/list.html',
			controller: 'TerminalListController',
			... global_config
		})
		.state('admin.caja_list', {
			url: '/caja/lista',
			title: 'Lista de cajas',
			templateUrl: 'partials/caja/list.html',
			controller: 'CajaListController',
			... global_config
		})
		.state('admin.movimiento_list', {
			url: '/movimiento/lista',
			title: 'Lista de movimientos',
			templateUrl: 'partials/movimiento/list.html',
			controller: 'MovimientoListController',
			... global_config
		});

		$urlRouterProvider.otherwise(function($injector){
			var Helper = $injector.get('Helper');
			Helper.goToCorrectStage();
		});

		/* Modal */
		$uibModalProvider.options.size = 'lg';
		$uibModalProvider.options.backdrop = 'static';
		/* End Modal */
	};
})();
