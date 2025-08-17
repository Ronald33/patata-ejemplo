(function(){
	"use strict";

	angular.module('app').factory('GlobalInterceptor', globalInterceptor);

	globalInterceptor.$inject = ['$q', 'Dialog'];
	function globalInterceptor($q, Dialog)
	{
		var service = {
			'responseError': responseError
		};

		return service;

		function responseError(response)
		{
			switch(response.status)
			{
				// case 409: showError(response.data); break;
				//default: alert('Ocurrió un error');
			};
			return $q.reject(response);

			function showError(message) { Dialog.ealert(message); }
		}
	}
})();

(function(){
	angular.module('app').config(config);

	config.$inject = ['$httpProvider'];
	function config($httpProvider)
	{
		$httpProvider.interceptors.push('GlobalInterceptor'); 
	}
})();